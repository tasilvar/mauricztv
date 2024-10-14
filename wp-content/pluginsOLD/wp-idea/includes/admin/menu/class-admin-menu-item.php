<?php
namespace bpmj\wpidea\admin\menu;

class Admin_Menu_Item
{
    protected $page_title;

    protected $cap;

    protected $menu_slug;

    protected $menu_title;

    protected $class;

    protected $id;

    protected $icon;

    protected $children = [];

    protected $has_parent = false;

    public function __construct(
        string $page_title,
        string $cap,
        string $menu_slug,
        string $menu_title = '',
        string $class = '',
        string $id = '',
        string $icon = '',
        bool $has_parent = false
    ) {
        $this->page_title = $page_title;
        $this->cap = $cap;
        $this->menu_slug = $menu_slug;
        $this->menu_title = $menu_title;
        $this->class = $class;
        $this->id = $id;
        $this->icon = $icon;
        $this->has_parent = $has_parent;
    }

    public static function from_wp_menu_item(array $wp_menu_item): self
    {
        $page_title = $wp_menu_item[0] ?? '';
        $cap = $wp_menu_item[1] ?? '';
        $menu_slug = $wp_menu_item[2] ?? '';
        $menu_title = $wp_menu_item[3] ?? '';
        $class = $wp_menu_item[4] ?? '';
        $id = $wp_menu_item[5] ?? '';
        $icon = $wp_menu_item[6] ?? '';
        
        return new self(
            $page_title,
            $cap,
            $menu_slug,
            $menu_title,
            $class,
            $id,
            $icon
        );
    }

    public static function from_wp_submenu_item(array $wp_menu_item): self
    {
        $page_title = $wp_menu_item[0] ?? '';
        $cap = $wp_menu_item[1] ?? '';
        $menu_slug = $wp_menu_item[2] ?? '';
        $menu_title = $wp_menu_item[3] ?? '';
        
        return new self(
            $page_title,
            $cap,
            $menu_slug,
            $menu_title
        );
    }

    public static function construct_separator_item(): self
    {
        return new self(
            '',
            'read',
            "separator",
            "",
            "wp-menu-separator wpi-custom-menu-separator"
        );
    }

    public function get_page_title(): string
    {
        return $this->page_title;
    }

    public function get_cap(): string
    {
        return $this->cap;
    }

    public function get_menu_slug(): string
    {
        return $this->menu_slug;
    }

    public function get_menu_slug_fixed_for_use_in_wp_menu(): string
    {
        $slug = $this->menu_slug;
        $former_wpidea_submenu_items_needing_fix = [
            Admin_Menu_Item_Slug::QUIZZES,
            Admin_Menu_Item_Slug::CERTIFICATES,
            Admin_Menu_Item_Slug::HELP,
            Admin_Menu_Item_Slug::VIDEOS,
            Admin_Menu_Item_Slug::VIDEO_UPLOADER,
            Admin_Menu_Item_Slug::VIDEO_SETTINGS,
            Admin_Menu_Item_Slug::SETTINGS,
            Admin_Menu_Item_Slug::TOOLS,
            Admin_Menu_Item_Slug::PAYMENTS_HISTORY,
            Admin_Menu_Item_Slug::DISCOUNT_CODES,
            Admin_Menu_Item_Slug::AFFILIATE_PROGRAM,
            Admin_Menu_Item_Slug::AFFILIATE_PROGRAM_REDIRECTIONS,
            Admin_Menu_Item_Slug::AFFILIATE_PROGRAM_PARTNERS,
            Admin_Menu_Item_Slug::CLIENTS,
            Admin_Menu_Item_Slug::TEMPLATE_GROUPS,
            Admin_Menu_Item_Slug::NOTIFICATIONS,
            Admin_Menu_Item_Slug::LOGS,
            Admin_Menu_Item_Slug::WEBHOOKS,
            Admin_Menu_Item_Slug::COURSES,
            Admin_Menu_Item_Slug::STUDENTS,
            Admin_Menu_Item_Slug::PACKAGES,
            Admin_Menu_Item_Slug::DIGITAL_PRODUCTS,
            Admin_Menu_Item_Slug::PURCHASE_REDIRECTIONS,
            Admin_Menu_Item_Slug::SERVICES,
            Admin_Menu_Item_Slug::USERS,
            Admin_Menu_Item_Slug::USERS_PROXY,
            Admin_Menu_Item_Slug::EDITOR_SERVICE,
            Admin_Menu_Item_Slug::EDITOR_DIGITAL_PRODUCT,
            Admin_Menu_Item_Slug::INCREASING_SALES,
            Admin_Menu_Item_Slug::EDITOR_COURSE,
            Admin_Menu_Item_Slug::PRICE_HISTORY,
            Admin_Menu_Item_Slug::PHYSICAL_PRODUCTS,
            Admin_Menu_Item_Slug::OPINIONS,
            Admin_Menu_Item_Slug::EDITOR_QUIZ,
            Admin_Menu_Item_Slug::REPORTS,
        ];

        if (in_array($slug, $former_wpidea_submenu_items_needing_fix, true)) {
            $slug = 'admin.php?page=' . $slug;
        }

        return $slug;
    }

