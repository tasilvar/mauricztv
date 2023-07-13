<?php

namespace bpmj\wpidea\templates_system\admin\renderers;

use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Dashicon;
use bpmj\wpidea\admin\helpers\html\Info_Box;
use bpmj\wpidea\admin\helpers\html\Interface_Renderable;
use bpmj\wpidea\admin\helpers\html\Paragraph;
use bpmj\wpidea\admin\helpers\html\Popup;
use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\renderers\Interface_Page_Renderer;
use bpmj\wpidea\admin\tables\Enhanced_Table;
use bpmj\wpidea\admin\tables\Enhanced_Table_Items_Collection;
use bpmj\wpidea\admin\helpers\html\Link;
use bpmj\wpidea\admin\tables\styles\Wpi_Style;
use bpmj\wpidea\Caps;
use bpmj\wpidea\Software_Variant;
use bpmj\wpidea\templates_system\admin\Template_Groups_Page;
use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\groups\Template_Group_Id;
use bpmj\wpidea\templates_system\groups\Template_Groups_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\User_Capability_Factory;
use bpmj\wpidea\View;
use bpmj\wpidea\wolverine\user\User;
use bpmj\wpidea\templates_system\admin\ajax\Group_Settings_Ajax_Handler;

class Template_Groups_Page_Renderer extends Abstract_Page_Renderer
{
    private const AJAX_NONCE_NAME = 'bpmj_template_groups_security_token';
    private const AJAX_PARAM_NAME_SECURITY_TOKEN = 'nonce';
    private const AJAX_PARAM_NAME_GROUP_ID = 'group_id';

    private Interface_User_Permissions_Service $user_permissions_service;

    private Interface_Current_User_Getter $current_user_getter;

    private User_Capability_Factory $capability_factory;

    private Interface_Translator $translator;

    public function __construct(
        Interface_User_Permissions_Service $user_permissions_service,
        Interface_Current_User_Getter $current_user_getter,
        User_Capability_Factory $capability_factory,
        Interface_Translator $translator
    ) {
        $this->user_permissions_service = $user_permissions_service;
        $this->current_user_getter = $current_user_getter;
        $this->capability_factory = $capability_factory;
        $this->translator = $translator;
    }

    public function get_rendered_page(): string
    {
        return View::get_admin('/templates/template-groups-page', [
            'page_title' => __('Templates', BPMJ_EDDCM_DOMAIN),
            'table' => $this->create_groups_table(),
            'info_box' => $this->create_info_box(),
            'no_active_group_warning' => $this->create_and_get_no_active_group_warning()
        ]);
    }

    private function create_groups_table(): Enhanced_Table
    {
        $table = Enhanced_Table::make_empty(Groups_Table_Item::class);

        $table->set_style(new Wpi_Style());

        $table->set_items($this->get_table_items());

        return $table;
    }

    private function get_table_items(): Enhanced_Table_Items_Collection
    {
        $collection = new Enhanced_Table_Items_Collection();
        $groups = Template_Group::find_all();

        $collection->set_total(count($groups));

        foreach ($groups as $group) {
            /** @var Template_Group $group */
            $table_item = new Groups_Table_Item(
                $this->get_group_templates_list_link($group),
                $this->get_activation_link_for_the_group($group),
                $this->create_group_edit_link($group),
                $this->create_group_color_settings_link($group),
                $this->create_settings_button($group)
            );

            $collection->append($table_item);
        }

        return $collection;
    }

    private function get_activation_link_for_the_group(Template_Group $group): Interface_Renderable
    {
        if (Template_Group::no_active_group_present()) {
            $activate_template_warning_popup = Popup::create(
                'activate-warning-' . $group->get_id()->stringify(),
                View::get_admin('/templates/activate-when-no-active-group-warning', [
                    'proceed_action' => $group->get_activation_url(),
                    'no_active_group_warning' => $this->create_no_active_group_warning()->get_html()
                ])
            );

            return Button::create(__('Activate', BPMJ_EDDCM_DOMAIN), Button::TYPE_CLEAN, 'activate-when-no-group-active-button')
                ->open_popup_on_click($activate_template_warning_popup)
                ->add_class('inline-button')
                ->set_dashicon(Dashicon::create('warning'));
        }

        $element = !$group->is_active() ? Link::create(
            __('Activate', BPMJ_EDDCM_DOMAIN),
            $group->get_activation_url()
        )->add_class('inline-button') : Dashicon::create('yes', 'template-enabled');

        if($element instanceof Link && $this->is_classic($group->get_name())) {
            $element->set_as_disabled();
        }

        if($element instanceof Link && ($group->get_base_template() === Template_Group::BASE_TEMPLATE_SCARLET)) {
            $activate_scarlet_warning_popup = Popup::create(
                'activate-scarlet-warning',
                View::get_admin('/templates/activate-scarlet-warning', [
                    'proceed_action' => $group->get_activation_url(),
                    'warning' => $this->get_activate_scarlet_warning()
                ])
            );

            return Button::create(__('Activate', BPMJ_EDDCM_DOMAIN), Button::TYPE_CLEAN, 'activate-when-no-group-active-button')
                ->open_popup_on_click($activate_scarlet_warning_popup)
                ->add_class('inline-button');
        }

        return $element;
    }

