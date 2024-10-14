<?php

namespace bpmj\wpidea\templates_system\templates;

use bpmj\wpidea\admin\helpers\utils\Snackbar;
use bpmj\wpidea\Caps;
use bpmj\wpidea\templates_system\Default_Templates_Creator;
use bpmj\wpidea\wolverine\user\User;

class Template_Actions_Handler
{
    public const QUERY_PARAM_NONCE = 'nonce';
    public const QUERY_PARAM_RESTORE_TEMPLATE = 'restore_template';

    /**
     * @var Default_Templates_Creator
     */
    private $default_templates_creator;

    public function __construct(Default_Templates_Creator $default_templates_creator)
    {
        $this->default_templates_creator = $default_templates_creator;
    }

    public function handle(): void
    {
        add_action('admin_init', [$this, 'watch_for_query_params']);
    }

    public function watch_for_query_params(): void
    {
        if(!User::currentUserHasAnyOfTheRoles([
            Caps::ROLE_SITE_ADMIN,
            Caps::ROLE_LMS_SUPPORT,
            Caps::ROLE_LMS_ADMIN
        ])) {
            return;
        }

        if(!empty($_GET[self::QUERY_PARAM_RESTORE_TEMPLATE])) {
            if (!wp_verify_nonce($_GET[self::QUERY_PARAM_NONCE] ?? null, self::QUERY_PARAM_RESTORE_TEMPLATE)) {
                WPI()->snackbar->display_message_on_next_request(__('The token looks invalid, please try again.', BPMJ_EDDCM_DOMAIN), Snackbar::TYPE_ERROR);

                wp_safe_redirect(remove_query_arg(self::QUERY_PARAM_RESTORE_TEMPLATE));
                exit;
            }

            $this->restore_template($_GET[self::QUERY_PARAM_RESTORE_TEMPLATE]);
        }
    }

    private function restore_template($template_id): void
    {
        $template = Template::find($template_id);

        if($template) {
            $template->delete();

            $this->default_templates_creator->create();

            WPI()->snackbar->display_message_on_next_request(__('Template successfully restored!', BPMJ_EDDCM_DOMAIN));
        }

        wp_send_json_success([
            'action' => 'redirect',
            'url' => remove_query_arg(self::QUERY_PARAM_RESTORE_TEMPLATE, admin_url('admin.php?page=wp-idea-templates')),
        ]);
    }
}