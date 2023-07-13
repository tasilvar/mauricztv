<?php namespace bpmj\wpidea\notices;

use bpmj\wpidea\user\Interface_User;
use bpmj\wpidea\user\Interface_User_Metadata_Service;

class User_Notice_Metadata_Service
{
    private const NOTICE_CLOSED_META_KEY = 'notice_closed_by_user';

    private $user_metadata_service;

    public function __construct(Interface_User_Metadata_Service $user_metadata_service)
    {
        $this->user_metadata_service = $user_metadata_service;
    }

    public function has_notice_closed(Interface_User $user): bool
    {
        return (bool)$this->user_metadata_service->get($user, self::NOTICE_CLOSED_META_KEY);
    }

    public function close_notice(Interface_User $user): void
    {
        $this->user_metadata_service->store($user, self::NOTICE_CLOSED_META_KEY, true);
    }

    public function unmark_notice_is_closed_for_all_users(): void
    {
        $this->user_metadata_service->delete_for_all_users(self::NOTICE_CLOSED_META_KEY);
    }
}