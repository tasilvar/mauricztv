<?php

namespace bpmj\wpidea\admin\pages;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\digital_products\model\Digital_Product_ID;
use bpmj\wpidea\digital_products\repository\Interface_Digital_Product_Repository;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\service\model\Service_ID;
use bpmj\wpidea\service\repository\Interface_Service_Repository;
use bpmj\wpidea\settings\Interface_Settings;

class Admin_Pages_Redirector implements Interface_Initiable
{
    private const SCREEN_ID = 'edit-tests';

    private Interface_Redirector $redirector;
    private Current_Request $current_request;
    private Interface_Url_Generator $url_generator;
    private Interface_Actions $actions;
    private Interface_Digital_Product_Repository $digital_product_repository;
    private Interface_Settings $settings;
    private Interface_Service_Repository $service_repository;

    public function __construct(
        Interface_Redirector $redirector,
        Current_Request $current_request,
        Interface_Url_Generator $url_generator,
        Interface_Actions $actions,
        Interface_Digital_Product_Repository $digital_product_repository,
        Interface_Settings $settings,
        Interface_Service_Repository $service_repository
    ) {
        $this->redirector = $redirector;
        $this->current_request = $current_request;
        $this->url_generator = $url_generator;
        $this->actions = $actions;
        $this->digital_product_repository = $digital_product_repository;
        $this->settings = $settings;
        $this->service_repository = $service_repository;
    }

    public function init(): void
    {
        $this->actions->add('admin_init', function () {
            $this->handle_redirect();
        });
        $this->actions->add('current_screen', [$this, 'redirect_quizzes_page']);
    }

    public function handle_redirect(): void
    {
        $this->redirect_bundles_page();
        $this->redirect_products_page();
        $this->redirect_services_page();
    }

    public function redirect_quizzes_page(\WP_Screen $screen): void
    {
        if (self::SCREEN_ID !== $screen->id) {
            return;
        }

        $this->redirect_to_the_quizzes_page();
    }

    private function redirect_bundles_page(): void
    {
        if (
            $this->is_old_bundles_page() &&
            $this->edited_post_was_of_bundle_subtype()
        ) {
            $this->redirect_to_courses_and_bundles_page();
        }
    }

    private function redirect_products_page(): void
    {
        if (
            $this->is_old_products_page() &&
            $this->is_digital_products_feature_enabled() &&
            $this->edited_post_was_of_digital_product_type()
        ) {
            $this->redirect_to_new_products_page();
        }
    }

    private function redirect_services_page(): void
    {
        if (
            $this->is_old_services_page() &&
            $this->is_services_feature_enabled() &&
            $this->edited_post_was_of_service_type()
        ) {
            $this->redirect_to_new_services_page();
        }
    }

    private function is_old_bundles_page(): bool
    {
        global $pagenow;

        return
            ($this->current_request->get_query_arg('post_type') === 'download')
            && $pagenow === 'edit.php';
    }

    private function redirect_to_courses_and_bundles_page(): void
    {
        $this->redirector->redirect(
            $this->get_courses_and_bundles_page()
        );
    }

    private function get_courses_and_bundles_page(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php?page=wp-idea-courses');
    }

    private function edited_post_was_of_bundle_subtype(): bool
    {
        $referer = $this->current_request->get_referer();
        if (!$referer) {
            return false;
        }
        $parts = parse_url($referer);
        parse_str($parts['query'], $query);
        $post_id = $query['post'] ?? false;

        if (!$post_id) {
            return false;
        }

        $subtype = get_post_meta($post_id, '_eddcm_subtype', true);

        return $subtype === 'bundle';
    }


    private function is_old_products_page(): bool
    {
        global $pagenow;

        return
            ($this->current_request->get_query_arg('post_type') === 'download')
            && $pagenow === 'edit.php';
    }

    private function redirect_to_new_products_page(): void
    {
        $this->redirector->redirect(
            $this->get_new_products_page_url()
        );
    }

    private function get_new_products_page_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php?page=wp-idea-digital-products');
    }

    private function is_digital_products_feature_enabled(): bool
    {
        return (bool)$this->settings->get(Settings_Const::DIGITAL_PRODUCTS_ENABLED);
    }

    private function edited_post_was_of_digital_product_type(): bool
    {
        $referer = $this->current_request->get_referer();
        if (!$referer) {
            return false;
        }
        $parts = parse_url($referer);
        parse_str($parts['query'], $query);
        $post_id = $query['post'] ?? false;

        if (!$post_id) {
            return false;
        }

        return (bool)$this->digital_product_repository->find(new Digital_Product_ID((int)$post_id));
    }


    private function is_old_services_page(): bool
    {
        global $pagenow;

        return
            ($this->current_request->get_query_arg('post_type') === 'download')
            && $pagenow === 'edit.php';
    }

    private function redirect_to_new_services_page(): void
    {
        $this->redirector->redirect(
            $this->get_new_services_page_url()
        );
    }

    private function get_new_services_page_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php?page=wp-idea-services');
    }

    private function is_services_feature_enabled(): bool
    {
        return (bool)$this->settings->get(Settings_Const::SERVICES_ENABLED);
    }

    private function edited_post_was_of_service_type(): bool
    {
        $referer = $this->current_request->get_referer();
        if (!$referer) {
            return false;
        }
        $parts = parse_url($referer);
        parse_str($parts['query'], $query);
        $post_id = $query['post'] ?? false;

        if (!$post_id) {
            return false;
        }

        return (bool)$this->service_repository->find(new Service_ID((int)$post_id));
    }

    private function redirect_to_the_quizzes_page(): void
    {
        $url = $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::QUIZZES
        ]);

        wp_safe_redirect($url);
        exit;
    }

}