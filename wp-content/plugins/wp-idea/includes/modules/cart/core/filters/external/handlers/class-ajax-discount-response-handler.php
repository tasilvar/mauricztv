<?php

namespace bpmj\wpidea\modules\cart\core\filters\external\handlers;

use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\modules\cart\api\Cart_API;

class Ajax_Discount_Response_Handler implements Interface_Initiable
{
    private Interface_Filters $filters;
    private Cart_API $cart_api;

    public function __construct(
        Interface_Filters $filters,
        Cart_API $cart_api
    ) {
        $this->filters = $filters;
        $this->cart_api = $cart_api;
    }

    public function init(): void
    {
        $this->filters->add(Filter_Name::AJAX_DISCOUNT_RESPONSE, [$this, 'ajax_discount_response']);
    }

    public function ajax_discount_response(array $response): array
    {
        $total_net_price = $this->cart_api->get_formatted_amount(
            $this->cart_api->get_the_net_total_price()
        );

        $total_vat = $this->cart_api->get_formatted_amount(
            $this->cart_api->get_total_vat_price()
        );

        $response['total_net_price'] = $this->cart_api->get_formatted_price_with_currency($total_net_price);
        $response['total_vat'] = $this->cart_api->get_formatted_price_with_currency($total_vat);

        return $response;
    }
}
