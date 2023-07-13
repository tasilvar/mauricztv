<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\admin\helpers\html\Configuration_Settings_Popup;
use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\fields\relation\Field_Relation;
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place\Interface_Settings_Storage_Place;
use bpmj\wpidea\admin\settings\web\Settings_Info_Box;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\packages\Packages_API_Static_Helper;
use Closure;

abstract class Abstract_Setting_Field
{
    private const DISABLED_HTML_ATTR = 'disabled';

    private const READONLY ='readonly';

    private string $name;

    private ?string $label;

    private ?string $description;

    private ?string $tooltip;

    private ?Closure $validation_callback = null;

    private ?Closure $sanitize_callback = null;

    private $value;

    private ?Additional_Fields_Collection $additional_fields;

    private ?Configuration_Settings_Popup $popup = null;

    private ?string $popup_title = null;

    private bool $visible = true;

    private bool $disabled = false;

    private ?string $disabled_reason = null;

    private bool $readonly = false;

    private ?Interface_Settings_Storage_Place $storage_place = null;

    private bool $use_raw_value = false;

    private ?Field_Relation $relation = null;

    private ?string $related_feature = null;

    private string $info_message = '';
    private string $info_message_type = Settings_Info_Box::INFO_BOX_TYPE_DEFAULT;

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        ?Additional_Fields_Collection $additional_fields = null,
        $value = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->description = $description;
        $this->tooltip = $tooltip;
        $this->additional_fields = $additional_fields;
        $this->value = $value;
    }

    public function set_popup(Configuration_Settings_Popup $popup, string $popup_title): self
    {
        $this->popup = $popup;
        $this->popup_title = $popup_title;
        return $this;
    }

    public function get_popup(): ?string
    {
        if (!$this->has_additional_config()) {
            return null;
        }

        if($this->field_requires_higher_package()) {
            $this->popup->disable_button();
        }

        return $this->popup->get_html(
            $this->get_name() . '-additional-settings-popup',
            $this->popup_title,
            $this->additional_fields
        );
    }

    public function set_validation_callback(Closure $callback): self
    {
        $this->validation_callback = $callback;
        return $this;
    }

    public function set_sanitize_callback(Closure $callback): self
    {
        $this->sanitize_callback = $callback;
        return $this;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_value()
    {
        return $this->value;
    }

    public function change_value($value): self
    {
        if ($this->sanitize_callback) {
            $value = $this->sanitize_callback->call($this, $value);
        }
        $this->value = $value;
        return $this;
    }

    public function get_label(): ?string
    {
        return $this->label;
    }

    public function get_description(): ?string
    {
        return $this->description;
    }

    public function get_tooltip(): ?string
    {
        return $this->tooltip;
    }

    public function get_additional_fields(): ?Additional_Fields_Collection
    {
        return $this->additional_fields;
    }

    public function validate(): Setting_Field_Validation_Result
    {
        if ($this->validation_callback) {
            return $this->validation_callback->call($this, $this->get_value());
        }
        return new Setting_Field_Validation_Result();
    }

    public function has_additional_config(): bool
    {
        if (!$this->additional_fields) {
            return false;
        }

        return true;
    }

    public function change_visibility(bool $visible): self
    {
        $this->visible = $visible;
        return $this;
    }

    public function is_visible(): bool
    {
        if (!$this->visible) {
            return false;
        }

        return true;
    }

    public function disable(): self
    {
        $this->disabled = true;

        return $this;
    }

    public function disable_with_reason(string $reason): self
    {
        $this->disabled = true;
        $this->disabled_reason = $reason;

        return $this;
    }

    private function is_disabled(): bool
    {
        return $this->disabled || $this->field_requires_higher_package();
    }
    public function get_disabled_html_attr(): string
    {
        if($this->is_disabled()) {
            return self::DISABLED_HTML_ATTR;
        }

        return '';

    }

    public function set_related_feature(string $feature): self
    {
        $this->related_feature = $feature;

        return $this;
    }

    public function add_info_message(string $message, string $type = Settings_Info_Box::INFO_BOX_TYPE_DEFAULT): self
    {
        $this->info_message = $message;
        $this->info_message_type = $type;

        return $this;
    }

    private function get_info_message(): ?string
    {
        return !empty($this->info_message) ? $this->info_message : null;
    }

    public function field_requires_higher_package(): bool
    {
        if(!$this->related_feature) {
            return false;
        }

        return !Packages_API_Static_Helper::has_access_to_feature($this->related_feature);
    }

    public function set_readonly(bool $readonly): self
    {
        $this->readonly = $readonly;
        return $this;
    }

    public function get_readonly(): string
    {
        if(!$this->readonly){
            return '';
        }

        return self::READONLY;
    }

    public function set_storage_place(Interface_Settings_Storage_Place $storage_place): self
    {
        $this->storage_place = $storage_place;

        return $this;
    }

    public function get_storage_place(): ?Interface_Settings_Storage_Place
    {
        return $this->storage_place;
    }

    public function set_use_raw_value(bool $use_raw_value): self
    {
        $this->use_raw_value = $use_raw_value;
        return $this;
    }

    public function get_use_raw_value(): bool
    {
        if (!$this->use_raw_value) {
            return false;
        }

        return true;
    }

    public function set_relation(?Field_Relation $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    private function maybe_render_info_box(): string
    {
        if($this->field_requires_higher_package()) {
            return $this->get_min_required_package_info_box();
        }

        if($this->disabled && $this->disabled_reason) {
            return $this->render_info_box_to_string($this->disabled_reason, Settings_Info_Box::INFO_BOX_TYPE_FIELD_DISABLED_FOR_A_REASON);
        }

        return '';
     }

     private function get_min_required_package_info_box(): string
     {
         return Packages_API_Static_Helper::render_no_access_to_feature_info($this->related_feature, null, true);
     }

    protected function render_info_box_to_string(string $message, string $type = Settings_Info_Box::INFO_BOX_TYPE_DEFAULT): string
    {
        return Settings_Info_Box::render_info_box($message, $type);
    }

    protected function get_field_wrapper_start($extra_input_wrapper_class = null): string
    {
        $info_message = $this->get_info_message() ? $this->render_info_box_to_string($this->get_info_message(), $this->info_message_type) : '';
        $depends_on =  $this->get_depends_on_dataset();
        $requires_higher_package_class = $this->field_requires_higher_package() ? 'single-field-wrapper--requires-higher-package' : '';
        $disabled_class = $this->is_disabled() ? ' single-field-wrapper--disabled' : '';

        $tooltip = $this->get_tooltip() ? "<span class='field-tooltip'>
                <img src='" . BPMJ_EDDCM_URL . "assets/imgs/settings/tooltip-icon.svg' alt=''/>
                <span class='field-tooltip-text'>" . $this->get_tooltip() . "</span>
            </span>" : '';


        return $info_message . "<div class='single-field-wrapper " . $requires_higher_package_class . ' ' . $disabled_class . "' data-related-field='" . $this->get_name() . "' $depends_on>
        <label for='" . $this->get_name() . "' class='field-label'>
            " . $this->get_label() . $tooltip . "
        </label>

        <div class='single-input-wrapper ".$extra_input_wrapper_class."'>";
    }

    protected function get_field_wrapper_end(bool $hidden_save_fields = false): string
    {
        $hint = $this->get_description() ? "<span class='hint'>" . $this->get_description() . "</span>" : '';
        $info_box = $this->maybe_render_info_box();

        $save_field =  !$hidden_save_fields ? "<button class='single-field-save-button' data-related-field='" . $this->get_name() . "' style='display: none;'>
                            " . Translator_Static_Helper::translate('settings.field.button.save') . "
                         </button>
                         <button class='single-field-cancel-button' data-related-field='" . $this->get_name() . "' style='display: none;'>
                           " . Translator_Static_Helper::translate('settings.field.button.cancel') . "
                         </button>" : '';

        return "
            $info_box
            $hint
            <span class='validation-errors' style='display: none;'></span>
        </div>

        <div class='field-buttons'>
           ".$save_field."
            <span class='saving' style='display: none;'>" . Translator_Static_Helper::translate('settings.field.button.saving') . "</span>
            <span class='saved' style='display: none;'>" . Translator_Static_Helper::translate('settings.field.button.saved') . "</span>
        </div>
    </div>";
    }

	protected function get_depends_on_dataset(): string
	{
		if (!$this->relation) {
			return '';
		}

        $relationType = $this->relation->get_relation_type();

        if ($relationType === Field_Relation::TYPE_DEPENDS_ON_SELECT_VALUE_EQUALS) {
            return $this->get_depends_on_select_value_equals();
        }

        if ($relationType === Field_Relation::TYPE_DEPENDS_ON_SELECT_VALUE_NOT_EQUALS) {
            return $this->get_depends_on_select_value_not_equals();
        }

        $relatedFieldName = $this->relation->get_related_field_name();
        return 'data-show-only-on-toggle-' . $relationType . '="' . $relatedFieldName . '"';
	}

    abstract public function render_to_string(): string;

    private function get_depends_on_select_value_equals(): string
    {
        $selectFieldValue = $this->relation->get_select_field_value();
        if (is_null($selectFieldValue)) {
            return '';
        }

        $relatedFieldName = $this->relation->get_related_field_name();

        return 'data-show-only-on-select-value="' . $relatedFieldName . '" data-select-value="' . $selectFieldValue . '"';
    }

    private function get_depends_on_select_value_not_equals(): string
    {
        $selectFieldValue = $this->relation->get_select_field_value();
        if (is_null($selectFieldValue)) {
            return '';
        }

        $relatedFieldName = $this->relation->get_related_field_name();

        return 'data-hide-only-on-select-value="' . $relatedFieldName . '" data-select-value="' . $selectFieldValue . '"';
    }

}