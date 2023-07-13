<?php

namespace bpmj\wpidea\templates_system\admin;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\admin\tables\Enhanced_Table_Items_Collection;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\groups\Template_Group_Id;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Lesson_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Module_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Panel_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Quiz_Template;
use bpmj\wpidea\templates_system\templates\Template;
use bpmj\wpidea\templates_system\templates\Template_Actions_Handler;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\nonce\Nonce_Handler;

class Templates_List_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private const COUNT_VALUE_FOR_DISABLED_PAGINATION = 0;
    private Interface_Settings $settings;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;

    private bool $courses_functionality_enabled;

    public function __construct(
        Interface_Settings $settings,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator
    ) {
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->settings = $settings;
    }

    public function get_rows(array $filters, Sort_By_Clause $sort_by, int $per_page, int $page, Dynamic_Table_Data_Usage_Context $context): array
    {
        $group_id = $this->get_active_group_id();

        if(is_null($group_id)){
            return [];
        }

        $templates = Template::find_by_group($group_id);

        $rows = [];

        /** @var Template $template */
        foreach ($templates as $template) {
            if ($this->template_should_not_be_editable($template)) {
                continue;
            }

            $rows[] = [
                'layout_type' => $this->translator->translate($template->get_name()),
                'layout_description' => $this->translator->translate($template->get_default_name() . '.description'),
                'edit_url' => $template->get_edit_url(),
                'restore_url' => $this->get_restore_url($template->get_id()),
            ];
        }

        return $rows;
    }

    private function get_restore_url(int $id): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => 'wp-idea-templates',
            Template_Actions_Handler::QUERY_PARAM_RESTORE_TEMPLATE => $id,
            Template_Actions_Handler::QUERY_PARAM_NONCE => Nonce_Handler::create(Template_Actions_Handler::QUERY_PARAM_RESTORE_TEMPLATE)
        ]);
    }

    public function get_total(array $filters): int
    {
        return self::COUNT_VALUE_FOR_DISABLED_PAGINATION;
    }

    private function get_active_group_id(): ?Template_Group_Id
    {
        $template_group = $this->get_active_group();

        if(!$template_group) {
            return null;
        }

        return $template_group->get_id();
    }

    private function get_active_group(): ?Template_Group
    {
        return Template_Group::get_active_group();
    }

    private function template_should_not_be_editable(
        Template $template
    ): bool {
        return !$this->is_courses_module_enabled()
            && $this->template_depends_on_courses_module_enabled($template);
    }

    private function get_course_related_template_classes(): array
    {
        return [
            Course_Module_Template::class,
            Course_Panel_Template::class,
            Course_Lesson_Template::class,
            Course_Quiz_Template::class
        ];
    }

    private function template_depends_on_courses_module_enabled(Template $template): bool
    {
        $course_related_template_classes = $this->get_course_related_template_classes();

        return in_array(get_class($template), $course_related_template_classes, true);
    }

    private function is_courses_module_enabled(): bool
    {
        $this->courses_functionality_enabled = $this->courses_functionality_enabled ?? ($this->settings->get(Settings_Const::COURSES_ENABLED) ?? true);

        return $this->courses_functionality_enabled;
    }
}