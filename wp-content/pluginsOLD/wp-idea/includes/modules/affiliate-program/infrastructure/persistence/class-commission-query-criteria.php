<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\persistence;

class Commission_Query_Criteria
{
    private ?int $id = null;
    private ?string $partner_id = null;
    private ?string $partner_email = null;
    private ?string $name = null;
    private ?string $email = null;
    private ?array $products = null;
    private ?array $sale_amount = null;
    private ?array $commission_percentage = null;
    private ?array $commission_amount = null;
    private ?array $created_at = null;
    private ?string $status = null;

    public function set_id(?int $id): void
    {
        $this->id = $id;
    }

    public function get_id(): ?int
    {
        return $this->id;
    }

    public function set_partner_id(?string $partner_id): void
    {
        $this->partner_id = $partner_id;
    }

    public function get_partner_id(): ?string
    {
        return $this->partner_id;
    }

    public function set_partner_email(?string $partner_email): void
    {
        $this->partner_email = $partner_email;
    }

    public function get_partner_email(): ?string
    {
        return $this->partner_email;
    }

    public function set_name(?string $name): void
    {
        $this->name = $name;
    }

    public function get_name(): ?string
    {
        return $this->name;
    }

    public function set_email(?string $email): void
    {
        $this->email = $email;
    }

    public function get_email(): ?string
    {
        return $this->email;
    }

    public function set_products(?array $products): void
    {
        $this->products = $products;
    }

    public function get_products(): ?array
    {
        return $this->products;
    }

    public function set_sale_amount(?array $sale_amount): void
    {
        $this->sale_amount = $sale_amount;
    }

    public function get_sale_amount(): ?array
    {
        return $this->sale_amount;
    }

    public function set_commission_amount(?array $commission_amount): void
    {
        $this->commission_amount = $commission_amount;
    }

    public function get_commission_amount(): ?array
    {
        return $this->commission_amount;
    }

    public function set_commission_percentage(?array $commission_percentage): void
    {
        $this->commission_percentage = $commission_percentage;
    }

    public function get_commission_percentage(): ?array
    {
        return $this->commission_percentage;
    }

    public function set_date(?array $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function get_date(): ?array
    {
        return $this->created_at;
    }

    public function set_status(?string $status): void
    {
        $this->status = $status;
    }

    public function get_status(): ?string
    {
        return $this->status;
    }

}