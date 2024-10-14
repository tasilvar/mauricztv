<?php

namespace bpmj\wpidea\admin\subscription\models;

use bpmj\wpidea\Software_License;

class License
{
    public const STATUS_VALID = 1;
    public const STATUS_INVALID = 0;
    private const BOX_EXPIRATION_DATE_SLUG = 'bpmj_wpidea_license_expires';

    private $key;
    private $type;
    private $status;

    public function __construct()
    {
        $this->type = WPI()->packages->get_package();
        $this->key = WPI()->trial->get_key();
        $this->status = Software_License::is_valid() ? self::STATUS_VALID : self::STATUS_INVALID;
    }

    public function get_type(): string
    {
        return $this->type;
    }

    public function get_key(): string
    {
        return $this->key;
    }

    public static function get_expiration_date(): string
    {
        return get_option(self::BOX_EXPIRATION_DATE_SLUG);
    }


    public static function set_expiration_date(string $expiration_date): void
    {
        update_option(self::BOX_EXPIRATION_DATE_SLUG, $expiration_date);
    }

}
