<?php
namespace bpmj\wpidea\templates_system\admin\blocks\attributes;


class Number_Attribute extends Attribute
{
    protected string $type = 'number';

    protected array $min = [];

    public function set_min_value(int $min_value, string $reason): void
    {
        $this->min = [
            'value' => $min_value,
            'warning' => $reason
        ];
    }

    protected function get_data(): array
    {
        return array_merge(
            parent::get_data(),
            [
                'min' => $this->min,
            ]
        );
    }
}