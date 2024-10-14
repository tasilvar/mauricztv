<?php
namespace bpmj\wpidea\modules\webhooks\core\value_objects;
use Exception;

class Webhook_Status
{

    public const ACTIVE = 1;
    public const SUSPENDED = 0;

    public const STATUS_ACTIVE_NAME  = 'active';
    public const STATUS_SUSPENDED_NAME  = 'suspended';


    public const VALID_STATUS = [
        self::ACTIVE,
        self::SUSPENDED
    ];

    private int $status;

    public function __construct(int $status)
    {
        if(!in_array($status, self::VALID_STATUS, true)) {
            throw new Exception('Invalid webhook status provided!');
        }

        $this->status = $status;
    }

    public function equals(Webhook_Status $other_status): bool
    {
        return $this->get_value() === $other_status->get_value();
    }

    public function get_value(): int
    {
        return $this->status;
    }

    public function get_name(): string
    {
        return ($this->status === self::ACTIVE)  ? self::STATUS_ACTIVE_NAME : self::STATUS_SUSPENDED_NAME;
    }
}