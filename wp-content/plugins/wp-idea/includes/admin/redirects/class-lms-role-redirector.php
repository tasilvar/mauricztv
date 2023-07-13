<?php

namespace bpmj\wpidea\admin\redirects;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\Caps;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\wolverine\user\User;
use bpmj\wpidea\wolverine\user\UserInterface;

class LMS_Role_Redirector implements Interface_Initiable
{
    private Interface_Redirector $redirector;

    private Interface_Actions $actions;

    private Interface_Filters $filters;

    private string $payment_history_url;
    private string $quizzes_url;

    public function __construct(
        Interface_Redirector $redirector,
        Interface_Actions $actions,
        Interface_Filters $filters,
        Interface_Url_Generator $url_generator
    ) {
        $this->redirector = $redirector;
        $this->actions = $actions;
        $this->filters = $filters;

        $this->payment_history_url = $url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::PAYMENTS_HISTORY,
        ]);

        $this->quizzes_url = $url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::QUIZZES,
        ]);
    }

    public function init(): void
    {
        $this->actions->add('wp', [$this, 'redirect_on_init']);
        $this->filters->add('login_redirect', [$this, 'redirect_after_login'], 10, 3);
    }

    public function redirect_on_init(): void
    {
        $user = User::getCurrent();

        if (is_null($user)) {
            return;
        }

        if ($this->has_user_specific_role_for_redirect($user)) {
            $this->redirector->redirect($this->get_redirect_url_for_role($user));
        }
    }

    public function redirect_after_login(string $redirect_to, string $requested_redirect_to, $user): string
    {
        if (!($user instanceof \WP_User)) {
            return $redirect_to;
        }

        $user = User::find($user->ID);

        if ($this->has_user_specific_role_for_redirect($user)) {
            return $this->get_redirect_url_for_role($user);
        }

        return $redirect_to;
    }

    private function has_user_specific_role_for_redirect(UserInterface $user): bool
    {
        /** WP_Roles */
        global $wp_roles;

        if ($user->hasRole(Caps::ROLE_LMS_ACCOUNTANT) && $wp_roles->is_role(Caps::ROLE_LMS_ACCOUNTANT)) {
            return true;
        } elseif ($user->hasRole(Caps::ROLE_LMS_ASSISTANT) && $wp_roles->is_role(Caps::ROLE_LMS_ASSISTANT)) {
            return true;
        }


        return false;
    }

    private function get_redirect_url_for_role(UserInterface $user): string
    {
        if ($user->hasRole(Caps::ROLE_LMS_ACCOUNTANT)) {
            return $this->payment_history_url;
        } elseif ($user->hasRole(Caps::ROLE_LMS_ASSISTANT)) {
            return $this->quizzes_url;
        }

        return '';
    }
}