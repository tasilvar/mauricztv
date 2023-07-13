<?php
namespace bpmj\wpidea\templates_system\admin\blocks\attributes;

abstract class Attribute
{
    protected string $label;

    protected ?string $hint;

    protected string $slug;

    /**
     * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/#type-validation
     */
    protected string $type;

    protected ?string $input_type = null;

    protected ?string $default_value = null;

    public function __construct($slug, $label, $hint = null, $default_value = null) {

        $this->slug = $slug;
        $this->label = $label;
        $this->hint = $hint;
        $this->default_value = $default_value;
    }

    public function get(): array
    {
        return [
            $this->slug => $this->get_data()
        ];
    }

    protected function get_data(): array
    {
        return [
            'type' => $this->type,
            'input_type' => $this->input_type ?? $this->type,
            'label' => $this->label,
            'help' => $this->hint,
            'default' => $this->default_value,
        ];
    }
}
