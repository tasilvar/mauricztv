<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter\infrastructure\io;

use bpmj\wpidea\modules\active_sessions_limiter\core\io\Interface_Sessions_Manager;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use WP_Session_Tokens;
use bpmj\wpidea\user\User_ID;

class Sessions_Manager implements Interface_Sessions_Manager
{
    private int $user_id;

    private function __construct(
        int $user_id
    )
    {
        $this->user_id = $user_id;
    }

    public static function create_for_user(int $user_id): self
    {
        return new self($user_id);
    }

    public function get_active_sessions_count(): int
    {
        $manager = WP_Session_Tokens::get_instance($this->get_managed_user_id());

        return count( $manager->get_all() );
    }

    public function destroy_all_sessions(): void
    {
        $manager = WP_Session_Tokens::get_instance($this->get_managed_user_id());

        $manager->destroy_all();
    }

    public function get_managed_user_id(): int
    {
        return $this->user_id;
    }
}