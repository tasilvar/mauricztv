<?php

namespace bpmj\wpidea\view;

class Admin_Bar implements Interface_Admin_Bar
{

    public function get_menu_position_by_id(string $id): ?object
    {
        global $wp_admin_bar;
        $menu_position = $wp_admin_bar->get_node($id);
        return $menu_position ?? null;
    }

    public function remove_menu_position_by_id(string $id): void
    {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu($id);
    }

    public function add_menu_position(object $menu_position): void
    {
        global $wp_admin_bar;
        $wp_admin_bar->add_menu($menu_position);
    }

    public function set_menu_position(object $menu_position): void
    {
        $this->remove_menu_position_by_id($menu_position->id);
        $this->add_menu_position($menu_position);
    }
}