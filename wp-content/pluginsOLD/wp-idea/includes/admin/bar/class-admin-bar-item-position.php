<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\bar;

use bpmj\wpidea\admin\bar\exceptions\Invalid_Admin_Bar_Item_Position_Exception;

class Admin_Bar_Item_Position
{
    public const INSIDE_WPI_INFO_BAR = 'inside_wpi_info_bar';

    public const BEFORE_USER_INFO = 'before_user_info';

    private const VALID_POSITIONS = [
        self::INSIDE_WPI_INFO_BAR,
        self::BEFORE_USER_INFO
    ];

    private string $position;

    private function __construct(string $position)
    {
        if(!in_array($position, self::VALID_POSITIONS)) {
            throw new Invalid_Admin_Bar_Item_Position_Exception('Invalid position! Allowed values: ' . implode(', ', self::VALID_POSITIONS));
        }

        $this->position = $position;
    }

    public function get_value(): string
    {
        return $this->position;
    }

    public function equals(Admin_Bar_Item_Position $other): bool
    {
        return $this->get_value() === $other->get_value();
    }

    public static function from_string(string $position): self
    {
        return new self($position);
    }
}