<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\courses\infrastructure\repositories;

use bpmj\wpidea\Courses;
use bpmj\wpidea\courses\core\entities\Course_With_Product;
use bpmj\wpidea\courses\core\repositories\Interface_Course_With_Product_Repository;
use bpmj\wpidea\learning\course\{Course_ID, Page_ID};
use bpmj\wpidea\sales\product\acl\Product_Sale_Dates_ACL;
use bpmj\wpidea\sales\product\Flat_Rate_Tax_Symbol_Helper;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\wolverine\event\Events;
use DateTime;

class Course_With_Product_Repository implements Interface_Course_With_Product_Repository
{
    private Courses $courses;
    private Interface_Product_Repository $product_repository;
    private Interface_Translator $translator;
    private Product_Sale_Dates_ACL $sale_dates_ACL;

    public function __construct(
        Courses $courses,
        Interface_Product_Repository $product_repository,
        Interface_Translator $translator,
        Product_Sale_Dates_ACL $sale_dates_ACL
    ) {
        $this->courses = $courses;
        $this->product_repository = $product_repository;
        $this->translator = $translator;
        $this->sale_dates_ACL = $sale_dates_ACL;
    }

    public function save(Course_With_Product $course_with_product): Course_ID
    {
        return $this->is_model_new_entity($course_with_product)
            ? $this->create($course_with_product)
            : $this->update($course_with_product);
    }

    private function is_model_new_entity(Course_With_Product $course_with_product): bool
    {
        return is_null($course_with_product->get_id());
    }

    private function create(Course_With_Product $course_with_product): Course_ID
    {
        $product = $this->product_repository->find(
            $course_with_product->get_product_id()
        );

        $course_id = $this->courses->createCPT();
        $form = [
            'last_section' => 'on',
            'banner' => '',
            'title' => $product->get_name()->get_value(),
            'content' => '',
            'variable_pricing' => '',
            'price' => $product->get_price()->get_value(),
            'sale_price' => '',
            'access_time' => '',
            'access_time_unit' => 'minutes',
            'purchase_limit' => '',
            'variable_prices' => [],
            'drip_value' => '',
            'drip_unit' => 'minutes',
            'access_start' => '',
            'access_start_hh' => '00',
            'access_start_mm' => '00',
            'recurring_payments_enabled' => '',
            'bpmj_wpidea' => [
                'flat_rate_tax_symbol' => ''
            ],
            'invoices_vat_rate' => '',
            '_edd_ipresso' => '',
            '_edd_ipresso_unsubscribe' => '',
            '_edd_activecampaign_tags' => '',
            '_edd_activecampaign_tags_unsubscribe' => '',
            '_bpmj_edd_sm_tags' => '',
            'cpt_id' => $course_id,
            'cloned_from_id' => $course_with_product->get_cloned_from_id() ? $course_with_product->get_cloned_from_id()->to_int() : null,
            'redirect_page' => $course_with_product->get_redirect_page() ?? '',
            'redirect_url' => $course_with_product->get_redirect_url() ?? '',
            'bpmj_eddcm_module' => [],
            'sales_disabled' => $product->sales_disabled() ? 'on' : 'off',
            'hide_from_lists' => $product->hide_from_list() ? 'on' : 'off'
        ];

        $product_id = $product->get_id()->to_int();

        $form = $this->courses->create_pages($form, $product_id);

        $form['product_id'] = $product_id;
        $this->courses->add_cpt($form, 'publish');

        $this->courses->drip($course_id);

        Events::trigger(Courses::EVENT_COURSE_CREATED, ['course_id' => $course_id]);
        return new Course_ID($course_id);
    }

    private function update(Course_With_Product $course_with_product): Course_ID
    {
        $product = $this->product_repository->find(
            $course_with_product->get_product_id()
        );

        $this->update_page_meta($product, $course_with_product);

        $this->update_course_meta($product, $course_with_product);

        $this->update_course_categories($product, $course_with_product);

        $this->update_course_tags($product, $course_with_product);

        $this->sale_dates_ACL->check_sale_dates();

        $this->after_page_meta_updated($course_with_product);

        return $course_with_product->get_id();
    }

    private function after_page_meta_updated(Course_With_Product $course_with_product): void
    {
        $panel_link = get_post_meta($course_with_product->get_product_id()->to_int(), 'edd_download_files', true);

        if (!  is_array($panel_link)) {
            $panel_link = [];
        }

        $id = $course_with_product->get_page_id()->to_int();

        $panel_link[$id] = [
            'index' => count($panel_link) + 1,
            'name' => $this->translator->translate('templates.checkout_confirmation.course_panel'),
            'file' => get_permalink($id),
            'attachment_id' => 0,
            'condition' => 'all',
        ];

        update_post_meta($course_with_product->get_product_id()->to_int(), 'edd_download_files', $panel_link);
    }

