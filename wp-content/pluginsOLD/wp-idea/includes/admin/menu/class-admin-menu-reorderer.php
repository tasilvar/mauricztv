<?php

namespace bpmj\wpidea\admin\menu;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\Caps;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\modules\videos\Videos_Module;
use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\options\Options_Const;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\User_Capability_Factory;
use bpmj\wpidea\user\User_Role_Factory;
use bpmj\wpidea\wolverine\user\User;
use Exception;

class Admin_Menu_Reorderer implements Interface_Initiable
{
    public const MENU_REORDER_HOOK = 'parent_file';

    private const PUBLIGO_MODE_CSS_CLASS = 'publigo-mode';

    private bool $should_menu_be_reordered_for_current_user;

    protected Admin_Menu_Items $items;

    private Interface_Actions $actions;

    private Site_Admin_Menu_Mode_Toggle $admin_menu_mode_toggle;

    private Interface_Current_User_Getter $current_user_getter;

    private Interface_User_Permissions_Service $user_permissions_service;

    private User_Capability_Factory $capability_factory;

    private Interface_Options $options;

    private Interface_Settings $settings;

    private Interface_Translator $translator;

    private Videos_Module $videos_module;

    private Interface_Filters $filters;

    public function __construct(
        Interface_Actions $actions,
        Site_Admin_Menu_Mode_Toggle $admin_menu_mode_toggle,
        Interface_Current_User_Getter $current_user_getter,
        Interface_User_Permissions_Service $user_permissions_service,
        Interface_Options $options,
        User_Capability_Factory $capability_factory,
        User_Role_Factory $user_role_factory,
        Interface_Settings $settings,
        Interface_Translator $translator,
        Videos_Module $videos_module,
        Interface_Filters $filters
    ) {
        $this->actions = $actions;
        $this->capability_factory = $capability_factory;
        $this->admin_menu_mode_toggle = $admin_menu_mode_toggle;
        $this->current_user_getter = $current_user_getter;
        $this->user_permissions_service = $user_permissions_service;
        $this->options = $options;
        $this->settings = $settings;
        $this->translator = $translator;
        $this->videos_module = $videos_module;
        $this->filters = $filters;
    }

    public function init(): void
    {
        if (empty($this->options->get(Options_Const::WPI_VALIDATED_KEY))) {
            return;
        }
        $this->actions->add(self::MENU_REORDER_HOOK, [$this, 'set_up']);
        $this->add_body_classes();
    }

    public function set_up()
    {
        if (!$this->should_menu_be_reordered_for_current_user()) {
            return;
        }

        $this->items = new Admin_Menu_Items();

        if (empty($this->items->get_flat_items_list())) {
            return;
        }

        $this->create_menu_structure();
        $this->replace_wp_menus();
    }

    public function should_menu_be_reordered_for_current_user(): bool
    {
        if(isset($this->should_menu_be_reordered_for_current_user)) {
            return $this->should_menu_be_reordered_for_current_user;
        }

        $current_user = $this->current_user_getter->get();

        $this->should_menu_be_reordered_for_current_user = $this->user_permissions_service->has_capability(
                $current_user,
                $this->capability_factory->create_from_name(Caps::CAP_USE_WP_IDEA_MODE)
            ) || $this->admin_menu_mode_toggle->idea_mode_is_enabled_by_user();

        return $this->should_menu_be_reordered_for_current_user;
    }

