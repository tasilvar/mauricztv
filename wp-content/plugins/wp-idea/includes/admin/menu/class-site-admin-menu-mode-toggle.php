<?php namespace bpmj\wpidea\admin\menu;

use bpmj\wpidea\Caps;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_User_Metadata_Service;

class Site_Admin_Menu_Mode_Toggle implements Interface_Initiable
{

    public const IDEA_MENU_MODE_ENABLED_META_KEY = 'wp-idea-menu-mode-enabled';
    private const ADMIN_INIT_HOOK = 'admin_init';
    private const ADMIN_MENU_HOOK = 'admin_menu';
    private const POSITION_ON_TOP_OF_MENU = 0;
    private const POSITION_IN_THE_MIDDLE_OF_MENU = 30;

    private $user_metadata_service;

    private $translator;

    private $actions;

    private $current_request;

    private $redirector;

    private $url_generator;

    public function __construct(
        Interface_User_Metadata_Service $user_metadata_service,
        Interface_Translator $translator,
        Interface_Actions $actions,
        Current_Request $current_request,
        Interface_Redirector $redirector,
        Interface_Url_Generator $url_generator
    ) {
        $this->user_metadata_service = $user_metadata_service;
        $this->translator            = $translator;
        $this->actions               = $actions;
        $this->current_request       = $current_request;
        $this->redirector            = $redirector;
        $this->url_generator         = $url_generator;
    }

    public function init(): void
    {
        $this->actions->add(self::ADMIN_MENU_HOOK, [$this, 'redirect_to_dashboard_when_already_in_correct_mode']);
        $this->actions->add(self::ADMIN_INIT_HOOK, function() {
            if ($this->current_request->get_query_arg('page') === Admin_Menu_Item_Slug::SWITCH_TO_WP_ADMIN) {
                $this->user_metadata_service->store_for_current_user(self::IDEA_MENU_MODE_ENABLED_META_KEY, false);
                $this->redirector->redirect(get_dashboard_url());
            }
        });
        $this->actions->add(self::ADMIN_INIT_HOOK, function() {
            if ($this->current_request->get_query_arg('page') === Admin_Menu_Item_Slug::SWITCH_TO_LMS_ADMIN) {
                $this->user_metadata_service->store_for_current_user(self::IDEA_MENU_MODE_ENABLED_META_KEY, true);
                $this->redirector->redirect($this->url_generator->get_dashboard_url());
            }
        });
    }

    public function idea_mode_is_enabled_by_user(): bool
    {
        return (bool)$this->user_metadata_service->get_for_current_user(self::IDEA_MENU_MODE_ENABLED_META_KEY);
    }

    public function get_toggle_button_add_menu_page_options(): array
    {
        $idea_mode_on = $this->idea_mode_is_enabled_by_user();
        $title = $idea_mode_on ?
            $this->translator->translate("admin_menu_mode.switch_to_wp_menu") :
            $this->translator->translate("admin_menu_mode.switch_to_lms_menu");
        $icon = $idea_mode_on ? "dashicons-wordpress" : "dashicons-lightbulb";
        $position = $idea_mode_on ? self::POSITION_ON_TOP_OF_MENU : self::POSITION_IN_THE_MIDDLE_OF_MENU;
        $slug = $idea_mode_on ?  Admin_Menu_Item_Slug::SWITCH_TO_WP_ADMIN : Admin_Menu_Item_Slug::SWITCH_TO_LMS_ADMIN;

        return [
            $title,
            $title,
            Caps::ROLE_SITE_ADMIN,
            $slug,
            function () {},
            $icon,
            $position
        ];
    }

    public function redirect_to_dashboard_when_already_in_correct_mode(): void
    {
        global $menu;
        $slug = $this->current_request->get_query_arg('page');

        if (!in_array($slug, [Admin_Menu_Item_Slug::SWITCH_TO_LMS_ADMIN, Admin_Menu_Item_Slug::SWITCH_TO_WP_ADMIN])) {
            return;
        }
        foreach ($menu as $page) {
            if ($page[2] === $slug) {
                return;
            }
        }
        $this->redirector->redirect($this->url_generator->get_dashboard_url());
    }
}