    public function get_menu_title(): string
    {
        return $this->menu_title;
    }

    public function set_menu_title(string $title): self
    {
        $this->menu_title = $title;

        return $this;
    }

    public function set_title(string $title): self
    {
        $this->menu_title = $title;
        $this->page_title = $title;

        return $this;
    }

    public function get_class(): string
    {
        if($this->has_parent()) $this->class = '';

        if(empty($this->class)){
            $is_top_level_item = !$this->has_parent();
            if ($is_top_level_item) {
                $this->class = 'menu-top toplevel_page_' . $this->get_menu_slug();
            }
        } 

        if($this->is_current()){
            if ($this->has_children()) {
                $this->class .= ' wp-has-current-submenu wp-menu-open';
            }
    
            if(!$this->has_children()) {
                $this->class .= ' current';
            }
        }

        return $this->class;
    }

    protected function is_current()
    {
        if(isset($_GET['page']) && $_GET['page'] === $this->get_menu_slug()) {
            return true;
        }

        if($this->request_uri_ends_with($this->get_menu_slug())) return true;

        foreach ($this->get_children() as $key => $child) {
            if($this->request_uri_ends_with($child->get_menu_slug())) return true;
        }

        $dashboard_menu_slug = 'admin.php?page=wpidea-dashboard';
        $is_dashboard_page = $this->request_uri_ends_with($dashboard_menu_slug) && ($this->get_menu_slug() == Admin_Menu_Item_Slug::DASHBOARD);
        if($is_dashboard_page) return true;

        return false;
    }

    protected function request_uri_ends_with($needle) {
        $haystack = $_SERVER['REQUEST_URI'];
        $needle = str_replace('&amp;', '&', $needle);

        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }

    public function get_id(): string
    {
        if(!$this->has_children()) return $this->id;

        if(empty($this->id)) return 'toplevel_page_' . $this->get_menu_slug();

        return $this->id;
    }

    public function get_icon(): string
    {
        return $this->icon;
    }

    public function set_icon($icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function set_has_parent(bool $has_parent): self
    {
        $this->has_parent = $has_parent;

        return $this;
    }

    public function has_parent(): bool
    {
        return $this->has_parent;
    }

    public function add_child(Admin_Menu_Item $child_menu_item): self
    {
        $this->set_has_parent(false);

        $child_menu_item = clone $child_menu_item;
        $child_menu_item->remove_children();
        $child_menu_item->set_has_parent(true);

        $this->children[] = $child_menu_item;

        return $this;
    }

    public function remove_children(): self
    {
        $this->children = [];

        return $this;
    }

    public function has_children(): bool
    {
        return !empty($this->children);
    }

    public function get_children(): array
    {
        return $this->children;
    }
}