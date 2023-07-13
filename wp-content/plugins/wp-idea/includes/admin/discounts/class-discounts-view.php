<?php

namespace bpmj\wpidea\admin\discounts;

use bpmj\wpidea\admin\pages\discount_codes\Discounts_Page_Renderer;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Discounts_View
{
    private Discounts_Page_Renderer $discount_codes_page_renderer;
    private Interface_View_Provider $view_provider;
    private Subscription $subscription;
    private Interface_Translator $translator;
    private Interface_Packages_API $packages_api;

    public function __construct(
        Discounts_Page_Renderer $discount_codes_page_renderer,
        Interface_View_Provider $view_provider,
        Subscription $subscription,
        Interface_Translator $translator,
        Interface_Packages_API $packages_api
    ) {
        $this->discount_codes_page_renderer = $discount_codes_page_renderer;
        $this->view_provider = $view_provider;
        $this->subscription = $subscription;
        $this->translator = $translator;
        $this->packages_api = $packages_api;
    }

    public function render()
    {
        if (isset($_GET['wp-idea-action']) && $_GET['wp-idea-action'] === 'edit_discount') {
            if (!isset($_GET['discount']) || !is_numeric($_GET['discount'])) {
                wp_die(
                    __('Something went wrong.', BPMJ_EDDCM_DOMAIN),
                    __('Error', BPMJ_EDDCM_DOMAIN),
                    ['response' => 400]
                );
            }

            $discount_id = absint($_GET['discount']);

            echo $this->view_provider->get_admin('/discounts/edit', [
                'discount_id' => $discount_id,
                'discount' => edd_get_discount($discount_id),
                'product_reqs' => edd_get_discount_product_reqs($discount_id),
                'excluded_products' => edd_get_discount_excluded_products($discount_id),
                'condition' => edd_get_discount_product_condition($discount_id),
                'single_use' => edd_discount_is_single_use($discount_id),
            ]);
            return;
        }

        if (isset($_GET['wp-idea-action']) && $_GET['wp-idea-action'] === 'add_discount') {
            echo $this->view_provider->get_admin('/discounts/add');
            return;
        }

        if (isset($_GET['wp-idea-action']) && $_GET['wp-idea-action'] === 'edd-dc-generator') {
            if (!$this->packages_api->has_access_to_feature(Packages::FEAT_DISCOUNT_CODE_GENERATOR)) {
                echo $this->view_provider->get_admin('/discounts/generator-plan-error', [
                    'title' => $this->translator->translate('discount_codes.plan_error.title'),
                    'message' => $this->packages_api->render_no_access_to_feature_info(Packages::FEAT_DISCOUNT_CODE_GENERATOR),
                ]);
                return;
            }
            echo $this->view_provider->get_admin('/discounts/generator');
            return;
        }

        $this->discount_codes_page_renderer->render_page();
    }
}
