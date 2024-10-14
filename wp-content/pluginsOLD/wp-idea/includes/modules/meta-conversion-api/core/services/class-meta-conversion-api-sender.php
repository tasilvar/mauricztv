<?php

namespace bpmj\wpidea\modules\meta_conversion_api\core\services;

use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\modules\meta_conversion_api\core\providers\Event_Data_Provider;
use bpmj\wpidea\sales\order\api\dto\Order_DTO;
use bpmj\wpidea\sales\product\api\dto\Product_DTO;
use bpmj\wpidea\settings\Interface_Settings;
use FacebookAds\Api;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequestAsync;

class Meta_Conversion_API_Sender implements Interface_Meta_Conversion_API_Sender
{
    private const PIXEL_FB_ID = 'pixel_fb_id';
    private const FB_ACCESS_TOKEN = 'fb_access_token';

    private Interface_Settings $settings;
    private Event_Data_Provider $event_data_provider;

    public function __construct(
        Interface_Settings $settings,
        Event_Data_Provider $event_data_provider
    ) {
        $this->settings = $settings;
        $this->event_data_provider = $event_data_provider;

        $this->api_init();
    }

    public function send_data_for_page_view_event(): void
    {
        if (!$this->is_fb_pixel_enabled()) {
            return;
        }

        $event = $this->event_data_provider->get_event_for_page_view();

        $this->create_async_request($event);
    }

    public function send_data_for_initiate_checkout_event(Cart_API $cart_api): void
    {
        if (!$this->is_fb_pixel_enabled()) {
            return;
        }

        if ($cart_api->get_cart_content()->is_empty()) {
            return;
        }

        $event = $this->event_data_provider->get_event_for_initiate_checkout($cart_api);

        $this->create_async_request($event);
    }

    public function send_data_for_add_to_cart_event(Product_DTO $product): void
    {
        if (!$this->is_fb_pixel_enabled()) {
            return;
        }

        $event = $this->event_data_provider->get_event_for_add_to_cart($product);

        $this->create_async_request($event);
    }

    public function send_data_for_purchase_event(Order_DTO $order): void
    {
        if (!$this->is_fb_pixel_enabled()) {
            return;
        }

        $event = $this->event_data_provider->get_event_for_purchase($order);

        $this->create_async_request($event);
    }

    private function api_init(): void
    {
        $access_token = $this->get_access_token();

        if (!$access_token) {
            return;
        }

        Api::init(null, null, $access_token, false);
    }

    private function create_async_request(Event $event): void
    {
        $async_request = (new EventRequestAsync($this->get_fb_pixel_id()))
            ->setEvents([$event]);

        $async_request->execute();
    }

    private function get_fb_pixel_id(): ?string
    {
        return $this->settings->get(self::PIXEL_FB_ID);
    }

    private function get_access_token(): ?string
    {
        return $this->settings->get(self::FB_ACCESS_TOKEN);
    }

    private function is_fb_pixel_enabled(): bool
    {
        return $this->get_fb_pixel_id() && $this->get_access_token();
    }
}
