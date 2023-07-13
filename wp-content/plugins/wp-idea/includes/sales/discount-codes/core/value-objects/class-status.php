<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\discount_codes\core\value_objects;

use bpmj\wpidea\sales\discount_codes\core\exceptions\Invalid_Discount_Status_Exception;

class Status
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_EXPIRED = 'expired';

    private const VALID_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
        self::STATUS_EXPIRED
    ];

    private string $status;

    public function __construct(
        string $status
    ) {
        if (!in_array($status, self::VALID_STATUSES)) {
            throw new Invalid_Discount_Status_Exception(
                'Discount status invalid! Provided: ' . $status . '. Should be one of: ' . implode(
                    ', ',
                    self::VALID_STATUSES
                )
            );
        }
        $this->status = $status;
    }

    public function get_value(): string
    {
        return $this->status;
    }
}