<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\sales\product\model;

class Gtu
{
    public const NO_GTU = 'no_gtu';
    public const GTU_01 = 'gtu_01';
    public const GTU_02 = 'gtu_02';
    public const GTU_03 = 'gtu_03';
    public const GTU_04 = 'gtu_04';
    public const GTU_05 = 'gtu_05';
    public const GTU_06 = 'gtu_06';
    public const GTU_07 = 'gtu_07';
    public const GTU_08 = 'gtu_08';
    public const GTU_09 = 'gtu_09';
    public const GTU_10 = 'gtu_10';
    public const GTU_11 = 'gtu_11';
    public const GTU_12 = 'gtu_12';
    public const GTU_13 = 'gtu_13';

    public const AVAILABLE_CODES = [
        self::GTU_01,
        self::GTU_02,
        self::GTU_03,
        self::GTU_04,
        self::GTU_05,
        self::GTU_06,
        self::GTU_07,
        self::GTU_08,
        self::GTU_09,
        self::GTU_10,
        self::GTU_11,
        self::GTU_12,
        self::GTU_13
    ];

    protected $code;

    public function __construct(string $code)
    {
        if(!in_array($code, self::AVAILABLE_CODES)) {
            $code = self::NO_GTU;
        }

        $this->code = $code;
    }

    public function equals(self $gtu): bool
    {
        return $gtu->get_code() === $this->get_code();
    }
 
    public function get_code(): string
    {
        return $this->code;
    }
}
