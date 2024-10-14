<?php

namespace bpmj\wpidea\templates_system\admin\blocks\attributes;

class Select_Attribute extends Attribute
{
    protected string $type = 'string';

    protected ?string $input_type = 'select';

    protected array $options = [];

    public function add_option(string $label, string $value): void
    {
        $this->options[] = [
            'label' => $label,
            'value' => $value
        ];
    }

    protected function get_data(): array
    {
        return array_merge(
            parent::get_data(),
            [
                'options' => $this->options,
            ]
        );
    }
}
