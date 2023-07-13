<?php

namespace bpmj\wpidea\view;

interface Interface_Admin_Bar
{
    public function get_menu_position_by_id(string $id): ?object;

    public function set_menu_position(object $menu_position): void;

    public function remove_menu_position_by_id(string $id): void;

    public function add_menu_position(object $menu_position): void;
}