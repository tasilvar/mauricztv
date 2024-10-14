<?php
namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\templates_system\admin\blocks\attributes\Number_Attribute;
use bpmj\wpidea\templates_system\admin\blocks\attributes\Select_Attribute;
use bpmj\wpidea\View;

class Products_Block extends Block
{
    const BLOCK_NAME = 'wpi/courses-list';

    const SHOW_ONLY_MY_COURSES_ENABLED_CHECKED_VALUE = 'enabled-checked';
    const SHOW_ONLY_MY_COURSES_ENABLED_UNCHECKED_VALUE = 'enabled-unchecked';
    const SHOW_ONLY_MY_COURSES_DISABLED_VALUE = 'disabled';

    private const ATTR_ITEMS_PER_PAGE = 'items_per_page';
    private const ATTR_ENABLE_SHOW_ONLY_MY_COURSES = 'enable_only_show_my_courses';

    public function __construct() {
        parent::__construct();

        $this->title = Translator_Static_Helper::translate('blocks.products.title');
    }

    protected function setup_attributes()
    {
        $this->setup_items_on_page_attribute();
        
        $courses_functionality_enabled = LMS_Settings::get_option(Settings_Const::COURSES_ENABLED) ?? true;
        
        if($courses_functionality_enabled) {
        	$this->setup_show_only_my_courses_option_attribute();
        }
    }

    public function get_content_to_render($atts)
    {
        global $wp_query;

        // attributes
        $per_page = $atts[self::ATTR_ITEMS_PER_PAGE];


        //@todo: wyciagnac te wszystkie odpytania settingsów i dodac helper do paginacji
        $default_view   = LMS_Settings::get_option('default_view', 'grid');
        $show_pagination= LMS_Settings::get_option('list_pagination') !== 'no';
        $page           = !empty( $wp_query->query['paged'] ) ? (int) $wp_query->query['paged'] : 1;
        $enable_show_only_my_courses_value = $this->app_view_is_active() ? self::SHOW_ONLY_MY_COURSES_ENABLED_CHECKED_VALUE : ($atts[self::ATTR_ENABLE_SHOW_ONLY_MY_COURSES] ?? 'disabled');
        $order_by = LMS_Settings::get_option('list_orderby');
        $order = LMS_Settings::get_option('list_order');

        if(is_user_logged_in() && $this->show_my_courses_only( $enable_show_only_my_courses_value )){
            
            if($this->is_category_page()){
                $products_and_count = WPI_API()->products->get_products_by_category_name($this->get_archive_name(), get_current_user_id(), $per_page, $page, $order_by, $order);
            } elseif($this->is_tag_page()) {
                $products_and_count = WPI_API()->products->get_products_by_tag_name($this->get_archive_name(), get_current_user_id(), $per_page, $page, $order_by, $order);
            } else {
                $products_and_count = WPI_API()->products->get_all_products_and_count_by_user_id(get_current_user_id(), $per_page, $page, $order_by, $order); 
            }

            $count_products = $products_and_count['count'];
            $products       = $products_and_count['products'];

        } else {
           
            if($this->is_category_page()){
                $products_and_count = WPI_API()->products->get_products_by_category_name($this->get_archive_name(), null, $per_page, $page, $order_by, $order);
               
                $count_products = $products_and_count['count'];
                $products       = $products_and_count['products']; 
            } elseif($this->is_tag_page()) {
                $products_and_count = WPI_API()->products->get_products_by_tag_name($this->get_archive_name(), null, $per_page, $page, $order_by, $order);
               
                $count_products = $products_and_count['count'];
                $products       = $products_and_count['products']; 
            } else {
                $count_products = WPI_API()->products->count_all();
                $products       = WPI_API()->products->all($per_page, $page, $order_by, $order);
            }
            
        }

        $total_pages    = ceil($count_products / $per_page);

        if(empty($products->count())) return View::get("{$this->get_template_path_base()}/products/list/list-empty");

        return View::get("{$this->get_template_path_base()}/products/list/list", [
            'products' => $products ,
            'default_view' => $default_view,
            'total' => $count_products,
            'total_pages' => $total_pages,
            'per_page' => $per_page,
            'page' => $page,
            'show_pagination' => $show_pagination && $total_pages > 1,
            'show_only_my_courses_is_checked' => $this->show_my_courses_only( $enable_show_only_my_courses_value ),
            'show_only_my_courses' => $this->show_only_my_courses($enable_show_only_my_courses_value),
            'description_category_page' => $this->get_archive_description_for_category_page()
        ]);
    }

    private function show_my_courses_only( string $enable_show_only_my_courses_value ): bool
    {
        if (isset($_GET['show_my_courses']) && $_GET['show_my_courses'] === '1') {
            return true;
        }

        if ( ! isset( $_GET['show_my_courses'] ) && self::SHOW_ONLY_MY_COURSES_ENABLED_CHECKED_VALUE === $enable_show_only_my_courses_value ) {
            return true;
        }

        return  false;
    }

    private function setup_items_on_page_attribute(): void
    {
        $attr = new Number_Attribute(
            self::ATTR_ITEMS_PER_PAGE,
            Translator_Static_Helper::translate('blocks.products.items_page'),
            Translator_Static_Helper::translate('blocks.products.items_page.desc'),
            LMS_Settings::get_option('list_number', 9)
        );
        $attr->set_min_value(1, __( 'The minimum number of courses on the site is 1.', BPMJ_EDDCM_DOMAIN ));

        $this->add_attribute($attr);
    }

    private function setup_show_only_my_courses_option_attribute(): void
    {
        $attr = new Select_Attribute(
            self::ATTR_ENABLE_SHOW_ONLY_MY_COURSES,
            __( 'Enable "Show only my courses" button', BPMJ_EDDCM_DOMAIN ),
            __( 'Shows only bought courses for logged in user', BPMJ_EDDCM_DOMAIN ),
            self::SHOW_ONLY_MY_COURSES_DISABLED_VALUE
        );
        $attr->add_option(__( 'Enabled (default checked)', BPMJ_EDDCM_DOMAIN ), self::SHOW_ONLY_MY_COURSES_ENABLED_CHECKED_VALUE);
        $attr->add_option(__( 'Enabled (default unchecked)', BPMJ_EDDCM_DOMAIN ), self::SHOW_ONLY_MY_COURSES_ENABLED_UNCHECKED_VALUE);
        $attr->add_option(__( 'Disabled', BPMJ_EDDCM_DOMAIN ), self::SHOW_ONLY_MY_COURSES_DISABLED_VALUE);

        $this->add_attribute($attr);
    }

    private function is_category_page(): bool
    {
       return is_tax('download_category');
    }

    private function is_tag_page(): bool
    {
       return is_tax('download_tag');
    }

    private function get_archive_name(): string
    {
       return single_tag_title('', false);
    }

    private function get_archive_description_for_category_page(): string
    {
        if (!$this->is_category_page()) {
            return '';
        }

        return get_the_archive_description();
    }

    public function show_only_my_courses(string $enable_show_only_my_courses_value): bool
    {
        if ($this->app_view_is_active()) {
            return false;
        }

        return is_user_logged_in() &&
            (
                self::SHOW_ONLY_MY_COURSES_ENABLED_CHECKED_VALUE === $enable_show_only_my_courses_value ||
                self::SHOW_ONLY_MY_COURSES_ENABLED_UNCHECKED_VALUE === $enable_show_only_my_courses_value
            );
    }

    private function app_view_is_active(): bool
    {
        return App_View_API_Static_Helper::is_active();
    }
}