    public function find(Course_ID $course_id): ?Course_With_Product
    {
        $product_id = $this->courses->get_product_by_course($course_id->to_int());
        $course = $this->courses->get_course_by_product($product_id);

        $post = get_post($course_id->to_int());

        $page_id = get_post_meta($course_id->to_int(), 'course_id', true);
        $redirect_url = get_post_meta($course_id->to_int(), 'redirect_url', true);
        $redirect_page = get_post_meta($course_id->to_int(), 'redirect_page', true);
        $certificate_template_id = get_post_meta($course_id->to_int(), 'certificate_template_id', true);
        $certificate_template_id = !empty($certificate_template_id) ? $certificate_template_id : null;

        $drip_value = get_post_meta($course_id->to_int(), 'drip_value', true);
        $drip_value = !empty($drip_value) ? (int)$drip_value : null;

        $drip_unit = get_post_meta($course_id->to_int(), 'drip_unit', true);

        $post_date = new DateTime($post->post_date);
        $post_date_gmt = new DateTime($post->post_date_gmt);

        return Course_With_Product::create(
            new Course_ID((int)$course->ID),
            new Product_ID((int)$product_id),
            new Page_ID((int)$page_id),
            $redirect_page,
            $redirect_url,
            $certificate_template_id,
            $drip_value ?? null,
            $drip_unit,
            $post_date,
            $post_date_gmt
        );
    }

    public function get_course_id_by_product(Product_ID $product_id): ?Course_ID
    {
        $course = $this->courses->get_course_by_product($product_id->to_int());

        if(!$course) {
            return null;
        }

        return new Course_ID($course->ID);
    }

    private function update_course_meta(Product $product, Course_With_Product $course_with_product): void
    {
        $variable_prices = $product->get_variable_prices();

        $args = [
            'ID' => $course_with_product->get_id()->to_int(),
            'post_title' => $product->get_name()->get_value(),
            'post_name' => $product->get_slug(),
            'post_excerpt' => $product->get_short_description() ? $product->get_short_description()->get_value() : '&nbsp;',
            'meta_input' => [
                'price' => $product->get_price() ? $product->get_price()->get_value() : null,
                'sale_price' => $product->get_sale_price() ? $product->get_sale_price()->get_value() : null,
                'tmp_sale_price' => $product->get_tmp_sale_price() ? $product->get_tmp_sale_price()->get_value() : null,
                'sale_price_from_date' => $product->get_sale_price_date_from() ? $product->get_sale_price_date_from()->format('Y-m-d') : null,
                'sale_price_from_hour' => $product->get_sale_price_date_from() ? $product->get_sale_price_date_from()->format('H') : null,
                'sale_price_to_date' => $product->get_sale_price_date_to() ? $product->get_sale_price_date_to()->format('Y-m-d') : null,
                'sale_price_to_hour' => $product->get_sale_price_date_to() ? $product->get_sale_price_date_to()->format('H') : null,
                'variable_sale_price_from_date' => $product->get_variable_sale_price_date_from() ? $product->get_variable_sale_price_date_from()->format(
                    'Y-m-d'
                ) : null,
                'variable_sale_price_from_hour' => $product->get_variable_sale_price_date_from() ? $product->get_variable_sale_price_date_from()->format(
                    'H'
                ) : null,
                'variable_sale_price_to_date' => $product->get_variable_sale_price_date_to() ? $product->get_variable_sale_price_date_to()->format(
                    'Y-m-d'
                ) : null,
                'variable_sale_price_to_hour' => $product->get_variable_sale_price_date_to() ? $product->get_variable_sale_price_date_to()->format('H') : null,
                'access_time' => $product->get_access_time(),
                'access_time_unit' => $product->get_access_time_unit(),
                'access_start' => $product->get_access_start() ? $product->get_access_start()->format('Y-m-d H:i') : null,
                'access_start_hh' => $product->get_access_start() ? $product->get_access_start()->format('H') : null,
                'access_start_mm' => $product->get_access_start() ? $product->get_access_start()->format('i') : null,
                'drip_value' => $course_with_product->get_drip_value(),
                'drip_unit' => $course_with_product->get_drip_unit(),
                'redirect_page' => $course_with_product->get_redirect_page(),
                'certificate_template_id' => $course_with_product->get_certificate_template_id(),
                'redirect_url' => $course_with_product->get_redirect_url(),
                '_thumbnail_id' => $product->get_thumbnail_id(),
                'recurring_payments_enabled' => $product->get_recurring_payments_enabled(),
                'variable_prices' => !$variable_prices ? $variable_prices : [],
                'variable_pricing' => $product->get_variable_pricing_enabled(),
                'sales_disabled' => $product->sales_disabled() ? 'on' : 'off',
                'hide_from_lists' => $product->hide_from_list() ? 'on' : 'off',
                'purchase_button_hidden' => $product->hide_purchase_button() ? 'on' : 'off',
                'promote_curse' => $product->get_promote_course() ? 'on' : 'off',
                'disable_certificates' => $product->get_disable_certificates() ? 'on' : 'off',
                'enable_certificate_numbering' => $product->get_enable_certificate_numbering() ? 'on' : 'off',
                'certificate_numbering_pattern' => $product->get_certificate_numbering_pattern(),
                'disable_email_subscription' => $product->get_disable_email_subscription() ? 'on' : 'off',
                'logo' => $product->get_logo(),
                'banner' => $product->get_banner(),
                'invoices_vat_rate' => $product->get_vat_rate() ?? '',
                '_edd-sell-discount-code' => $product->get_discount_code_settings()->get_sell_discount_code(),
                '_edd-sell-discount-time' => $product->get_discount_code_settings()->get_sell_discount_time(),
                '_edd-sell-discount-time-type' => $product->get_discount_code_settings()->get_sell_discount_time_type(),
                'gtu' => $product->get_gtu()->get_code(),
                Flat_Rate_Tax_Symbol_Helper::META_NAME => $product->get_flat_rate_tax_symbol() ? $product->get_flat_rate_tax_symbol()->get_value(
                ) : Flat_Rate_Tax_Symbol_Helper::NO_TAX_SYMBOL,
                'navigation_next_lesson_label' => $product->get_navigation_next_lesson_label(),
                'navigation_previous_lesson_label' => $product->get_navigation_previous_lesson_label(),
                'progress_tracking' => $product->get_progress_tracking(),
                'inaccessible_lesson_display' => $product->get_inaccessible_lesson_display(),
                'progress_forced' => $product->get_progress_forced()
            ],
        ];

        wp_update_post($args);
    }

