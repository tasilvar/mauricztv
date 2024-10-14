<?php

namespace bpmj\wpidea\modules\affiliate_program\api\dto;

class Commission_DTO
{
    private int $id;
    private string $sales_amount;
    private string $commission_amount;
    private string $sale_date;
    private string $status;
    private ?string $campaign;

    public function __construct(
        int $id,
        string $sales_amount,
        string $commission_amount,
        string $sale_date,
        string $status,
        ?string $campaign
    ) {
        $this->id = $id;
        $this->sales_amount = $sales_amount;
        $this->commission_amount = $commission_amount;
        $this->sale_date = $sale_date;
        $this->status = $status;
        $this->campaign = $campaign;
    }

    public function get_id(): int
    {
        return $this->id;
    }

    public function get_sales_amount(): string
    {
        return $this->sales_amount;
    }

    public function get_commission_amount(): string
    {
        return $this->commission_amount;
    }

    public function get_sale_date(): string
    {
        return $this->sale_date;
    }

    public function get_status(): string
    {
        return $this->status;
    }

    public function get_campaign(): ?string
    {
        return $this->campaign;
    }

}