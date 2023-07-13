<?php

declare(strict_types=1);

namespace bpmj\wpidea\sales\order\delivery;

class Delivery
{
    private string $phone;
    private string $street;
    private string $building_number;
    private ?int $apartment_number;
    private string $postal_code;
    private string $city;
    private string $receiver_first_name;
    private string $receiver_last_name;
    private string $receiver_company;

    public function __construct($phone, $street, $building_number, $apartment_number, $postal_code, $city, $receiver_first_name, $receiver_last_name, $receiver_company)
    {
        $this->phone = $phone;
        $this->street = $street;
        $this->building_number = $building_number;
        $this->apartment_number = $apartment_number;
        $this->postal_code = $postal_code;
        $this->city = $city;
        $this->receiver_first_name = $receiver_first_name;
        $this->receiver_last_name = $receiver_last_name;
        $this->receiver_company = $receiver_company;
    }

    public function get_phone(): string
    {
        return $this->phone;
    }

    public function get_street(): string
    {
        return $this->street;
    }

    public function get_building_number(): string
    {
        return $this->building_number;
    }

    public function get_apartment_number(): ?int
    {
        return $this->apartment_number;
    }

    public function get_postal_code(): string
    {
        return $this->postal_code;
    }

    public function get_city(): string
    {
        return $this->city;
    }

    public function get_receiver_first_name(): string
    {
        return $this->receiver_first_name;
    }

    public function get_receiver_last_name(): string
    {
        return $this->receiver_last_name;
    }

    public function get_receiver_company(): string
    {
        return $this->receiver_company;
    }
}