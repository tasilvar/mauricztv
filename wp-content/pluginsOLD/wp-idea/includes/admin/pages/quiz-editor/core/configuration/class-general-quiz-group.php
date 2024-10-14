<?php

namespace bpmj\wpidea\admin\pages\quiz_editor\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;
use bpmj\wpidea\admin\settings\core\collections\Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Checkbox_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Configure_Popup_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Media_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Number_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Text_Area_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Text_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Toggle_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Time_Limit_Settings;

class General_Quiz_Group extends Abstract_Settings_Group
{
    public const NAME = 'name';
    public const DESCRIPTION_POPUP = 'description_popup';
    public const DESCRIPTION = 'description';
    private const SUBTITLE_MODE = 'subtitle_mode';
    private const ADDITIONAL_INFO = 'additional_info';
    private const SUBTITLE = 'subtitle';
    private const LEVEL = 'level';
    private const DURATION = 'duration';
    private const SLUG = 'slug';
    private const FEATURED_IMAGE = 'featured_image';
    public const TIME_MODE = 'time_mode';
    private const TIME = 'time';
    public const EVALUATED_BY_ADMIN_MODE = 'evaluated_by_admin_mode';
    public const RANDOMIZE_QUESTION_ORDER = 'randomize_question_order';
    public const RANDOMIZE_ANSWER_ORDER = 'randomize_answer_order';
	public const ATTEMPTS_MODE = 'attempts_mode';
	private const ATTEMPTS_NUMBER = 'attempts_number';
    public const CAN_SEE_ANSWERS_MODE = 'can_see_answers_mode';
    public const ALSO_SHOW_CORRECT_ANSWERS = 'also_show_correct_answers';

