<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\core\services;

use bpmj\wpidea\modules\increasing_sales\Increasing_Sales_Module;
use bpmj\wpidea\Current_Request;

class Offer_Cookie_Manager
{

    private Current_Request $current_request;

    public function __construct(
        Current_Request $current_request
    )
    {
        $this->current_request = $current_request;
    }

    public function set_offer_cookie(int $offer_id): void
    {
        $this->set_cookie(Increasing_Sales_Module::COOKIE_NAME, $offer_id);
    }

    private function set_cookie(string $name, $value): void
    {
        $this->current_request->set_cookie_arg(
            $name,
            (string)$value,
            time() + Increasing_Sales_Module::COOKIE_LIFE_TIME
        );
    }

    public function get_offer_cookie_value(): ?int
    {
        $cookie = $this->current_request->get_cookie_arg(Increasing_Sales_Module::COOKIE_NAME);

        if(empty($cookie) || !is_numeric($cookie)) {
            return null;
        }

        return (int)$cookie;
    }

    public function clear_offer_cookie(): void
    {
        $this->current_request->delete_cookie_arg(Increasing_Sales_Module::COOKIE_NAME);
    }
}