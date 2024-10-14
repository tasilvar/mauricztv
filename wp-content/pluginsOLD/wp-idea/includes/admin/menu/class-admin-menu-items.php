<?php
namespace bpmj\wpidea\admin\menu;

class Admin_Menu_Items
{
    /**
     * @var Admin_Menu_Items_Collection
     */
    protected $flat_items_list;

    /**
     * @var Admin_Menu_Items_Collection
     */
    protected $structurized_items_list;

    public function __construct() {
        $this->flat_items_list = new Admin_Menu_Items_Collection();
        $this->structurized_items_list = new Admin_Menu_Items_Collection();

        $this->init_items();
    }

    public function get_flat_items_list(): Admin_Menu_Items_Collection
    {
        return $this->flat_items_list;
    }

    public function get_structurized_items_list(): Admin_Menu_Items_Collection
    {
        return $this->structurized_items_list;
    }

    public function add_item(?Admin_Menu_Item $item, $children = []): self
    {
        if (is_null($item)) return $this;

        foreach ($children as $key => $child) {
            if(!$child instanceof Admin_Menu_Item) continue;

            $item->add_child($child);
        }

        $this->structurized_items_list->append($item);

        return $this;
    }

    public function add_separator(): self
    {
        $this->structurized_items_list->append(Admin_Menu_Item::construct_separator_item());

        return $this;
    }

    public function find_by_slug(string $slug): ?Admin_Menu_Item
    {
        $matching_admin_menu_item = $this->get_flat_items_list()->find_item_by_slug($slug);

        return $matching_admin_menu_item ? clone $matching_admin_menu_item : null;
    }

    public function find_by_slug_and_transform(string $slug, ?callable $transform_callback = null): ?Admin_Menu_Item
    {
        $item = $this->find_by_slug($slug);
        if (!$item) {
            return null;
        }

        if ($transform_callback) {
            $transform_callback($item);
        }

        if ($item instanceof Admin_Menu_Item) {
            return $item;
        } else {
            throw new \Exception("Callable $transform_callback must return instance of Admin_Menu_Item.");
        }
    }

    public function convert_structure_to_wp_menu_array(): array
    {
        $wp_menu_items = [];

        foreach ($this->get_structurized_items_list() as $key => $item) {
            $wp_menu_items[] = [
                $item->get_page_title(),
                $item->get_cap(),
                $item->get_menu_slug_fixed_for_use_in_wp_menu(),
                $item->get_menu_title(),
                $item->get_class(),
                $item->get_id(),
                $item->get_icon()
            ];
        }

        return $wp_menu_items;
    }

    public function convert_structure_to_wp_submenu_array(): array
    {
        $wp_submenu_items = [];

        foreach ($this->get_structurized_items_list() as $key => $item) {
            if(!$item->has_children()) continue;

            foreach ($item->get_children() as $child_key => $child_item) {
                $wp_submenu_items[$item->get_menu_slug_fixed_for_use_in_wp_menu()][] = [
                    $child_item->get_page_title(),
                    $child_item->get_cap(),
                    $child_item->get_menu_slug_fixed_for_use_in_wp_menu(),
                    $child_item->get_menu_title(),
                    $child_item->get_class(),
                ];
            }
        }

        return $wp_submenu_items;
    }

    protected function init_items(): void
    {
        global $menu, $submenu;

        foreach ($menu as $key => $wp_menu_item) {
            $menu_item = Admin_Menu_Item::from_wp_menu_item($wp_menu_item);
            $this->add_menu_item_to_the_flat_list($menu_item);
        }

        foreach ($submenu as $parent_menu_slug => $wp_submenu_items) {
            $parent_item = $this->find_by_slug($parent_menu_slug);

            if (empty($parent_item)) continue;

            foreach ($wp_submenu_items as $key => $submenu_item) {
                if (empty($submenu_item)) continue;

                $this->add_menu_item_to_the_flat_list(Admin_Menu_Item::from_wp_submenu_item($submenu_item));
            }
        }
    }

    protected function add_menu_item_to_the_flat_list(Admin_Menu_Item $item): self
    {
        $this->flat_items_list->append($item);

        return $this;
    }
}
