<?php

declare(strict_types=1);

namespace bpmj\wpidea\sales\order\additional;

class Additional_Fields
{
    private $buy_as_gift;
    private $voucher_codes;
    private $checkbox_checked;
    private $checkbox_description;
    private $checkbox2_checked;
    private $checkbox2_description;
    private $order_comment;

    public function set_buy_as_gift(bool $buy_as_gift):void
    {
         $this->buy_as_gift = $buy_as_gift;
    }

    public function get_buy_as_gift():bool
    {
        return $this->buy_as_gift;
    }

    public function set_voucher_codes(string $voucher_codes):void
    {
        $this->voucher_codes = $voucher_codes;
    }

    public function get_voucher_codes():string
    {
        return $this->voucher_codes;
    }

    public function set_checkbox_checked(bool $checkbox_checked):void
    {
        $this->checkbox_checked = $checkbox_checked;
    }

    public function get_checkbox_checked():bool
    {
        return $this->checkbox_checked;
    }

    public function set_checkbox_description(string $checkbox_description):void
    {
        $this->checkbox_description = $checkbox_description;
    }

    public function get_checkbox_description():string
    {
        return $this->checkbox_description;
    }

    public function set_checkbox2_checked(bool $checkbox2_checked):void
    {
        $this->checkbox2_checked = $checkbox2_checked;
    }

    public function get_checkbox2_checked():bool
    {
        return $this->checkbox2_checked;
    }

    public function set_checkbox2_description(string $checkbox2_description):void
    {
        $this->checkbox2_description = $checkbox2_description;
    }

    public function get_checkbox2_description():string
    {
        return $this->checkbox2_description;
    }

    public function set_order_comment(string $order_comment):void
    {
        $this->order_comment = $order_comment;
    }

    public function get_order_comment():string
    {
        return $this->order_comment;
    }

}