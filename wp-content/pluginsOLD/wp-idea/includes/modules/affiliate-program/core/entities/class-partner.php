<?php

namespace bpmj\wpidea\modules\affiliate_program\core\entities;

use bpmj\wpidea\data_types\mail\Email_Address;
use bpmj\wpidea\data_types\personal_data\Full_Name;
use bpmj\wpidea\user\User_ID;
use DateTime;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Affiliate_ID;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_ID;

class Partner
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const VALID_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE
    ];

    private User_ID $user_id;
    private Affiliate_ID $affiliate_id;
    private Full_Name $full_name;
    private Email_Address $email;
    private ?Partner_ID $id;
    private ?DateTime $created_at;
    private bool $is_active;
    private Partner_Stats $stats;

    public function __construct(
        ?Partner_ID $id,
        User_ID $user_id,
        Affiliate_ID $affiliate_id,
        Full_Name $full_name,
        Email_Address $email,
        ?DateTime $created_at,
        bool $is_active,
        Partner_Stats $stats
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->affiliate_id = $affiliate_id;
        $this->full_name = $full_name;
        $this->email = $email;
        $this->created_at = $created_at;
        $this->is_active = $is_active;
        $this->stats = $stats;
    }

    public function get_id(): ?Partner_ID
    {
        return $this->id;
    }

    public function get_user_id(): User_ID
    {
        return $this->user_id;
    }

    public function get_affiliate_id(): Affiliate_ID
    {
        return $this->affiliate_id;
    }

    public function get_full_name(): Full_Name
    {
        return $this->full_name;
    }

    public function get_email(): Email_Address
    {
        return $this->email;
    }

    public function get_created_at(): ?DateTime
    {
        return $this->created_at;
    }

    public function is_active(): bool
    {
        return $this->is_active;
    }

    public function get_stats(): Partner_Stats
    {
        return $this->stats;
    }
}