<?php

namespace bpmj\wpidea\templates_system\admin\blocks\attributes;

class Toggle_Attribute extends Attribute
{
    protected string $type = 'boolean';

    protected bool $checked = false;

    protected function get_data(): array
    {
        return array_merge(
            parent::get_data(),
            [
                'checked' => $this->checked,
            ]
        );
    }
}
