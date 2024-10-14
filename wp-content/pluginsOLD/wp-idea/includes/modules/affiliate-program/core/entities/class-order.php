<?php

namespace bpmj\wpidea\modules\affiliate_program\core\entities;

class Order
{
    private int $id;
    private float $total;
    private string $client_name;
    private string $client_email;
    private array $purchased_product_ids;
    private \DateTimeImmutable $order_date;
    private ?Partner $partner;
    private ?string $campaign;

    private function __construct(
        int $id,
        float $total,
        string $client_name,
        string $client_email,
        array $purchased_product_ids,
        \DateTimeImmutable $order_date,
        ?Partner $partner,
        ?string $campaign
    ) {
        $this->id = $id;
        $this->total = $total;
        $this->client_name = $client_name;
        $this->client_email = $client_email;
        $this->purchased_product_ids = $purchased_product_ids;
        $this->order_date = $order_date;
        $this->partner = $partner;
        $this->campaign = $campaign;
    }

    public static function create(
        int $id,
        float $total,
        string $client_name,
        string $client_email,
        array $purchased_product_ids,
        \DateTimeImmutable $order_date,
        ?Partner $partner,
        ?string $campaign
    ): self {
        return new self(
            $id,
            $total,
            $client_name,
            $client_email,
            $purchased_product_ids,
            $order_date,
            $partner,
            $campaign
        );
    }

    public function get_id(): int
    {
        return $this->id;
    }

    public function get_total(): float
    {
        return $this->total;
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

    public function get_date(): \DateTimeImmutable
    {
        return $this->order_date;
    }

    public function get_partner(): ?Partner
    {
        return $this->partner;
    }

    public function get_campaign(): ?string
    {
        return $this->campaign;
    }
}