<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\data;

use bpmj\wpidea\admin\tables\dynamic\data\exceptions\Invalid_Data_Usage_Context_Value_Exception;
use bpmj\wpidea\data_types\String_VO;

class Dynamic_Table_Data_Usage_Context extends String_VO
{
    public const DISPLAY_DATA = 'display_data';
    public const EXPORT_DATA = 'export_data';

    private const ALLOWED_VALUES = [
        self::DISPLAY_DATA,
        self::EXPORT_DATA
    ];

    public function __construct(
        string $value
    )
    {
        if(!in_array($value, self::ALLOWED_VALUES)) {
            throw new Invalid_Data_Usage_Context_Value_Exception('Value should be one of: ' . implode(',', self::ALLOWED_VALUES));
        }

        parent::__construct($value);
    }

    public static function from_value(string $value): self
    {
        return new self($value);
    }
}