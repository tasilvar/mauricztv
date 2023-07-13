<?php

namespace bpmj\wpidea\modules\affiliate_program\core\entities;

use bpmj\wpidea\modules\affiliate_program\core\value_objects\Commission_ID;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_ID;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Status;
use DateTimeInterface;

class Commission
{
    private ?Commission_ID $id;
    private Partner_ID $partner_id;
    private string $partner_affiliate_id;
    private string $partner_email;
    private string $client_name;
    private string $client_email;
    private array $purchased_product_ids;
    private int $sale_amount_in_fractions;
    private int $commission_percentage;
    private int $commission_amount_in_fractions;
    private DateTimeInterface $created_at;
    private Status $status;
    private ?string $campaign;

    public function __construct(
        ?Commission_ID $id,
        Partner_ID $partner_id,
        string $partner_affiliate_id,
        string $partner_email,
        string $client_name,
        string $client_email,
        array $purchased_product_ids,
        int $sale_amount_in_fractions,
        int $commission_percentage,
        int $commission_amount_in_fractions,
        DateTimeInterface $created_at,
        Status $status,
        ?string $campaign
    ) {
        $this->id = $id;
        $this->partner_id = $partner_id;
        $this->partner_affiliate_id = $partner_affiliate_id;
        $this->partner_email = $partner_email;
        $this->client_name = $client_name;
        $this->client_email = $client_email;
        $this->purchased_product_ids = $purchased_product_ids;
        $this->sale_amount_in_fractions = $sale_amount_in_fractions;
        $this->commission_percentage = $commission_percentage;
        $this->commission_amount_in_fractions = $commission_amount_in_fractions;
        $this->created_at = $created_at;
        $this->status = $status;
        $this->campaign = $campaign;
    }

    public static function create(
        ?Commission_ID $id,
        Partner_ID $partner_id,
        string $partner_affiliate_id,
        string $partner_email,
        string $client_name,
        string $client_email,
        array $purchased_product_ids,
        int $sale_amount_in_fractions,
        int $commission_percentage,
        int $commission_amount_in_fractions,
        DateTimeInterface $created_at,
        Status $status,
        ?string $campaign = null
    ): Commission {
        return new self(
            $id,
            $partner_id,
            $partner_affiliate_id,
            $partner_email,
            $client_name,
            $client_email,
            $purchased_product_ids,
            $sale_amount_in_fractions,
            $commission_percentage,
            $commission_amount_in_fractions,
            $created_at,
            $status,
            $campaign
        );
    }

    public function get_id(): ?Commission_ID
    {
        return $this->id;
    }

    public function get_partner_id(): Partner_ID
    {
        return $this->partner_id;
    }

    public function get_partner_affiliate_id(): string
    {
        return $this->partner_affiliate_id;
    }

    public function get_partner_email(): string
    {
        return $this->partner_email;
    }

    public function get_client_name(): string
    {
        return $this->client_name;
    }

    public function get_client_email(): string
    {
        return $this->client_email;
    }

    public function get_purchased_product_ids(): array
    {
        return $this->purchased_product_ids;
    }

    public function get_sale_amount_in_fractions(): int
    {
        return $this->sale_amount_in_fractions;
    }

    public function get_commission_amount_in_fractions(): int
    {
        return $this->commission_amount_in_fractions;
    }

    public function get_commission_percentage(): int
    {
        return $this->commission_percentage;
    }

    public function get_date(): DateTimeInterface
    {
        return $this->created_at;
    }

    public function get_status(): Status
    {
        return $this->status;
    }

    public function change_status(Status $status): self
    {
        $this->status = $status;
        return $this;
    }
    
    public function get_campaign(): ?string
    {
        return $this->campaign;
    }
}