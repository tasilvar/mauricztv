<?php
/*
* This file is licenses under proprietary license
*/

namespace bpmj\wpidea\admin\subscription\models;


class Server_License_Data
{
    public const STATUS_VALID = 'valid';
    public const STATUS_INVALID = 'invalid';
    public const STATUS_INACTIVE = 'inactive';

    private $status;
    private $expires;
    private $payment_id;
    private $price_id;
    private $customer_email;
    private $customer_name;

    public function __construct(
        string $status,
        ?string $expires = null,
        ?int $payment_id = null,
        ?int $price_id = null,
        ?string $customer_email = null,
        ?string $customer_name = null
    )
    {
        $this->status = $status;
        $this->expires = $expires;
        $this->payment_id = $payment_id;
        $this->price_id = $price_id;
        $this->customer_email = $customer_email;
        $this->customer_name = $customer_name;
    }

    public function get_status(): string
    {
        return $this->status;
    }

    public function get_expires(): ?string
    {
        return $this->expires;
    }

    public function get_payment_id(): ?int
    {
        return $this->payment_id;
    }

    public function get_price_id(): ?int
    {
        return $this->price_id;
    }

    public function get_customer_email(): ?string
    {
        return $this->customer_email;
    }

    public function get_customer_name(): ?string
    {
        return $this->customer_name;
    }
}