    private function update_page_meta(Product $product, Course_With_Product $course_with_product): void
    {
        $args = [
            'ID' => $course_with_product->get_page_id()->to_int(),
            'post_title' => $product->get_name()->get_value(),
            'post_name' => $product->get_slug(),
            'post_excerpt' => $product->get_short_description() ? $product->get_short_description()->get_value() : '&nbsp;',
            'post_date' => $course_with_product->get_post_date() ? $course_with_product->get_post_date()->format('Y-m-d H:i:s') : null,
            'post_date_gmt' => $course_with_product->get_post_date_gmt() ? $course_with_product->get_post_date_gmt()->format('Y-m-d H:i:s') : null,
            'meta_input' => [
                '_bpmj_eddpc_redirect_page' => $course_with_product->get_redirect_page(),
                '_bpmj_eddpc_redirect_url' => $course_with_product->get_redirect_url(),
                '_bpmj_eddpc_access_start_enabled' => $product->get_access_start_enabled(),
                '_bpmj_eddpc_access_start' => $product->get_access_start() ? $product->get_access_start()->format('Y-m-d H:i') : null,
                '_thumbnail_id' => $product->get_thumbnail_id(),
                'sales_disabled' => $product->sales_disabled() ? 'on' : 'off',
                'hide_from_lists' => $product->hide_from_list() ? 'on' : 'off',
                'purchase_button_hidden' => $product->hide_purchase_button() ? 'on' : 'off',
                'promote_curse' => $product->get_promote_course() ? 'on' : 'off',
                'disable_certificates' => $product->get_disable_certificates() ? 'on' : 'off',
                'enable_certificate_numbering' => $product->get_enable_certificate_numbering() ? 'on' : 'off',
                'certificate_numbering_pattern' => $product->get_certificate_numbering_pattern(),
                'disable_email_subscription' => $product->get_disable_email_subscription() ? 'on' : 'off',
                'logo' => $product->get_logo(),
                'banner' => $product->get_banner(),
                'gtu' => $product->get_gtu()->get_code(),
                Flat_Rate_Tax_Symbol_Helper::META_NAME => $product->get_flat_rate_tax_symbol() ? $product->get_flat_rate_tax_symbol()->get_value(
                ) : Flat_Rate_Tax_Symbol_Helper::NO_TAX_SYMBOL,
                'navigation_next_lesson_label' => $product->get_navigation_next_lesson_label(),
                'navigation_previous_lesson_label' => $product->get_navigation_previous_lesson_label(),
                'progress_tracking' => $product->get_progress_tracking(),
                'inaccessible_lesson_display' => $product->get_inaccessible_lesson_display(),
                'progress_forced' => $product->get_progress_forced()
            ],
        ];

        wp_update_post($args);
    }

    private function update_course_tags(Product $product, Course_With_Product $course_with_product): void
    {
        wp_set_object_terms($course_with_product->get_id()->to_int(), $product->get_tags()->to_array(), 'download_tag', false);
    }

    private function update_course_categories(Product $product, Course_With_Product $course_with_product): void
    {
        wp_set_post_terms($course_with_product->get_id()->to_int(), $this->get_prepared_categories($product), 'download_category');
    }

    private function get_prepared_categories(Product $product): array
    {
        $array = [];

        foreach ($product->get_categories() as $category) {
            $array[] = $category->term_taxonomy_id;
        }

        return $array;
    }

    private function get_thumbnail_url_from_post_id(int $post_id): ?string
    {
        $url = get_the_post_thumbnail_url($post_id);

        return $url ?: null;
    }
}
