<?php

namespace bpmj\wpidea\modules\google_analytics\core\services;

use bpmj\wpidea\user\api\Interface_User_API;

class Hash_User_ID_Generator
{
    private const SUFFIX = '_ga4';
    private Interface_User_API $user_api;

    public function __construct(
        Interface_User_API $user_api
    ) {
        $this->user_api = $user_api;
    }

    public function get_current_user_id_hash(): ?string
    {
        $current_user_id = $this->user_api->get_current_user_id();

        if (!$current_user_id) {
            return null;
        }

        return $this->generate_hash_id($current_user_id->to_int());
    }

    private function generate_hash_id(int $id): string
    {
        return md5($id . self::SUFFIX);
    }
}
