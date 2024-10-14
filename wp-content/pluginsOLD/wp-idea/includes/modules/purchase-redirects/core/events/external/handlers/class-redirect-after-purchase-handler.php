<?php

namespace bpmj\wpidea\modules\purchase_redirects\core\events\external\handlers;

use bpmj\wpidea\modules\purchase_redirects\core\services\Site_Info_Getter;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\infrastructure\assets\Interface_Script_Loader;
use bpmj\wpidea\modules\purchase_redirects\core\services\Redirection_Rules_Processor;
use bpmj\wpidea\modules\purchase_redirects\core\repositories\Interface_Purchase_Redirect_Repository;
use bpmj\wpidea\modules\purchase_redirects\acl\Interface_Cart_Module_ACL;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;
use bpmj\wpidea\sales\order\Order;
use bpmj\wpidea\translator\Interface_Translator;

class Redirect_After_Purchase_Handler implements Interface_Event_Handler
{
    private const DELAY_IN_MILLISECONDS = '3000';

    private Interface_Actions $actions;
    private Interface_Purchase_Redirect_Repository $purchase_redirect_repository;
    private Interface_Orders_Repository $order_repository;
    private Redirection_Rules_Processor $redirection_rules_processor;
    private Interface_Cart_Module_ACL $cart_module_acl;
    private Interface_Script_Loader $script_loader;
    private ?string $redirection_url = null;
    private Interface_Translator $translator;
    private Site_Info_Getter $site_info_getter;


    public function __construct(
        Interface_Actions $actions,
        Interface_Purchase_Redirect_Repository $purchase_redirect_repository,
        Interface_Orders_Repository $order_repository,
        Redirection_Rules_Processor $redirection_rules_processor,
        Interface_Cart_Module_ACL $cart_module_acl,
        Interface_Script_Loader $script_loader,
        Interface_Translator $translator,
        Site_Info_Getter $site_info_getter
    ) {
        $this->actions = $actions;
        $this->purchase_redirect_repository = $purchase_redirect_repository;
        $this->order_repository = $order_repository;
        $this->redirection_rules_processor = $redirection_rules_processor;
        $this->cart_module_acl = $cart_module_acl;
        $this->script_loader = $script_loader;
        $this->translator = $translator;
        $this->site_info_getter = $site_info_getter;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::TEMPLATE_REDIRECT, [$this, 'redirect_after_purchase'], 0);
    }

    public function redirect_after_purchase(): void
    {
        if (!$this->cart_module_acl->is_success_page()) {
            return;
        }

        $payment_id = $this->cart_module_acl->get_payment_id_from_session();

        if (!$payment_id) {
            return;
        }

        $order = $this->order_repository->find_by_id($payment_id);

        if (!$order) {
            return;
        }

        $order_product_ids = $this->get_order_product_ids($order);
        $redirection_rules = $this->purchase_redirect_repository->get_redirections_in_array();


        $this->redirection_url = $this->redirection_rules_processor->get_redirection_url_for_order_content(
            $redirection_rules,
            $order_product_ids
        );

        if (!$this->redirection_url) {
            return;
        }

        $this->actions->add(Action_Name::ENQUEUE_SCRIPTS, [$this, 'register_scripts']);
    }

    private function get_order_product_ids(Order $order): array
    {
        $cart_product_ids = [];

        foreach ($order->get_cart_content()->get_item_details() as $cart_item_detail) {
            $cart_product_ids[] = $cart_item_detail['id'];
        }

        return $cart_product_ids;
    }

    public function register_scripts(): void
    {
        $this->script_loader->enqueue_script('wpi_purchase_redirects', BPMJ_EDDCM_URL . 'includes/modules/purchase-redirects/web/assets/redirect.js', [
            'jquery',
        ], BPMJ_EDDCM_VERSION);

        $this->script_loader->localize_script('wpi_purchase_redirects', 'settings_purchase_redirects', [
            'redirection_url' => $this->redirection_url,
            'redirect_notice' => $this->translator->translate('purchase_redirects.redirect_notice'),
            'site_logo_url' => $this->site_info_getter->get_logo_site_url(),
            'delay_in_milliseconds' => self::DELAY_IN_MILLISECONDS
        ]);
    }
}