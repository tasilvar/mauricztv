<?php

namespace bpmj\wpidea\modules\affiliate_program\core\repositories;

use bpmj\wpidea\modules\affiliate_program\core\value_objects\Affiliate_ID;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_ID;
use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Status;

class Partner_Query_Criteria
{
    private ?Partner_ID $id = null;

    private ?Affiliate_ID $affiliate_id = null;

    private ?User_ID $user_id = null;

    private ?string $full_name_like = null;

    private ?string $email_like = null;

    private ?int $sales_sum_from = null;

    private ?int $sales_sum_to = null;

    private ?int $commissions_sum_from = null;

    private ?int $commissions_sum_to = null;

    private ?int $unsettled_commissions_sum_from = null;

    private ?int $unsettled_commissions_sum_to = null;

    private ?string $status = null;

    private ?string $partner_link = null;

    public function set_id(?Partner_ID $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function get_id(): ?Partner_ID
    {
        return $this->id;
    }

    public function set_affiliate_id(?Affiliate_ID $affiliate_id): self
    {
        $this->affiliate_id = $affiliate_id;
        return $this;
    }

    public function get_affiliate_id(): ?Affiliate_ID
    {
        return $this->affiliate_id;
    }

    public function set_user_id(User_ID $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function get_user_id(): ?User_ID
    {
        return $this->user_id;
    }

    public function set_full_name_like(?string $full_name_like): void
    {
        $this->full_name_like = $full_name_like;
    }

    public function get_full_name_like(): ?string
    {
        return $this->full_name_like;
    }

    public function set_email_like(?string $email_like): void
    {
        $this->email_like = $email_like;
    }

    public function get_email_like(): ?string
    {
        return $this->email_like;
    }

    public function set_sales_sum_range(?int $sales_sum_from, ?int $sales_sum_to): void
    {
        $this->sales_sum_from = $sales_sum_from;
        $this->sales_sum_to = $sales_sum_to;
    }

    public function get_sales_sum_from(): ?int
    {
        return $this->sales_sum_from;
    }

    public function get_sales_sum_to(): ?int
    {
        return $this->sales_sum_to;
    }

    public function set_commissions_sum_range(?int $commissions_sum_from, ?int $commissions_sum_to): void
    {
        $this->commissions_sum_from = $commissions_sum_from;
        $this->commissions_sum_to = $commissions_sum_to;
    }

    public function get_commissions_sum_from(): ?int
    {
        return $this->commissions_sum_from;
    }

    public function get_commissions_sum_to(): ?int
    {
        return $this->commissions_sum_to;
    }

    public function set_unsettled_commissions_sum_range(?int $unsettled_commissions_sum_from, ?int $unsettled_commissions_sum_to): void
    {
        $this->unsettled_commissions_sum_from = $unsettled_commissions_sum_from;
        $this->unsettled_commissions_sum_to = $unsettled_commissions_sum_to;
    }

    public function get_unsettled_commissions_sum_from(): ?int
    {
        return $this->unsettled_commissions_sum_from;
    }

    public function get_unsettled_commissions_sum_to(): ?int
    {
        return $this->unsettled_commissions_sum_to;
    }

    public function set_status(?string $status): void
    {
        $this->status = $status;
    }

    public function get_status(): ?string
    {
        return $this->status;
    }

    public function set_partner_link_like(?string $partner_link): void
    {
        $this->partner_link = $partner_link;
    }

    public function get_partner_link_like(): ?string
    {
        return $this->partner_link;
    }
}