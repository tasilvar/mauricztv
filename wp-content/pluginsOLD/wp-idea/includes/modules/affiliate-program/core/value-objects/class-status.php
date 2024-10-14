<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\core\value_objects;

use bpmj\wpidea\modules\affiliate_program\core\exceptions\Invalid_Commission_Status_Exception;

class Status
{
    public const STATUS_SETTLED = 'settled';
    public const STATUS_UNSETTLED = 'unsettled';

    public const VALID_STATUSES = [
        self::STATUS_SETTLED,
        self::STATUS_UNSETTLED
    ];

    private string $status;

    public function __construct(
        string $status
    ) {
        if (!in_array($status, self::VALID_STATUSES)) {
            throw new Invalid_Commission_Status_Exception(
                'Commission status invalid! Provided: ' . $status . '. Should be one of: ' . implode(
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