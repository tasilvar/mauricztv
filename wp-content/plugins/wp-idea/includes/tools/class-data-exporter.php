<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\tools;

use bpmj\wpidea\infrastructure\system\date\Interface_System_Datetime_Info;

class Data_Exporter
{
    public const DELIMITER_SEMICOLON = ';';
    public const DELIMITER_COMMA = ',';

    public const ENCODING_UTF8 = 'UTF-8';

    private $datetime_info;

    public function __construct(
        Interface_System_Datetime_Info $datetime_info
    ) {
        $this->datetime_info = $datetime_info;
    }

    public function output_array_to_csv(
        array $data,
        string $filename = 'export',
        string $delimiter = self::DELIMITER_COMMA,
        string $encoding = self::ENCODING_UTF8,
        bool $add_timestamp_to_filename = true
    ): void {
        $filename = $this->prepare_filename($filename, $add_timestamp_to_filename);

        $this->set_headers($filename, $encoding);

        $this->output_data($data, $delimiter);
    }

    private function prepare_filename(string $filename, bool $add_timestamp_to_filename): string
    {
        if ($add_timestamp_to_filename) {
            $timestamp = new \DateTime();
            $timestamp->setTimezone($this->datetime_info->get_current_timezone());
            $filename .= '-' . $timestamp->format('Y_m_d_H_i_s');
        }

        if (strpos($filename, '.csv') === false) {
            $filename .= '.csv';
        }

        return $filename;
    }

    private function set_headers(string $filename, string $encoding): void
    {
        header('Content-Encoding: ' . $encoding);
        header('Content-Type: application/csv; charset=' . $encoding . ';');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        header('Filename: ' . $filename);
    }

    private function output_data(array $data, string $delimiter): void
    {
        $f = fopen('php://output', 'wb');

        foreach ($data as $line) {
            fputcsv($f, $line, $delimiter);
        }

        fclose($f);
    }
}