    /**
     * @throws Exception
     */
    protected function create_menu_structure()
    {
        $items = $this->items;
        $user = User::getCurrent();

        if ($this->admin_menu_mode_toggle->idea_mode_is_enabled_by_user()) {
            $items->add_item(
                $items->find_by_slug(Admin_Menu_Item_Slug::SWITCH_TO_WP_ADMIN)
            );
        }

        $items->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::DASHBOARD,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-lightbulb')->set_title(
                    __(
                        'Dashboard',
                        BPMJ_EDDCM_DOMAIN
                    )
                )
            )
        )->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::COURSES,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-welcome-learn-more')
                    ->set_title(__('Courses', BPMJ_EDDCM_DOMAIN))
            ),
            [
                $items->find_by_slug_and_transform(
                    Admin_Menu_Item_Slug::COURSES,
                    fn(Admin_Menu_Item $item) => $item->set_title(__('All Courses', BPMJ_EDDCM_DOMAIN))
                ),
                $items->find_by_slug(Admin_Menu_Item_Slug::STUDENTS)
            ]
        );

        if ($this->settings->get(Settings_Const::DIGITAL_PRODUCTS_ENABLED)) {
            $items->add_item(
                $items->find_by_slug_and_transform(
                    Admin_Menu_Item_Slug::DIGITAL_PRODUCTS,
                    fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-download')
                ),
                []
            );
        }
        if ($this->settings->get(Settings_Const::PHYSICAL_PRODUCTS_ENABLED)) {
            $items->add_item(
                $items->find_by_slug_and_transform(
                    Admin_Menu_Item_Slug::PHYSICAL_PRODUCTS,
                    fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-products')
                )
            );
        }

        if ($this->settings->get(Settings_Const::SERVICES_ENABLED)) {
            $items->add_item(
                $items->find_by_slug_and_transform(
                    Admin_Menu_Item_Slug::SERVICES,
                    fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-businessman')
                ),
                []
            );
        }

        $items->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::PACKAGES,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-grid-view')
            ),
            []
        );

        $items->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::CATEGORIES,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-category')
                    ->set_title($this->translator->translate('categories.menu_title'))
            ),
        );

        $items->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::TAGS,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-tag')
                    ->set_title($this->translator->translate('tags.menu_title'))
            ),
        );

        $items->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::QUIZZES,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-welcome-write-blog')
            )
        )->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::CERTIFICATES,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-text-page')
            )
        )->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::AFFILIATE_PROGRAM,
                fn($item) => $item->set_icon('dashicons-money-alt')
            ),
            [
                $items->find_by_slug(Admin_Menu_Item_Slug::AFFILIATE_PROGRAM_PARTNERS),
                $items->find_by_slug_and_transform(
                    Admin_Menu_Item_Slug::AFFILIATE_PROGRAM,
                    fn($item) => $item->set_title($this->translator->translate('affiliate_program.commissions'))
                ),
                $items->find_by_slug(Admin_Menu_Item_Slug::AFFILIATE_PROGRAM_REDIRECTIONS)
            ]
        )->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::PAYMENTS_HISTORY,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-cart')
                    ->set_title(__('Sales', BPMJ_EDDCM_DOMAIN))
            ),
            [
                $items->find_by_slug(Admin_Menu_Item_Slug::PAYMENTS_HISTORY),
                $items->find_by_slug(Admin_Menu_Item_Slug::DISCOUNT_CODES),
                $items->find_by_slug(Admin_Menu_Item_Slug::CLIENTS),
                $items->find_by_slug(Admin_Menu_Item_Slug::PRICE_HISTORY),
            ]
        )->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::OPINIONS,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-media-text')
            )
        )->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::INCREASING_SALES,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-chart-bar')
            )
        )->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::REPORTS,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-chart-line')
            )
        );

        if ($this->videos_module->is_enabled()) {
            $media_items = [
                $items->find_by_slug(Admin_Menu_Item_Slug::VIDEOS),
                $items->find_by_slug_and_transform(
                    Admin_Menu_Item_Slug::MEDIA,
                    fn(Admin_Menu_Item $item
                    ) => $item->set_title($this->translator->translate('media.submenu.other_media.title'))
                ),
            ];
        } else {
            $media_items = [
                $items->find_by_slug(Admin_Menu_Item_Slug::MEDIA)
            ];
        }

        $items->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::HELP,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-editor-help')
            )
        )->add_separator()
            ->add_item(
                $items->find_by_slug(Admin_Menu_Item_Slug::MEDIA),
                $media_items
            )->add_item(
                $items->find_by_slug(Admin_Menu_Item_Slug::PAGES),
                [
                    $items->find_by_slug_and_transform(
                        Admin_Menu_Item_Slug::PAGES,
                        fn(Admin_Menu_Item $item) => $item->set_title(__('All Pages'))
                    ),
                    $items->find_by_slug(Admin_Menu_Item_Slug::PAGES_ADD_NEW)
                ]
            )->add_item(
                $items->find_by_slug(Admin_Menu_Item_Slug::POSTS),
                [
                    $items->find_by_slug_and_transform(
                        Admin_Menu_Item_Slug::POSTS,
                        fn(Admin_Menu_Item $item) => $item->set_title(__('All Posts'))
                    ),
                    $items->find_by_slug(Admin_Menu_Item_Slug::POSTS_ADD_NEW),
                    $items->find_by_slug(Admin_Menu_Item_Slug::POSTS_CATEGORIES),
                    $items->find_by_slug(Admin_Menu_Item_Slug::POSTS_TAGS)
                ]
            )->add_item(
                $items->find_by_slug(Admin_Menu_Item_Slug::COMMENTS)
            );

        if ($user->hasRole(Caps::ROLE_LMS_SUPPORT)) {
            $items->add_item(
                $items->find_by_slug(Admin_Menu_Item_Slug::USER_PROFILE)
            );
        } elseif ($user->can(Caps::CAP_MANAGE_STUDENTS)) {
            $items->add_item(
                $items->find_by_slug_and_transform(Admin_Menu_Item_Slug::USERS,
                    fn(Admin_Menu_Item $item)=> $item->set_icon('dashicons-admin-users')),
                [
                    $items->find_by_slug(Admin_Menu_Item_Slug::USERS),
                    $items->find_by_slug(Admin_Menu_Item_Slug::USER_NEW),
                    $items->find_by_slug(Admin_Menu_Item_Slug::USER_PROFILE),
                ]
            );
        }

        $positions = [
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::SETTINGS,
                fn(Admin_Menu_Item $item) => $item->set_title(__('Main settings', BPMJ_EDDCM_DOMAIN))
            ),
            $items->find_by_slug(Admin_Menu_Item_Slug::TEMPLATE_GROUPS),
            $items->find_by_slug(Admin_Menu_Item_Slug::MENU)
        ];
        if ($items->find_by_slug(Admin_Menu_Item_Slug::CUSTOMIZE)) {
            $positions[] = $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::CUSTOMIZE,
                fn(Admin_Menu_Item $item) => $item->set_title(__('Appearance', BPMJ_EDDCM_DOMAIN))
            );
        }

        $items->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::SETTINGS,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-admin-generic')
            ),
            $positions
        );

        $items->add_item(
            $items->find_by_slug_and_transform(
                Admin_Menu_Item_Slug::TOOLS,
                fn(Admin_Menu_Item $item) => $item->set_icon('dashicons-admin-tools')
            ),
            [
                $items->find_by_slug(Admin_Menu_Item_Slug::TOOLS),
                $items->find_by_slug(Admin_Menu_Item_Slug::NOTIFICATIONS),
                $items->find_by_slug(Admin_Menu_Item_Slug::LOGS),
                $items->find_by_slug(Admin_Menu_Item_Slug::WEBHOOKS),
                $items->find_by_slug(Admin_Menu_Item_Slug::PURCHASE_REDIRECTIONS),
            ]
        );
    }

    protected function replace_wp_menus()
    {
        global $menu, $submenu;

        $menu = $this->items->convert_structure_to_wp_menu_array();
        $submenu = $this->items->convert_structure_to_wp_submenu_array();
    }

    private function add_body_classes(): void
    {
        $this->filters->add('admin_body_class', function (string $classes) {
            if (!$this->should_menu_be_reordered_for_current_user()) {
                return $classes;
            }

            return $classes . ' ' . self::PUBLIGO_MODE_CSS_CLASS;
        });
    }

}