	public function get_name(): string
    {
        return 'general';
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.name_description'),
            (new Fields_Collection())
                ->add($this->get_name_field())
                ->add($this->get_description_popup_field())
                ->add($this->get_subtitle_popup_field())
                ->add($this->get_additional_information_popup_field())
        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.location'),
            (new Fields_Collection())
                ->add($this->get_url_field())
        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.graphic'),
            (new Fields_Collection())
                ->add($this->get_featured_image_field())
        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.quiz_settings'),
            (new Fields_Collection())
                ->add($this->get_evaluated_by_admin_mode_field())
                ->add($this->get_time_popup_field())
                ->add($this->get_attempts_popup_field())
                ->add($this->get_enable_answers_preview_field())
                ->add($this->get_randomize_question_order_field())
                ->add($this->get_randomize_answer_order_field())
        );
    }

    private function get_name_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::NAME,
            $this->translate_with_prefix('sections.general.name')
        ))->set_max_length(200);
    }

    private function get_description_popup_field(): Abstract_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::DESCRIPTION_POPUP,
            $this->translate_with_prefix('sections.general.description'),
            null,
            $this->translate_with_prefix('sections.general.description.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_description_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.general.description')
        );
    }

    private function get_description_field(): Abstract_Setting_Field
    {
        return new Text_Area_Setting_Field(
            self::DESCRIPTION,
            $this->translate_with_prefix('sections.general.description')
        );
    }

    private function get_subtitle_popup_field(): Abstract_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::SUBTITLE_MODE,
            $this->translate_with_prefix('sections.general.subtitle'),
            null,
            $this->translate_with_prefix('sections.general.subtitle.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_subtitle_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.general.subtitle')
        );
    }

    private function get_subtitle_field(): Abstract_Setting_Field
    {
        return new Text_Area_Setting_Field(
            self::SUBTITLE,
            $this->translate_with_prefix('sections.general.subtitle')
        );
    }

    private function get_additional_information_popup_field(): Abstract_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::ADDITIONAL_INFO,
            $this->translate_with_prefix('sections.general.additional_info'),
            null,
            $this->translate_with_prefix('sections.general.additional_info.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_level_field())
                ->add($this->get_duration_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.general.additional_info')
        );
    }

    private function get_level_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::LEVEL,
            $this->translate_with_prefix('sections.general.level'),
            null,
            $this->translate_with_prefix('sections.general.level.tooltip')
        );
    }

    private function get_duration_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::DURATION,
            $this->translate_with_prefix('sections.general.duration'),
            null,
            $this->translate_with_prefix('sections.general.duration.tooltip')
        );
    }

    private function get_url_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::SLUG,
            $this->translate_with_prefix('sections.general.url')
        ))->set_sanitize_callback(function ($value) {
            return sanitize_title($value);
        });
    }

    private function get_featured_image_field(): Abstract_Setting_Field
    {
        return (new Media_Setting_Field(
            self::FEATURED_IMAGE,
            $this->translate_with_prefix('sections.general.featured_image')
        ))->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (empty($value)) {
                return $results;
            }

            if (!attachment_url_to_postid($value)) {
                $results->add_error_message('product_editor.sections.general.featured_image.attachment_must_exist');
            }

            return $results;
        });
    }

    private function get_evaluated_by_admin_mode_field(): Abstract_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::EVALUATED_BY_ADMIN_MODE,
            $this->translate_with_prefix('sections.general.evaluated_by_admin_mode'),
            null,
            $this->translate_with_prefix('sections.general.evaluated_by_admin_mode.tooltip')
        );
    }

    private function get_time_popup_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::TIME_MODE,
            $this->translate_with_prefix('sections.general.time'),
            null,
            $this->translate_with_prefix('sections.general.time.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_time_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.general.time')
        );
    }

    private function get_time_field(): Abstract_Setting_Field
    {
        $field = new Number_Setting_Field(
            self::TIME,
            $this->translate_with_prefix('sections.general.time'),
            null,
            null,
            null,
            Quiz_Time_Limit_Settings::DEFAULT_TIME
        );

        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (!is_numeric($value) || (int)$value <= 0) {
                $results->add_error_message('quiz_editor.sections.general.time.validation');
            }
            return $results;
        });

        return $field;
    }

	private function get_attempts_popup_field(): Abstract_Setting_Field
	{
		return (new Toggle_Setting_Field(
			self::ATTEMPTS_MODE,
			$this->translate_with_prefix('sections.general.number_test_attempts'),
			null,
			$this->translate_with_prefix('sections.general.number_test_attempts.tooltip'),
			(new Additional_Fields_Collection())
				->add($this->get_attempts_number_field())
		))->set_popup(
			$this->settings_popup,
			$this->translate_with_prefix('sections.general.number_test_attempts')
		);
	}

	private function get_attempts_number_field(): Abstract_Setting_Field
	{
		$field = new Number_Setting_Field(
			self::ATTEMPTS_NUMBER,
			$this->translate_with_prefix('sections.general.number_test_attempts')
		);

		$field->set_sanitize_callback(function ($value) {
			return trim($value);
		});
		$field->set_validation_callback(function ($value) {
			$results = new Setting_Field_Validation_Result();

			if (!is_numeric($value) || (int)$value <= 0) {
				$results->add_error_message('quiz_editor.sections.general.number_test_attempts.validation');
			}
			return $results;
		});

		return $field;
	}

    private function get_randomize_question_order_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::RANDOMIZE_QUESTION_ORDER,
            $this->translate_with_prefix('sections.general.randomizing_and_limiting_questions'),
            null,
            $this->translate_with_prefix('sections.general.randomize_question_order.tooltip'),
        ));
    }

    private function get_randomize_answer_order_field(): Abstract_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::RANDOMIZE_ANSWER_ORDER,
            $this->translate_with_prefix('sections.general.randomize_answer_order'),
            null,
            $this->translate_with_prefix('sections.general.randomize_answer_order.tooltip')
        );
    }

    private function translate_with_prefix(string $translation_id): string
    {
        return $this->translator->translate($this->get_translate_prefix() . '.' . $translation_id);
    }

    private function get_translate_prefix(): string
    {
        return 'quiz_editor';
    }

    private function get_enable_answers_preview_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::CAN_SEE_ANSWERS_MODE,
            $this->translate_with_prefix('sections.general.answers_preview'),
            null,
            $this->translate_with_prefix('sections.general.answers_preview.tooltip'),
            (new Additional_Fields_Collection())
            ->add($this->get_also_show_correct_answers_filed())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.general.answers_preview')
        );
    }

    private function get_also_show_correct_answers_filed(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::ALSO_SHOW_CORRECT_ANSWERS,
            $this->translate_with_prefix('sections.general.also_show_correct_answers'),
            null,
            $this->translate_with_prefix('sections.general.also_show_correct_answers.tooltip')
        );
    }
}