    private function get_group_templates_list_link(Template_Group $group): Link
    {
        $template_name = $group->get_name();

        if($this->is_classic($template_name)) {
            $template_name .= ' ' . $this->translator->translate('templates_system.classic.undeveloped');
        }

        return Link::create(
            $template_name,
            $group->get_edit_url()
        );
    }

    private function create_info_box(): Info_Box
    {
        $info_box = Info_Box::create(
            __('WP Idea templates system', BPMJ_EDDCM_DOMAIN),
            BPMJ_EDDCM_URL . 'assets/imgs/wpi-builder.png'
        );

        $info_box
            ->add_paragraph(sprintf(__('From here you can activate or edit the selected template. %sEach template can be edited or extended via Gutenberg block editor%s, which gives you plentiful of new possibilities! You can now customize the WP Idea to make it look exactly the way you like! ', BPMJ_EDDCM_DOMAIN), '<strong>', '</strong>'))
            ->set_size(Info_Box::SIZE_DEFAULT);
        return $info_box;
    }

    private function create_group_edit_link(Template_Group $group): Link
    {
        return Link::create(__('Edit', BPMJ_EDDCM_DOMAIN), $group->get_edit_url())
            ->add_class('inline-button')
            ->add_class('inline-button--main')
            ->set_dashicon(Dashicon::create('edit'));
    }

    private function create_settings_button(Template_Group $group): Button
    {
        $popup = Popup::create_ajax(
            'edit_group_settings_' . $group->get_id()->stringify(),
            Group_Settings_Ajax_Handler::AJAX_GET_SETTINGS_ACTION_NAME,
            [
                self::AJAX_PARAM_NAME_SECURITY_TOKEN => wp_create_nonce(self::AJAX_NONCE_NAME),
                self::AJAX_PARAM_NAME_GROUP_ID => $group->get_id()->stringify()
            ]
        )->add_class('group-settings-popup');

        return Button::create(__('Settings', BPMJ_EDDCM_DOMAIN))
            ->set_dashicon(Dashicon::create('admin-generic'))
            ->add_class('template-group-settings-button')
            ->open_popup_on_click($popup);
    }

    public function render_settings_popup_content(): void
    {
        $this->validate_permissions_or_die();

        $this->validate_settings_popup_content_token_or_die();

        $group_id = !empty($_GET[self::AJAX_PARAM_NAME_GROUP_ID]) ? sanitize_text_field($_GET[self::AJAX_PARAM_NAME_GROUP_ID]) : null;

        if (!$group_id) {
            wp_send_json_error(__('No group ID provided.', BPMJ_EDDCM_DOMAIN));
        }

        $group = Template_Group::find(Template_Group_Id::from_string($group_id));

        if (!$group) {
            wp_send_json_error(__('No group with a given ID exists.', BPMJ_EDDCM_DOMAIN));
        }

        $settings = $group->get_settings();

        wp_send_json_success(View::get_admin('/templates/settings/template-group-settings', [
            'title' => $this->translator->translate('templates_list.popup_title'),
            'group_id' => $group->get_id()->stringify(),
            'fields' => Software_Variant::is_saas() ? $settings->unset('override_all')->get_all() : $settings->get_all()
        ]));
    }

    private function validate_permissions_or_die(): void
    {
        $user = $this->current_user_getter->get();
        $cap = $this->capability_factory->create_from_name(Caps::CAP_MANAGE_SETTINGS);
        if (!$this->user_permissions_service->has_capability($user, $cap)) {
            wp_send_json_error(__('You are not authorized to perform this operation!', BPMJ_EDDCM_DOMAIN));
        }
    }

    private function validate_settings_popup_content_token_or_die(): void
    {
        if (!check_ajax_referer(self::AJAX_NONCE_NAME, self::AJAX_PARAM_NAME_SECURITY_TOKEN, false)) {
            wp_send_json_error('Invalid security token');
        }
    }

    private function create_group_color_settings_link(Template_Group $group): Link
    {
        $link = Link::create(__('Color settings', BPMJ_EDDCM_DOMAIN), $group->get_color_settings_url())
            ->add_class('inline-button')
            ->set_dashicon(Dashicon::create('admin-customizer'));

        if (!$group->is_active()) {
           $link->set_as_disabled();
           $link->add_title(__('You can customize color settings only when the template is active.', BPMJ_EDDCM_DOMAIN));
        }

        return $link;
    }

    private function create_and_get_no_active_group_warning(): string
    {
        if (!Template_Group::no_active_group_present()) {
            return '';
        }

        $p = $this->create_no_active_group_warning();
        $p->add_class('no-template-group-enabled-warning');

        return $p->get_html();
    }

    private function create_no_active_group_warning(): Paragraph
    {
        $p = Paragraph::create(sprintf(__('%sIt looks like none of the templates are active.%s 
            Probably before implementing the new template system, 
            you used the "disabled" option in the WP Idea template settings. 
            This functionality has been deprecated. 
            You can still use it, however, once you enable any of the new templates, you 
            won\'t be able to go back to the mode with the WP Idea template disabled.', BPMJ_EDDCM_DOMAIN), '<strong>', '</strong>'));

        return $p;
    }

    private function is_classic(string $template_name): bool
    {
        return 'Classic' === $template_name || 'Klasyczny' === $template_name;
    }

    private function get_activate_scarlet_warning(): string
    {
        return $this->translator->translate('templates_system.scarlet.activation_warning');
    }
}