<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\logs;

use bpmj\wpidea\controllers\admin\Admin_Logs_Ajax_Controller;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\logs\model\Log;
use bpmj\wpidea\infrastructure\logs\persistence\Log_Query_Criteria;
use bpmj\wpidea\infrastructure\system\date\Interface_System_Datetime_Info;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use DateTimeImmutable;

class Logs_Table_Data_Parser
{
    private Interface_Translator $translator;

    private Url_Generator $url_generator;

    private Interface_System_Datetime_Info $datetime_info;

    public function __construct(
        Interface_Translator $translator,
        Url_Generator $url_generator,
        Interface_System_Datetime_Info $datetime_info
    ) {
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->datetime_info = $datetime_info;
    }

    public function parse_log_objects_to_plain_array(array $logs): array
    {
        $data = [];

        foreach ($logs as $log) {
            $log_local_datetime = $log->get_created_at()->setTimezone(
                $this->datetime_info->get_current_timezone()
            );

            /** @var Log $log */
            $data[] = [
                'id' => $log->get_id(),
                'created_at' => $log_local_datetime->format('Y-m-d H:i:s'),
                'level' => $log->get_level()->get_value(),
                'level_label' => $this->translator->translate('logs.level.' . $log->get_level()->get_value()),
                'source' => $log->get_source(),
                'source_label' => $this->translator->translate('logs.source.' . $log->get_source()),
                'message' => $this->format_the_message($log->get_message()),
                'delete_log_url' => $this->url_generator->generate(Admin_Logs_Ajax_Controller::class, 'delete', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
                    'id' => $log->get_id()
                ])
            ];
        }

        return $data;
    }


    public function get_criteria_from_filters_array(array $filters): Log_Query_Criteria
    {
        $level = $this->get_filter_value_if_present($filters, 'level');
        $level = $level ? (int)$level : null;

        $date_from = $this->get_filter_value_if_present($filters, 'created_at')['startDate'] ?? null;
        $date_from = $date_from ? new DateTimeImmutable($date_from) : $date_from;

        $date_to = $this->get_filter_value_if_present($filters, 'created_at')['endDate'] ?? null;
        $date_to = $date_to ? new DateTimeImmutable($date_to) : $date_to;
        if($date_to) {
            $date_to = $date_to->setTime(23, 59, 59);
        }

        $source = $this->get_filter_value_if_present($filters, 'source');

        $message = $this->get_filter_value_if_present($filters, 'message');

        return new Log_Query_Criteria($date_from, $date_to, $level, $source, $message);
    }

    /**
     * @return mixed|null
     */
    private function get_filter_value_if_present(array $filters, string $filter_name)
    {
        return array_values(array_filter($filters, static function($filter, $key) use ($filter_name) {
            return $filter['id'] === $filter_name;
        }, ARRAY_FILTER_USE_BOTH))[0]['value'] ?? null;
    }

    private function format_the_message(string $message): string
    {
        return preg_replace('~[\r\n]+~', ' ', $message);
    }
}