<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\modules\opinions\api\Opinions_API_Static_Helper;
use bpmj\wpidea\modules\opinions\core\collections\Opinion_Collection;
use bpmj\wpidea\templates_system\admin\blocks\attributes\Number_Attribute;
use bpmj\wpidea\templates_system\admin\blocks\attributes\Select_Attribute;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\translator\Interface_Translator_Aware;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\view\Interface_View_Provider_Aware;

class Opinions_Block extends Block implements Interface_View_Provider_Aware, Interface_Translator_Aware
{
    const BLOCK_NAME = 'wpi/opinions';

    private const OPINIONS_PAGE_PARAM_NAME = 'opinions_page';
    private const ATTR_ITEMS_PER_PAGE = 'items_per_page';
    private const ATTR_SHOW_OPINIONS_IN_COLUMN = 'show_opinions_in_column';
    private const DEFAULT_ITEMS_PER_PAGE = 5;
    private const DEFAULT_SHOW_OPINIONS_IN_COLUMN = 1;

    private ?Interface_View_Provider $view_provider = null;
    private ?Interface_Translator $translator = null;

    public function __construct() {
        parent::__construct();

        $this->title = Translator_Static_Helper::translate('blocks.opinions.title');
    }

    protected function setup_attributes()
    {
        $this->setup_show_opinions_in_column_option_attribute();
        $this->setup_items_on_page_attribute();
    }

    public function get_content_to_render($atts)
    {
        if(!Opinions_API_Static_Helper::is_enabled()){
            return '';
        }

        $product_id = $this->get_product_id();

        if(!$product_id){
            return '';
        }

        $per_page = (int)$atts[self::ATTR_ITEMS_PER_PAGE];
        $show_opinions_in_column = (int)$atts[self::ATTR_SHOW_OPINIONS_IN_COLUMN];

        $count_opinions = $this->get_count_opinions_by_product_id($product_id);
        $total_pages  = ceil($count_opinions / $per_page);
        $page = $this->get_opinions_page((int)$total_pages);

        $results = $this->get_opinions_by_product_id($product_id, $per_page, $page);

        return $this->view_provider->get($this->get_template_path_base() . '/opinions/opinions', [
            'translator' => $this->translator,
            'view' => $this->view_provider,
            'template_path_base' => $this->get_template_path_base(),
            'results' => $results,
            'total_pages' => (int)$total_pages,
            'page' => $page,
            'opinions_page_param_name' => self::OPINIONS_PAGE_PARAM_NAME,
            'show_opinions_in_column' => $show_opinions_in_column
        ]);
    }

    public function set_view_provider(Interface_View_Provider $view_provider): void
    {
        $this->view_provider = $view_provider;
    }

    public function set_translator(Interface_Translator $translator): void
    {
        $this->translator = $translator;
    }

    private function setup_items_on_page_attribute(): void
    {
        $attr = new Number_Attribute(
            self::ATTR_ITEMS_PER_PAGE,
            Translator_Static_Helper::translate('blocks.opinions.items_page'),
            Translator_Static_Helper::translate('blocks.opinions.items_page.desc'),
            self::DEFAULT_ITEMS_PER_PAGE
        );
        $attr->set_min_value(1, Translator_Static_Helper::translate('blocks.opinions.items_page.min'));

        $this->add_attribute($attr);
    }

    private function setup_show_opinions_in_column_option_attribute(): void
    {
        $attr = new Select_Attribute(
            self::ATTR_SHOW_OPINIONS_IN_COLUMN,
            Translator_Static_Helper::translate('blocks.opinions.column.label'),
            Translator_Static_Helper::translate('blocks.opinions.column.desc'),
            self::DEFAULT_SHOW_OPINIONS_IN_COLUMN
        );

        $attr->add_option(Translator_Static_Helper::translate('blocks.opinions.column.options1'), 1);
        $attr->add_option(Translator_Static_Helper::translate('blocks.opinions.column.options2'), 2);

        $this->add_attribute($attr);
    }

    private function get_opinions_by_product_id(int $product_id, int $per_page = 0, int $page = 1): Opinion_Collection
    {
        return Opinions_API_Static_Helper::get_opinions_by_product_id($product_id, $per_page, $page);
    }

    private function get_count_opinions_by_product_id(int $product_id): int
    {
        return Opinions_API_Static_Helper::get_count_opinions_by_product_id($product_id);
    }

    private function get_product_id(): ?int
    {
        return $this->get_current_request()->get_current_page_id();
    }

    private function get_opinions_page(int $total_pages): int
    {
        $page = $this->get_current_request()->get_query_arg(self::OPINIONS_PAGE_PARAM_NAME) ?? '';

        $page = (!empty($page) && is_numeric($page) && ($page > 0)) ? (int)$page : 1;

        return ($page < $total_pages) ? $page : $total_pages;
    }

    private function get_current_request(): Current_Request
    {
        return Current_Request::create();
    }
}
