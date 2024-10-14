<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\discount_codes\core\entities;

use bpmj\wpidea\sales\discount_codes\core\value_objects\Discount_ID;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Code;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Name;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Amount;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Uses;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Time_Limit;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Status;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Max_Uses;

class Discount
{
    private Discount_ID $id;
    private Code $code;
    private Name $name;
    private Amount $amount;
    private Uses $uses;
    private ?Max_Uses $max_uses;
    private Time_Limit $time_limit;
    private Status $status;

    private function __construct(
        Discount_ID $id,
        Code $code,
        Name $name,
        Amount $amount,
        Uses $uses,
        ?Max_Uses $max_uses,
        Time_Limit $time_limit,
        Status $status
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->amount = $amount;
        $this->uses = $uses;
        $this->max_uses = $max_uses;
        $this->time_limit = $time_limit;
        $this->status = $status;
    }

    public static function create(
        Discount_ID $id,
        Code $code,
        Name $name,
        Amount $amount,
        Uses $uses,
        ?Max_Uses $max_uses,
        Time_Limit $time_limit,
        Status $status
    ): self {
        return new self($id, $code, $name, $amount, $uses, $max_uses, $time_limit, $status);
    }

    public function get_id(): Discount_ID
    {
        return $this->id;
    }

    public function get_code(): Code
    {
        return $this->code;
    }

    public function get_name(): Name
    {
        return $this->name;
    }

    public function get_amount(): Amount
    {
        return $this->amount;
    }

    public function get_uses(): Uses
    {
        return $this->uses;
    }

    public function get_max_uses(): ?Max_Uses
    {
        return $this->max_uses;
    }

    public function get_time_limit(): Time_Limit
    {
        return $this->time_limit;
    }

    public function get_status(): Status
    {
        return $this->status;
    }
}
