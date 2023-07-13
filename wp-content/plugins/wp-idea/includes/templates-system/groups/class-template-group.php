<?php

namespace bpmj\wpidea\templates_system\groups;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\pages\Page;
use bpmj\wpidea\templates_system\admin\renderers\Legacy_Templates_List_Renderer;
use bpmj\wpidea\templates_system\Experimental_Cart_View_Handler;
use bpmj\wpidea\templates_system\groups\helpers\Active_Template_Group_Handler;
use bpmj\wpidea\templates_system\groups\settings\Template_Group_Settings;
use bpmj\wpidea\templates_system\groups\settings\Template_Group_Settings_Fields;
use bpmj\wpidea\templates_system\templates\classic\Cart_Template as Cart_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Course_Lesson_Template as Course_Lesson_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Course_Module_Template as Course_Module_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Course_Offer_Page_Template as Course_Offer_Page_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Course_Panel_Template as Course_Panel_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Courses_Page_Template as Courses_Page_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\User_Account_Template as User_Account_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Category_Page_Template as Category_Page_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Tag_Page_Template as Tag_Page_Template_Classic;
use bpmj\wpidea\templates_system\templates\scarlet\Cart_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Lesson_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Module_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Offer_Page_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Panel_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Quiz_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Courses_Page_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Experimental_Cart_Template;
use bpmj\wpidea\templates_system\templates\scarlet\User_Account_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Category_Page_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Tag_Page_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Search_Page_Template;

class Template_Group
{
    public const BASE_TEMPLATE_SCARLET = LMS_Settings::TEMPLATE_SCARLET;
    public const BASE_TEMPLATE_CLASSIC = LMS_Settings::TEMPLATE_CLASSIC;
    public const BASE_TEMPLATES = [
        self::BASE_TEMPLATE_CLASSIC,
        self::BASE_TEMPLATE_SCARLET
    ];

    private $id;

    private $is_preinstalled;

    private $name;

    private $base_template;

    private function __construct(string $name, string $base_template, bool $is_preinstalled = false)
    {
        $this->id = Template_Group_Id::create();
        $this->name = $name;
        $this->base_template = $base_template;
        $this->is_preinstalled = $is_preinstalled;
    }


    public static function find(Template_Group_Id $id): ?Template_Group
    {
        /** @var Template_Groups_Repository $repository */
        $repository = WPI()->container->get(Template_Groups_Repository::class);

        return $repository->find($id);
    }

    public static function find_all(): Groups_Collection
    {
        /** @var Template_Groups_Repository $repository */
        $repository = WPI()->container->get(Template_Groups_Repository::class);

        return $repository->find_all();
    }

    public static function create_preinstalled_for_template(string $name, string $base_template_name): Template_Group
    {
        /** @var Template_Groups_Repository $repository */
        $repository = WPI()->container->get(Template_Groups_Repository::class);

        $group = new self($name, $base_template_name, true);

        $repository->add($group);

        return $group;
    }

    public static function find_preinstalled_for_template(string $base_template_name): ?Template_Group
    {
        /** @var Template_Groups_Repository $repository */
        $repository = WPI()->container->get(Template_Groups_Repository::class);

        $matches = array_filter(
            $repository->find_all()->getArrayCopy(),
            static function($group) use ($base_template_name) {
                return $group->is_preinstalled() && $group->get_base_template() === $base_template_name;
            }
        );

        return !empty($matches) ? reset($matches) : null;
    }

    public static function get_active_group(): ?Template_Group
    {
        $group_id = self::get_active_group_id();

        if ($group_id === null) {
            return null;
        }

        return self::find($group_id);
    }

    private static function get_active_group_id(): ?Template_Group_Id
    {
        /** @var Active_Template_Group_Handler $handler */
        $handler = WPI()->container->get(Active_Template_Group_Handler::class);

        return $handler->get_active_group_id();
    }

    public function get_id(): Template_Group_Id
    {
        if(is_string($this->id)) {
            return Template_Group_Id::from_string($this->id);
        }

        return $this->id;
    }

