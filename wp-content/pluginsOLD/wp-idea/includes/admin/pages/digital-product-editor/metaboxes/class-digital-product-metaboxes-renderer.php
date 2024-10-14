<?php
/**
 * Ta klasa odpowiada za zapis metaboxów w panelu edycji produktu cyfrowego.
 * Jest w dużej mierze adaptacją (i częściowo kopią) kodu z klasy bpmj\wpidea\admin\Edit_Course.
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\digital_product_editor\metaboxes;

use bpmj\wpidea\admin\pages\digital_product_editor\Digital_Product_Editor_Page_Checker;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;
use WP_Post;
use BPMJ_EDD_Sell_Discount_Product_Metabox;

class Digital_Product_Metaboxes_Renderer implements Interface_Initiable
{
    private const ADMIN_BODY_CLASSES = 'digital-product-editor';

    private Digital_Product_Editor_Page_Checker $page_checker;
    private Interface_Actions $actions;
    private Interface_Filters $filters;


    public function __construct(
        Digital_Product_Editor_Page_Checker $page_checker,
        Interface_Actions $actions,
        Interface_Filters $filters
    ) {
        $this->page_checker = $page_checker;
        $this->actions = $actions;
        $this->filters = $filters;
    }

    public function init(): void
    {
        $this->actions->add('add_meta_boxes', [$this, 'setup_metaboxes'], 1);
    }

    public function setup_metaboxes(): void
    {
        global $post;
        $post_id = $post->ID ?? null;

        if (!$this->page_checker->is_digital_product_offer_edit_page(
            $post_id ? (int)$post_id : null,
            $post->post_type ?? null
        )) {
            return;
        }

        $this->actions->remove('add_meta_boxes', 'edd_add_download_meta_box');
        
        $this->actions->remove('add_meta_boxes', [ BPMJ_EDD_Sell_Discount_Product_Metabox::instance(), 'add' ]);

        $this->add_body_classes();

        remove_meta_box('bpmj_eddcm_product_settings', 'download', 'side');
    }

    private function add_body_classes(): void
    {
        $this->filters->add('admin_body_class', static function (string $classes) {
            $classes .= ' ' . self::ADMIN_BODY_CLASSES;


            return $classes;
        });
    }
}