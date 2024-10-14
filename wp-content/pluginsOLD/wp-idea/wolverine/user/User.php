<?php

namespace bpmj\wpidea\wolverine\user;

use bpmj\wpidea\Caps;
use bpmj\wpidea\Software_Variant;
use bpmj\wpidea\wolverine\Utils;
use WP_User;

class User implements UserInterface
{
    protected $repository;

    protected $userData;

    public function __construct()
    {
        $this->repository = new Repository();
        $this->userData = new UserData();
    }

    public static function findAllWithRole(string $roleName): array
    {
        $users = (new static())->repository->findUsersWithRole($roleName);

        $userModels = [];
        foreach ( $users as $user ) {
            $userModels[] = self::loadByWPUser($user);
        }

        return $userModels;
    }

    public static function getCurrentUserId()
    {
        return get_current_user_id();
    }

    public static function getCurrent(): ?UserInterface
    {
        $id = self::getCurrentUserId();

        if (empty($id)) return null;

        return User::find($id);
    }

    public function hasRole(string $role)
    {
        return user_can($this->getId(), $role);
    }

    public static function currentUserHasAnyOfTheRoles(array $roles): bool
    {
        $current_user = self::getCurrent();

        if ($current_user === null) {
            return false;
        }

        return $current_user->hasAnyOfTheRoles($roles);
    }

    public function hasAnyOfTheRoles(array $roles): bool
    {
        foreach ($roles as $key => $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function getMeta(string $key, bool $single = true)
    {
        return get_user_meta($this->userData->id, $key, $single);
    }

    public function updateMeta(string $key, $value): bool
    {
        return update_user_meta($this->userData->id, $key, $value);
    }

    public function getId()
    {
        return $this->userData->id;
    }

    public function setId($id)
    {
        $this->userData->id = $id;

        return $this;
    }

    public function getLogin()
    {
        return $this->userData->login;
    }

    public function getFirstName()
    {
        return $this->userData->first_name;
    }

    public function getLastName()
    {
        return $this->userData->last_name;
    }

    public function getEmail()
    {
        return $this->userData->email;
    }

    public function setLogin($login)
    {
        $this->userData->login = $login;

        return $this;
    }

    public static function find($id): ?User
    {
        return (new static())->load($id);
    }

    public static function findByLogin($login): ?User
    {
        $userStatic = new static();
        return $userStatic->loadByLogin($login);
    }

    public static function findByEmail($login): ?User
    {
        $userStatic = new static();
        return $userStatic->loadByEmail($login);
    }


    public static function loadByWPUser(WP_User $wpUser): User
    {
        $userStatic = new static();
        $userData = $userStatic->repository->loadByWPUser($wpUser);

        return $userStatic->setUserData($userData);
    }

    public function load($id)
    {
        $userData = $this->repository->find($id);

        return ($userData) ? $this->setUserData($userData) : null;
    }


    public function loadByLogin($login)
    {
        $userData = $this->repository->findBy('login', $login);

        return ($userData) ? $this->setUserData($userData) : null;
    }


    public function loadByEmail($email)
    {
        $userData = $this->repository->findBy('email', $email);

        return ($userData) ? $this->setUserData($userData) : null;
    }

    public function getPasswordResetLink()
    {
        return $this->repository->getUserPasswordResetLink($this->getId());
    }

    public function save()
    {
        return $this->repository->store($this->userData);
    }

    protected function setUserData(UserData $userData)
    {
        $this->userData = $userData;

        return $this;
    }

    public static function currentUserHasAnyOfTheCapabilities(array $caps): bool
    {
        return User::currentUserHasAnyOfTheRoles($caps);
    }

    public static function oneOfCurrentUserCapsIs(array $caps): ?string
    {
        $current_user = self::getCurrent();

        if ($current_user === null) {
            return null;
        }


        foreach ($caps as $key => $cap) {
            if ($current_user->can($cap)) {
                return $cap;
            }
        }

        return null;
    }


    public function canManageCourses(): bool
    {
        return $this->can(Caps::CAP_MANAGE_PRODUCTS);
    }

    public function canManageQuizes(): bool
    {
        return $this->can(Caps::CAP_MANAGE_QUIZZES);
    }

    public function canViewReports(): bool
    {
        return $this->can(Caps::CAP_VIEW_REPORTS);
    }

    public function canExportReports(): bool
    {
        return $this->can(Caps::CAP_EXPORT_REPORTS);
    }

    public function can(string $capability): bool
    {
        return user_can($this->getId(), $capability);
    }

    public function ban($banTimeInMinutes = 15)
    {
        $time = time() + ($banTimeInMinutes * Utils::SECONDS_IN_MINUTE);
        $this->userData->ban_date = $time;
        return $this->save();
    }

    public function removeBan()
    {
        $this->userData->ban_date = '';
        $this->userData->ban_forever = false;
        return $this->save();
    }

    public function getBanDate()
    {
        return $this->userData->ban_date;
    }

    public function getBanForever()
    {
        return $this->userData->ban_forever;
    }

    public function getFailedLoginCount(): int
    {
        return !empty($this->userData->failed_login_count) ? (int)$this->userData->failed_login_count : 0;
    }

    public function resetFailedLoginCount()
    {
        $this->userData->failed_login_count = 0;
        return $this->save();
    }

    public function getRemainingBanTimeInMinutes()
    {
        if ($this->isBannedForever()) {
            return __('Banned forever', BPMJ_EDDCM_DOMAIN);
        }

        $banDate = $this->userData->ban_date;

        $remainingBanTimeInSeconds = $banDate - time();
        return (int)($remainingBanTimeInSeconds / Utils::SECONDS_IN_MINUTE);
    }

    public function isBanned(): bool
    {
        if ($this->isBannedForever()) {
            return true;
        }

        $banDate = $this->getBanDate();
        $nowDate = time();

        if (empty($banDate)) {
            return false;
        }

        if ($banDate > $nowDate) {
            return true;
        }

        return false;
    }

    public function isBannedForever(): bool
    {
        return $this->getBanForever();
    }

    public function increaseFailedLoginCount()
    {
        $failedLoginCount = $this->getFailedLoginCount();

        $failedLoginCount++;

        $this->userData->failed_login_count = $failedLoginCount;
        return $this->save();
    }

    public function banForever()
    {
        $this->userData->ban_forever = true;
        return $this->save();
    }

}
