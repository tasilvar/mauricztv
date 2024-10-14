<?php

declare(strict_types=1);

namespace bpmj\wpidea\sales\order\invoice;

class Invoice
{
    private string $invoice_company_name;
    private string $invoice_country;
    private string $invoice_city;
    private string $invoice_street;
    private string $invoice_building_number;
    private ?int $invoice_apartment_number;
    private string $invoice_nip;
    private string $invoice_type;
    private string $invoice_person_name;
    private string $invoice_postcode;
    
    public function set_invoice_type(string $invoice_type): void
    {
         $this->invoice_type = $invoice_type;
    }

    public function set_invoice_nip(string $invoice_nip): void
    {
         $this->invoice_nip = $invoice_nip;
    }
    
    public function get_invoice_nip():?string
    {
         return $this->invoice_nip;
    }

    public function set_invoice_company_name(string $invoice_company_name): void
    {
         $this->invoice_company_name = $invoice_company_name;
    }
    
    public function get_invoice_company_name(): string
    {
         return $this->invoice_company_name;
    }
    
    public function set_invoice_country(string $invoice_country): void
    {
         $this->invoice_country = $invoice_country;
    }
    
    public function get_invoice_country(): string
    {
         return $this->invoice_country;
    }
    
    public function set_invoice_city(string $invoice_city): void
    {
         $this->invoice_city = $invoice_city;
    }

    public function get_invoice_city(): string
    {
        return $this->invoice_city;
    }
    
    public function set_invoice_street(string $invoice_street): void
    {
         $this->invoice_street = $invoice_street;
    }

    public function get_invoice_street(): string
    {
        return $this->invoice_street;
    }

    public function set_invoice_building_number(string $invoice_building_number): void
    {
        $this->invoice_building_number = $invoice_building_number;
    }

    public function get_invoice_building_number(): string
    {
        return $this->invoice_building_number;
    }

    public function set_invoice_apartment_number(?int $invoice_apartment_number): void
    {
        $this->invoice_apartment_number = $invoice_apartment_number;
    }

    public function get_invoice_apartment_number(): ?int
    {
        return $this->invoice_apartment_number;
    }

    public function set_invoice_person_name(string $invoice_person_name): void
    {
        $this->invoice_person_name = $invoice_person_name;
    }

    public function get_invoice_person_name(): string
    {
        return $this->invoice_person_name;
    }

    public function set_invoice_postcode(string $invoice_postcode): void
    {
        $this->invoice_postcode = $invoice_postcode;
    }

    public function get_invoice_postcode(): string
    {
        return $this->invoice_postcode;
    }
}