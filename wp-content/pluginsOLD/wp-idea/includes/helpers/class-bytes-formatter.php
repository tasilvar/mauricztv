<?php
namespace bpmj\wpidea\helpers;

class Bytes_Formatter
{
    private const UNITS = ['B', 'KB', 'MB', 'GB', 'TB'];

    public function to_formatted_string(int $bytes): string
    {
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count(self::UNITS) - 1);

        $bytes /= pow(1000, $pow);

        return round($bytes, 2) . ' ' . self::UNITS[$pow];
    }

    public function gb_to_bytes(int $size): int
    {
        return $size * pow(1024, 3);
    }

    public function mb_to_bytes(int $size): int
    {
        return $size * pow(1024, 2);
    }
}
