<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\resources;

use bpmj\wpidea\resources\exceptions\Unsupported_Type_Exception;

class Resource_Type
{
    public const DIGITAL_PRODUCT = 'digital_product';
    public const COURSE = 'course';
    public const SERVICE = 'service';
    public const PHYSICAL_PRODUCT = 'physical_product';
    public const BUNDLE = 'bundle';

    public const SUPPORTED_TYPES = [
        self::DIGITAL_PRODUCT,
        self::COURSE,
        self::SERVICE,
        self::PHYSICAL_PRODUCT,
        self::BUNDLE,
    ];

    private string $name;

    public function __construct(string $type_name)
    {
        if(!in_array($type_name, self::SUPPORTED_TYPES)) {
            throw new Unsupported_Type_Exception('Cannot recognize type ' . $type_name . '! Supported types: ' . implode(', ', self::SUPPORTED_TYPES));
        }

        $this->name = $type_name;
    }

    public function equals(self $type): bool
    {
        return $this->get_name() === $type->get_name();
    }

    public function get_name(): string
    {
        return $this->name;
    }
}