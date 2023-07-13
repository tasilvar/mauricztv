<?php


declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\service_editor\metaboxes;

use bpmj\wpidea\admin\pages\service_editor\Service_Editor_Page_Checker;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;
use BPMJ_EDD_Sell_Discount_Product_Metabox;
use WP_Post;

class Service_Metaboxes_Renderer implements Interface_Initiable
{
    private const ADMIN_BODY_CLASSES = 'service-editor';

    private Service_Editor_Page_Checker $page_checker;
    private Interface_Actions $actions;
    private Interface_Filters $filters;
    private Current_Request $current_request;

    public function __construct(
        Service_Editor_Page_Checker $page_checker,
        Interface_Actions $actions,
        Interface_Filters $filters,
        Current_Request $current_request
    ) {
        $this->page_checker = $page_checker;
        $this->actions = $actions;
        $this->filters = $filters;
        $this->current_request = $current_request;
    }

    public function init(): void
    {
        if (!$this->is_a_service_edit_page()) {
            return;
        }

        $this->actions->add('add_meta_boxes', [$this, 'setup_metaboxes'], 1);
    }

    public function setup_metaboxes(): void
    {
        $this->remove_edd_actions();
        $this->add_body_classes();
    }

    protected function remove_edd_actions(): void
    {
        $this->actions->remove('add_meta_boxes', 'edd_add_download_meta_box');

        $this->actions->remove('add_meta_boxes', array(BPMJ_EDD_Sell_Discount_Product_Metabox::instance(), 'add'));

        remove_meta_box('bpmj_eddcm_product_settings', 'download', 'side');
    }

    private function add_body_classes(): void
    {
        $this->filters->add('admin_body_class', static function (string $classes) {
            $classes .= ' ' . self::ADMIN_BODY_CLASSES;
            
            return $classes;
        });
    }

    private function is_a_service_edit_page(): bool
    {
        $post_id = $this->current_request->get_query_arg('post');
        $post_id = is_numeric($post_id) ? (int)$post_id : null;
        $action = $this->current_request->get_query_arg('action');
        $post_type = get_post_type($post_id);

        return $this->page_checker->is_service_offer_edit_page(
            $post_id,
            $post_type ?: null,
            $action
        );
    }

}