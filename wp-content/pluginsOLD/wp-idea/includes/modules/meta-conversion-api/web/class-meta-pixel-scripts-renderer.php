<?php

namespace bpmj\wpidea\modules\meta_conversion_api\web;

use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\sales\order\api\dto\Order_DTO;
use bpmj\wpidea\sales\order\api\Interface_Order_API;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\modules\meta_conversion_api\api\Meta_Conversion_API;

class Meta_Pixel_Scripts_Renderer
{
    public const PIXEL_FB_ID_SETTING_NAME = 'pixel_fb_id';
    public const FB_PURCHASE_EVENT_EXECUTED = 'fb_purchase_event_executed';

    private Interface_Actions $actions;
    private System $system;
    private Interface_Settings $settings;
    private Cart_API $cart_api;
    private Meta_Conversion_API $meta_conversion_api;
    private Interface_Order_API $order_api;

    public function __construct(
            Interface_Order_API $order_api,
            Interface_Actions $actions,
            System $system,
            Interface_Settings $settings,
            Cart_API $cart_api,
            Meta_Conversion_API $meta_conversion_api
    ) {
        $this->order_api = $order_api;
        $this->actions = $actions;
        $this->system = $system;
        $this->settings = $settings;
        $this->cart_api = $cart_api;
        $this->meta_conversion_api = $meta_conversion_api;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::HEAD, [$this, 'before_head_close_tag_scripts'], 1000);
    }

    public function before_head_close_tag_scripts(): void
    {
        echo $this->get_head_scripts();
    }

    private function get_head_scripts(): string
    {
        $string = '';

        if ($this->is_fb_pixel_enabled()) {
            $string .= $this->get_fb_pixel($this->settings->get(self::PIXEL_FB_ID_SETTING_NAME));
        }

        return $string;
    }

    private function get_fb_pixel($id): string
    {
        ob_start();
        ?>
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?= $id ?>');
            fbq('track', 'PageView', {eventID: '<?= $this->get_unique_id() ?>'});
        </script>
        <?php

        echo $this->get_fb_pixel_add_to_cart_event();

        echo $this->get_fb_pixel_initiate_checkout_event();

        echo $this->get_fb_pixel_purchase_event();

        return ob_get_clean();
    }

    private function get_fb_pixel_add_to_cart_event(): string
    {
        ob_start();
        if ($this->is_fb_pixel_enabled()) : ?>
            <script>
                jQuery(document).ready(function ($) {
                    $('body').on('edd_cart_item_added', function (e, response) {
                        var cart_item = $(response.cart_item);

                        fbq('track', 'AddToCart', {
                            contents: [
                                {
                                    id: cart_item.children('a').data('download-id'),
                                    name: cart_item.find('.edd-cart-item-title').text(),
                                    price: parseFloat(cart_item.find('.edd-cart-item-price').text()),
                                }
                            ],
                            content_ids: [parseInt(cart_item.children('a').data('download-id'))],
                            content_type: 'product',
                            content_name: $('title').text(),
                            value: parseFloat(cart_item.find('.edd-cart-item-price').text()),
                            currency: '<?= $this->system->get_system_currency() ?>',
                        }, {eventID: '<?= $this->get_unique_id() ?>'});
                    });
                });
            </script>
        <?php
        endif;

        return ob_get_clean();
    }

    private function get_fb_pixel_initiate_checkout_event(): string
    {
        ob_start();
        if ($this->is_fb_pixel_enabled() && $this->cart_api->is_checkout()) :
            $cart_items = edd_get_cart_contents();
            ?>
            <script>
                jQuery(document).ready(function ($) {
                    var products = [],
                        contentIds = [];

                    <?php foreach ( $cart_items as $key => $item ) : ?>
                    products.push({
                        id: parseInt('<?= $item['id'] ?>'),
                        name: '<?= edd_get_cart_item_name($item); ?>',
                        price: parseFloat('<?= edd_get_cart_item_price($item['id'], $item['options']) ?>'),
                    });

                    contentIds.push(parseInt('<?= $item['id'] ?>'));
                    <?php endforeach; ?>

                    fbq('track', 'InitiateCheckout', {
                        content_category: 'course',
                        content_ids: contentIds,
                        contents: products,
                        value: parseFloat('<?= edd_cart_total() ?>'),
                        currency: '<?= edd_get_currency() ?>',
                        num_items: products.length,
                    }, {eventID: '<?= $this->get_unique_id() ?>'});

                    <?php if ( isset($_SESSION['buy-by-purchase-link']) ) :
                    unset($_SESSION['buy-by-purchase-link']);
                    ?>
                    fbq('track', 'AddToCart', {
                        contents: products,
                        content_ids: contentIds,
                        content_type: 'product',
                        content_name: $('title').text(),
                        value: parseFloat('<?= edd_cart_total() ?>'),
                        currency: '<?= $this->system->get_system_currency() ?>'
                    }, {eventID: '<?= $this->get_unique_id() ?>'});
                    <?php endif; ?>
                });
            </script>
        <?php
        endif;

        return ob_get_clean();
    }

    private function get_fb_pixel_purchase_event(): string
    {
        $payment_id = $this->get_payment_id_on_success_page();
        if (!$payment_id) {
            return '';
        }

        $order = $this->order_api->find($payment_id);
        if (!$order) {
            return '';
        }

        ob_start();
        if ($this->is_fb_pixel_enabled() && $this->cart_api->is_success_page() && !$this->fb_purchase_event_executed($order)) :
            $cart = $order->get_cart_content()->get_item_details();
            ?>
            <script>
                jQuery(document).ready(function ($) {
                    var products = [],
                        contentIds = [];
                    <?php foreach ( $cart as $key => $item ) : ?>
                    products.push({
                        id: parseInt('<?= $item['id'] ?>'),
                        name: '<?= $item['name'] ?>',
                        price: parseFloat('<?= $item['price'] ?>'),
                    });

                    contentIds.push(parseInt('<?= $item['id'] ?>'));
                    <?php endforeach; ?>

                    fbq('track', 'Purchase', {
                        content_ids: contentIds,
                        contents: products,
                        content_name: $('title').text(),
                        content_type: 'product',
                        value: '<?= $order->get_total() ?>',
                        currency: '<?= $this->system->get_system_currency() ?>',
                        num_items: products.length,
                    }, {eventID: '<?= $order->get_id() ?>'});
                });
            </script>
        <?php
            $this->order_api->store_meta($order, self::FB_PURCHASE_EVENT_EXECUTED, '1');
        endif;

        return ob_get_clean();
    }

    private function fb_purchase_event_executed(Order_DTO $order): bool
    {
        $purchase_event_executed = $this->order_api->get_meta($order, self::FB_PURCHASE_EVENT_EXECUTED);
        if ($purchase_event_executed) {
            return true;
        }

        return false;
    }

    private function is_fb_pixel_enabled(): bool
    {
        return !empty($this->settings->get(self::PIXEL_FB_ID_SETTING_NAME));
    }

    private function get_payment_id_on_success_page(): ?int
    {
        global $edd_receipt_args;

        $session = edd_get_purchase_session();
        if (isset($_GET['payment_key'])) {
            $payment_key = urldecode($_GET['payment_key']);
        } else {
            if ($session) {
                $payment_key = $session['purchase_key'];
            } elseif (!empty($edd_receipt_args['payment_key'])) {
                $payment_key = $edd_receipt_args['payment_key'];
            }
        }

        if (empty($payment_key)) {
            return null;
        }

        return edd_get_purchase_id_by_key($payment_key);
    }

    private function get_unique_id(): string
    {
        return $this->meta_conversion_api->get_unique_id_for_request();
    }
}
