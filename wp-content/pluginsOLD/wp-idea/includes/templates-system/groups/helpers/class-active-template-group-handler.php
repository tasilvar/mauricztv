<?php

namespace bpmj\wpidea\templates_system\groups\helpers;

use bpmj\wpidea\admin\helpers\utils\Snackbar;
use bpmj\wpidea\Caps;
use bpmj\wpidea\templates_system\groups\settings\Template_Group_Settings;
use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\groups\Template_Group_Id;
use bpmj\wpidea\wolverine\settings\Settings;
use bpmj\wpidea\wolverine\user\User;

class Active_Template_Group_Handler
{
    public const QUERY_PARAM_ACTIVATE = 'bpmj_activate_template_group';

    private const ACTIVE_GROUP_OPTION = 'bpmj_active_template_group';
    private const QUERY_PARAM_GROUP_ACTIVATED = 'bpmj_template_group_activated';
    public const QUERY_PARAM_NONCE = 'nonce';

    private $active_group_id;

    public function __construct()
    {
        add_action('admin_init', [$this, 'activate_group_if_query_param_present']);
        add_action('admin_init', [$this, 'trigger_events_if_query_param_present']);
    }

    public function set_active_group(Template_Group $group): void
    {
        update_option(self::ACTIVE_GROUP_OPTION, $group->get_id()->stringify());

        $this->active_group_id = $group->get_id();

        $this->change_legacy_template_option($group->get_base_template());
    }

    public function set_active_group_by_id(Template_Group_Id $group_id): void
    {
        $group = Template_Group::find($group_id);

        if(!$group) return;

        $group->set_as_active();
    }

    public function is_group_active(Template_Group $group): bool
    {
        $active_group_id = $this->get_active_group_id();

        if ($active_group_id === null) {
            return false;
        }

        return $active_group_id->equals($group->get_id());
    }

    public function get_active_group_id(): ?Template_Group_Id
    {
        if (isset($this->active_group_id)) {
            return $this->active_group_id;
        }

        $active_group_id = get_option(self::ACTIVE_GROUP_OPTION, null);

        $this->active_group_id = $active_group_id !== null ? Template_Group_Id::from_string($active_group_id) : null;

        return $this->active_group_id;
    }

    private function change_legacy_template_option(string $base_template): void
    {
        Settings::setTemplate($base_template);
    }

    public function activate_group_if_query_param_present(): void
    {
        $group_id = $_GET[self::QUERY_PARAM_ACTIVATE] ?? null;

        if(!$group_id) return;

        if(!User::currentUserHasAnyOfTheRoles([
            Caps::ROLE_SITE_ADMIN,
            Caps::ROLE_LMS_SUPPORT,
            Caps::ROLE_LMS_ADMIN
        ])) {
            return;
        }

        $this->verify_activate_group_nonce();

        $this->set_active_group_by_id(Template_Group_Id::from_string($group_id));

        $this->do_after_group_activation_redirect();
    }

    private function do_after_group_activation_redirect(): void
    {
        $redirection_url = remove_query_arg(self::QUERY_PARAM_ACTIVATE);
        $redirection_url = remove_query_arg(self::QUERY_PARAM_GROUP_ACTIVATED, $redirection_url);
        $redirection_url = $this->add_param_for_additional_redirect_to_force_settings_reload($redirection_url);

        $this->redirect_and_exit($redirection_url);
    }

    public function trigger_events_if_query_param_present(): void
    {
        $group_activated = $_GET[self::QUERY_PARAM_GROUP_ACTIVATED] ?? null;

        if (!$group_activated) {
            return;
        }

        $this->verify_activate_group_nonce();

        $this->trigger_template_group_settings_changed_event();

        WPI()->snackbar->display_message_on_next_request(__('Template group activated!', BPMJ_EDDCM_DOMAIN));

        $this->redirect_and_exit(remove_query_arg([self::QUERY_PARAM_GROUP_ACTIVATED, self::QUERY_PARAM_NONCE]));
    }

    private function trigger_template_group_settings_changed_event(): void
    {
        Template_Group::get_active_group()->get_settings()->trigger(Template_Group_Settings::EVENT_GROUP_SETTINGS_CHANGED);
    }

    private function add_param_for_additional_redirect_to_force_settings_reload(string $redirection_url): string
    {
        return add_query_arg([self::QUERY_PARAM_GROUP_ACTIVATED => true], $redirection_url);
    }

    private function redirect_and_exit(string $redirection_url): void
    {
        wp_safe_redirect($redirection_url);
        exit;
    }

    private function verify_activate_group_nonce(): void
    {
        $nonce_okay = wp_verify_nonce($_GET[self::QUERY_PARAM_NONCE] ?? null, self::QUERY_PARAM_ACTIVATE);

        if ($nonce_okay) {
            return;
        }

        WPI()->snackbar->display_message_on_next_request(__('The token looks invalid, please try again.'), Snackbar::TYPE_ERROR);

        wp_safe_redirect(remove_query_arg([self::QUERY_PARAM_GROUP_ACTIVATED,self::QUERY_PARAM_ACTIVATE, self::QUERY_PARAM_NONCE]));
        exit;
    }
}
