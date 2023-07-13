<?php

namespace bpmj\wpidea\templates_system\admin\renderers;

use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Dashicon;
use bpmj\wpidea\admin\helpers\html\Info_Box;
use bpmj\wpidea\admin\helpers\html\Link;
use bpmj\wpidea\admin\helpers\html\Popup;
use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\renderers\Interface_Page_Renderer;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\tables\Enhanced_Table;
use bpmj\wpidea\admin\tables\Enhanced_Table_Items_Collection;
use bpmj\wpidea\admin\tables\styles\Wpi_Style;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\templates_system\admin\Template_Groups_Page;
use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\groups\Template_Group_Id;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Lesson_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Module_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Panel_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Quiz_Template;
use bpmj\wpidea\templates_system\templates\Template;
use bpmj\wpidea\View;
use bpmj\wpidea\translator\Interface_Translator;

class Legacy_Templates_List_Renderer extends Abstract_Page_Renderer implements Interface_Templates_List_Renderer
{
    public const GROUP_ID_QUERY_PARAM = 'group_id';

    private Template_Groups_Page $template_groups_page;
    private Interface_Translator $translator;

    private LMS_Settings $lms_settings;

    public function __construct(
        Template_Groups_Page $template_groups_page,
        LMS_Settings $lms_settings,
        Interface_Translator $translator
    )
    {
        $this->template_groups_page = $template_groups_page;
        $this->lms_settings = $lms_settings;
        $this->translator = $translator;
    }

    public function get_rendered_page(): string
    {
        $group_id = $this->get_group_id_from_url();

        return View::get_admin('/templates/legacy-templates-list-page', [
            'page_title' => __('Templates list', BPMJ_EDDCM_DOMAIN),
            'template_group' => $group_id ? Template_Group::find($group_id) : null,
            'table' => $this->create_templates_table(),
            'info_box' => $this->create_info_box(),
            'template_groups_page_url' => $this->template_groups_page->get_url()
        ]);
    }

    private function create_templates_table(): Enhanced_Table
    {
        $table = Enhanced_Table::make_empty(Templates_List_Table_Item::class);

        $table->set_style(new Wpi_Style());

        $table->set_items($this->get_table_items());

        return $table;
    }

    private function get_table_items(): Enhanced_Table_Items_Collection
    {
        $collection = new Enhanced_Table_Items_Collection();
        $group_id = $this->get_group_id_from_url();

        if(is_null($group_id)){
            return $collection;
        }

        $templates = Template::find_by_group($group_id);

        $collection->set_total(count($templates));

        $courses_functionality_enabled = $this->lms_settings->get(Settings_Const::COURSES_ENABLED) ?? true;

        $course_related_template_classes = [
            Course_Module_Template::class,
            Course_Panel_Template::class,
            Course_Lesson_Template::class,
            Course_Quiz_Template::class
            ];

        foreach ($templates as $template) {

            if(
                !$courses_functionality_enabled
                && in_array(get_class($template), $course_related_template_classes)
            ){
                continue;
            }

            $restore_warning_popup = Popup::create(
                'restore-tpl-' . $template->get_id(),
                View::get_admin('/templates/template/restore-warning', [
                    'proceed_action' => $template->get_delete_url()
                ])
            );

            /** @var Template $template */
            $table_item = new Templates_List_Table_Item(
                $this->translator->translate($template->get_name()),
                Link::create(__('Edit', BPMJ_EDDCM_DOMAIN), $template->get_edit_url())
                    ->add_class('inline-button')
                    ->add_class('inline-button--main')
                    ->set_dashicon(Dashicon::create('edit')),
                Button::create(__('Restore', BPMJ_EDDCM_DOMAIN), Button::TYPE_SECONDARY, 'restore-button')
                    ->open_popup_on_click($restore_warning_popup)
                    ->add_class('inline-button')
                    ->set_dashicon(Dashicon::create('backup'))
            );

            $collection->append($table_item);
        }

        return $collection;
    }

    private function create_info_box(): Info_Box
    {
        $info_box = Info_Box::create(__('WP Idea page templates', BPMJ_EDDCM_DOMAIN));

        $info_box
            ->set_size(Info_Box::SIZE_SMALL)
            ->add_paragraph(sprintf(__('Here you can edit the template layout of the individual pages (eg Course Panel Page, Lesson, Cart, etc.). Changes made to the templates will be applied to all pages of that type (e.g. to all lessons). %sBut no worries%s - if you inadvertently break something, you can just click a button %s"Restore"%s next to the selected template.', BPMJ_EDDCM_DOMAIN), '<strong>', '</strong>', '<i>', '</i>'));

        return $info_box;
    }

    private function get_group_id_from_url(): ?Template_Group_Id
    {
        return isset($_GET[self::GROUP_ID_QUERY_PARAM]) ? Template_Group_Id::from_string($_GET[self::GROUP_ID_QUERY_PARAM]) : null;
    }
}
