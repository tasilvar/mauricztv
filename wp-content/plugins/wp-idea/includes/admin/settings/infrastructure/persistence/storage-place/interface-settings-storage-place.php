<?php

namespace bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place;

interface Interface_Settings_Storage_Place
{
    public function get_data(string $name);

    public function update_data(string $name, $value): void;
}