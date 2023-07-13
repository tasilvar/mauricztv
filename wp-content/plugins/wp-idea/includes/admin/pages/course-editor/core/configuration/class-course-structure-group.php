<?php

namespace bpmj\wpidea\admin\pages\course_editor\core\configuration;

use bpmj\wpidea\admin\pages\course_editor\core\fields\{Drip_Unit_Select_Setting_Field, Drip_Value_Number_Setting_Field};
use bpmj\wpidea\admin\pages\course_editor\core\fields\Course_Structure_Field;
use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;
use bpmj\wpidea\admin\settings\core\collections\Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Message;
use bpmj\wpidea\Courses;
use bpmj\wpidea\courses\core\value_objects\Drip_Unit;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;

class Course_Structure_Group extends Abstract_Settings_Group
{
    public const GROUP_NAME = 'structure';
    private const COURSE_STRUCTURE = 'structure';
    private const DRIP_VALUE = 'drip_value';
    private const DRIP_UNIT = 'drip_unit';

    private Courses $courses;
    private Interface_Packages_API $packages_api;

    public function __construct(
        Courses $courses,
        Interface_Packages_API $packages_api
    )
    {
        $this->courses = $courses;
        $this->packages_api = $packages_api;
    }

    public function get_name(): string
    {
        return self::GROUP_NAME;
    }

    public function register_fields(): void
    {
        $this->add_field($this->get_info_field());

        $this->add_fieldset(
            $this->translator->translate('course_editor.sections.structure.fieldset.module_availability'),
            (new Fields_Collection())
                ->add($this->get_drip_unit_field())
                ->add($this->get_drip_value_field())
        );


        $this->add_field($this->get_course_structure_field());
    }

    private function get_info_field(): Abstract_Setting_Field
    {
        return new Message($this->translator->translate('course_editor.sections.structure.info'));
    }

    private function get_drip_value_field(): Abstract_Setting_Field
    {
        return (new Drip_Value_Number_Setting_Field(
            self::DRIP_VALUE,
            $this->translator->translate('course_editor.sections.structure.fieldset.drip_value'),
            null,
            $this->translator->translate('course_editor.sections.structure.fieldset.drip_value.tooltip')
        ))->set_related_feature(Packages::FEAT_DELAYED_ACCESS);
    }

    private function get_drip_unit_field(): Abstract_Setting_Field
    {
        return (new Drip_Unit_Select_Setting_Field(
            self::DRIP_UNIT,
            $this->translator->translate('course_editor.sections.structure.fieldset.drip_unit'),
            null,
            null,
            null,
            $this->get_drip_unit_options()
        ))->set_related_feature(Packages::FEAT_DELAYED_ACCESS);
    }

    private function get_course_structure_field(): Abstract_Setting_Field
    {
        $course_id = $this->current_request->get_query_arg(Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME);

        return new Course_Structure_Field(
            self::COURSE_STRUCTURE,
            new Course_ID((int)$course_id),
            $this->courses,
            $this->packages_api,
            $this->translator
        );
    }

    private function get_drip_unit_options(): array
    {
        $options = [];

        foreach (Drip_Unit::VALID_UNIT as $unit) {
            $options[$unit] = $this->translator->translate('course_editor.sections.general.access_time_unit.option.' . $unit);
        }

        return $options;
    }
}