    public function is_preinstalled(): bool
    {
        return $this->is_preinstalled;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_base_template(): string
    {
        return $this->base_template;
    }

    public function set_as_active(): void
    {
        /** @var Active_Template_Group_Handler $handler */
        $handler = WPI()->container->get(Active_Template_Group_Handler::class);

        $handler->set_active_group($this);

        $this->get_settings()->trigger(Template_Group_Settings::EVENT_GROUP_SETTINGS_CHANGED);
    }

    public function is_active(): bool
    {
        /** @var Active_Template_Group_Handler $handler */
        $handler = WPI()->container->get(Active_Template_Group_Handler::class);

        return $handler->is_group_active($this);
    }

    public function get_edit_url(): string
    {
        return add_query_arg([
            'page' => Admin_Menu_Item_Slug::TEMPLATES_LIST,
            Legacy_Templates_List_Renderer::GROUP_ID_QUERY_PARAM => $this->get_id()->stringify()
        ], admin_url('admin.php'));
    }

    public function get_settings(): Template_Group_Settings
    {
        return Template_Group_Settings::for_group($this);
    }

    public function get_options(): Template_Group_Settings_Fields
    {
        return $this->get_settings()->get_all();
    }

    public function set_option(string $option_name, string $value): self
    {
        $this->get_settings()
            ->set($option_name, $value)
            ->save();

        return $this;
    }

    public function get_option(string $option_name): ?string
    {
        $settings_field = $this->get_settings()->get($option_name);

        return $settings_field ? $settings_field->get_value() : null;
    }

    public function update_settings(?array $request_data): bool
    {
        if (!is_array($request_data)) {
            return false;
        }

        return $this->get_settings()
            ->update_from_array($request_data)
            ->save();
    }

    public function get_template_class_for_page(string $page): ?string
    {
        switch ($page) {
            case Page::COURSES:
                return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC
                    ? Courses_Page_Template_Classic::class
                    : Courses_Page_Template::class;
            case Page::CART:
                return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC
                    ? Cart_Template_Classic::class
                    : Cart_Template::class;
            case Page::CART_EXPERIMENTAL:
                return Experimental_Cart_Template::class;
            case Page::COURSE_OFFER:
                return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC
                    ? Course_Offer_Page_Template_Classic::class
                    : Course_Offer_Page_Template::class;
            case Page::COURSE_PANEL:
                return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC
                    ? Course_Panel_Template_Classic::class
                    : Course_Panel_Template::class;
            case Page::COURSE_MODULE:
                return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC
                    ? Course_Module_Template_Classic::class
                    : Course_Module_Template::class;
            case Page::COURSE_LESSON:
                return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC
                    ? Course_Lesson_Template_Classic::class
                    : Course_Lesson_Template::class;
            case Page::USER_ACCOUNT:
                return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC
                    ? User_Account_Template_Classic::class
                    : User_Account_Template::class;
            case Page::QUIZ:
                return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC
                    ? null //not supported in classic
                    : Course_Quiz_Template::class;
            case Page::CATEGORY:
                return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC
                    ? Category_Page_Template_Classic::class
                    : Category_Page_Template::class;
            case Page::TAG:
                return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC
                    ? Tag_Page_Template_Classic::class
                    : Tag_Page_Template::class;
            case Page::SEARCH_RESULTS:
                return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC
                    ? null //not supported in classic
                    : Search_Page_Template::class;
            default:
                return null;
        }
    }

    public function get_activation_url(): string
    {
        return add_query_arg([
            Active_Template_Group_Handler::QUERY_PARAM_ACTIVATE => $this->get_id()->stringify(),
            Active_Template_Group_Handler::QUERY_PARAM_NONCE => wp_create_nonce(Active_Template_Group_Handler::QUERY_PARAM_ACTIVATE)
        ]);
    }

    public function get_color_settings_url(): string
    {
        if($this->get_base_template() === self::BASE_TEMPLATE_CLASSIC) {
            return add_query_arg([
                'page' => 'wp-idea-settings',
                'autofocus' => 'courses_layout'
            ], admin_url('admin.php'));
        }

        return add_query_arg([
            'autofocus[section]' => 'bpmj_eddcm_colors_settings'
        ], admin_url('customize.php'));
    }

    public function supports_legacy_color_settings(): bool
    {
        return $this->get_base_template() === self::BASE_TEMPLATE_CLASSIC;
    }

    public static function no_active_group_present(): bool
    {
        return self::get_active_group_id() === null;
    }
}