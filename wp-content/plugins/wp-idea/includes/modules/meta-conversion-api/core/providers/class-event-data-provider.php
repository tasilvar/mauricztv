<?php

namespace bpmj\wpidea\modules\meta_conversion_api\core\providers;

use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\modules\meta_conversion_api\api\Meta_Conversion_API;
use bpmj\wpidea\modules\meta_conversion_api\core\services\Interface_Page_Info_Getter;
use bpmj\wpidea\sales\order\api\dto\Order_DTO;
use bpmj\wpidea\sales\product\api\dto\Product_DTO;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\UserData;

class Event_Data_Provider
{
    private const PURCHASE_EVENT_NAME = 'Purchase';
    private const ADD_TO_CART_EVENT_NAME = 'AddToCart';
    private const INITIATE_CHECKOUT_EVENT_NAME = 'InitiateCheckout';
    private const PAGE_VIEW_EVENT_NAME = 'PageView';

    private const CONTENT_TYPE = 'product';
    private const CONTENT_CATEGORY = 'course';

    private string $system_currency;
    private Interface_User_Data_Provider $user_data_provider;
    private Interface_Page_Info_Getter $page_info_getter;
    private System $system;
    private Meta_Conversion_API $meta_conversion_api;

    public function __construct(
        Interface_User_Data_Provider $user_data_provider,
        Interface_Page_Info_Getter $page_info_getter,
        System $system,
        Meta_Conversion_API $meta_conversion_api
    ) {
        $this->user_data_provider = $user_data_provider;
        $this->page_info_getter = $page_info_getter;
        $this->system = $system;
        $this->meta_conversion_api = $meta_conversion_api;
    }

    public function get_event_for_page_view(): Event
    {
        $user_data = $this->user_data_provider->get_user_data_for_logged_user();

        return (new Event())
            ->setEventName(self::PAGE_VIEW_EVENT_NAME)
            ->setEventSourceUrl($this->page_info_getter->get_current_page_url())
            ->setEventTime(time())
            ->setUserData($user_data)
            ->setEventId($this->get_unique_id())
            ->setActionSource(ActionSource::WEBSITE);
    }

    public function get_event_for_initiate_checkout(Cart_API $cart_api): Event
    {
        $user_data = $this->user_data_provider->get_user_data_for_logged_user();

        $content = [];
        $ids = [];

        foreach ($cart_api->get_cart_content() as $cart_item) {
            $content[] = (new Content())
                ->setProductId($cart_item->get_item_product_id()->to_int())
                ->setTitle($cart_api->get_item_name($cart_item))
                ->setItemPrice($cart_api->get_item_price($cart_item))
                ->setQuantity($cart_item->get_item_quantity());

            $ids[] = $cart_item->get_item_product_id()->to_int();
        }

        $custom_data = $this->get_custom_data($content, $ids, $cart_api->get_the_net_total_price(), null, self::CONTENT_CATEGORY);

        return $this->get_event_data(self::INITIATE_CHECKOUT_EVENT_NAME, $user_data, $custom_data);
    }

    public function get_event_for_add_to_cart(Product_DTO $product): Event
    {
        $user_data = $this->user_data_provider->get_user_data_for_logged_user();

        $content = [
            (new Content())
                ->setProductId($product->get_id())
                ->setTitle($product->get_name())
                ->setItemPrice($product->get_price())
                ->setQuantity(1)
        ];

        $ids = [$product->get_id()];

        $custom_data = $this->get_custom_data($content, $ids, $product->get_price(), self::CONTENT_TYPE);

        return $this->get_event_data(self::ADD_TO_CART_EVENT_NAME, $user_data, $custom_data);
    }

    public function get_event_for_purchase(Order_DTO $order): Event
    {
        $user_data = $this->user_data_provider->get_user_data($order->get_client(), $order->get_invoice());

        $content = [];
        $ids = [];

        foreach ($order->get_cart_content()->get_item_details() as $cart_item_detail) {
            $content[] = (new Content())
                ->setProductId($cart_item_detail['id'])
                ->setTitle($cart_item_detail['name'])
                ->setItemPrice((float)$cart_item_detail['price'])
                ->setQuantity($cart_item_detail['quantity']);

            $ids[] = $cart_item_detail['id'];
        }

        $custom_data = $this->get_custom_data($content, $ids, $order->get_total(), self::CONTENT_TYPE);

        return $this->get_event_data(self::PURCHASE_EVENT_NAME, $user_data, $custom_data, $order->get_id());
    }

    private function get_custom_data(array $content, array $ids, float $total_price, ?string $content_type = null, ?string $content_category = null): CustomData
    {
        $custom_data = (new CustomData())
            ->setContents($content)
            ->setContentName($this->page_info_getter->get_page_title())
            ->setContentIds($ids)
            ->setCurrency($this->get_currency())
            ->setValue($total_price)
            ->setNumItems(count($content));

        if ($content_type) {
            $custom_data->setContentType($content_type);
        }

        if ($content_category) {
            $custom_data->setContentCategory($content_category);
        }

        return $custom_data;
    }

    private function get_event_data(string $event_name, UserData $user_data, ?CustomData $custom_data = null, ?int $external_id = null): Event
    {
        $event = (new Event())
            ->setEventName($event_name)
            ->setEventSourceUrl($this->page_info_getter->get_current_page_url())
            ->setEventTime(time())
            ->setUserData($user_data)
            ->setEventId($external_id ?? $this->get_unique_id())
            ->setActionSource(ActionSource::WEBSITE);

        if ($custom_data) {
            $event->setCustomData($custom_data);
        }

        return $event;
    }

    private function get_currency(): string
    {
        if (!isset($this->system_currency)) {
            $this->system_currency = $this->system->get_system_currency();
        }

        return $this->system_currency;
    }

    private function get_unique_id(): string
    {
        return $this->meta_conversion_api->get_unique_id_for_request();
    }
}
