<?php

namespace bpmj\wpidea\caps;

use bpmj\wpidea\Caps;
use bpmj\wpidea\wolverine\user\User;

class Admin_Account_Protector
{
    public function init_protection_filter(): void
    {
        $this->prevent_admin_user_editing();
    }

    private function prevent_admin_user_editing(): void
    {
        add_filter( 'editable_roles', [$this, 'filter_editable_roles']);
        add_filter( 'map_meta_cap', [$this, 'filter_meta_cap'], 10, 4);
    }

    public function filter_editable_roles($roles)
    {
        if( isset( $roles[Caps::ROLE_SITE_ADMIN] ) && !current_user_can(Caps::ROLE_SITE_ADMIN) ){
            unset( $roles[Caps::ROLE_SITE_ADMIN]);
        }

        return $roles;
    }

    public function filter_meta_cap($caps, $cap, $user_id, $args)
    {
        $caps = $this->filter_caps_to_prevent_editing_admin_user($caps, $cap, $user_id, $args);

        return $caps;
    }

    private function filter_caps_to_prevent_editing_admin_user($caps, $cap, $user_id, $args)
    {
        switch( $cap ){
            case 'promote_users':
                $is_user_edit_request = !empty($_POST['user_id']);

                // disable bulk editing roles
                if (!$is_user_edit_request) {
                    $caps[$cap] = false;
                    break;
                }

                // disable editing admin by non-admin
                $edited_user = absint($_POST['user_id']);
                if(!$this->can_user_be_edited_by_current_user($edited_user)) {
                    $caps[$cap] = false;
                }
                break;
            case 'edit_user':
            case 'remove_user':
                if (isset($args[0]) && $args[0] === $user_id) {
                    break;
                }

                if (empty($args[0])) {
                    $caps[$cap] = false;
                    break;
                }

                $edited_user = absint($args[0]);
                if(!$this->can_user_be_edited_by_current_user($edited_user)) {
                    $caps[$cap] = false;
                }
                break;
            case 'delete_user':
            case 'delete_users':
                if (empty($args[0])) {
                    break;
                }

                $edited_user = absint($args[0]);
                if (!$this->can_user_be_edited_by_current_user($edited_user)) {
                    $caps[$cap] = false;
                }

                break;
            default:
                break;
        }

        return $caps;
    }

    private function can_user_be_edited_by_current_user(int $user_id): bool
    {
        $other_user = User::find($user_id);
        if(is_null($other_user)) {
        	return false;
        }
        $other_user_is_admin = $other_user->can(Caps::ROLE_SITE_ADMIN);
        $current_user_is_admin = User::getCurrent()->can(Caps::ROLE_SITE_ADMIN);

        if ($other_user_is_admin && !$current_user_is_admin) {
            return false;
        }

        return true;
    }
}