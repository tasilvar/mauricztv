<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\quizzes;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\learning\quiz\Resolved_Quiz_Query_Criteria;
use bpmj\wpidea\routing\Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_User_Repository;
use DateTimeImmutable;

abstract class Quiz_Table_Data_Parser
{
    /**
     * @var Interface_Translator
     */
    protected $translator;

    /**
     * @var Url_Generator
     */
    protected $url_generator;

    protected $user_repository;

    public function __construct(
        Interface_Translator $translator,
        Url_Generator $url_generator,
        Interface_User_Repository $user_repository
    ) {
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->user_repository = $user_repository;
    }

    abstract public function parse_models_to_plain_array(array $quizzes): array;

    public function process_sort_by(array $sort_by): Sort_By_Clause
    {
        $default_sort_by = (new Sort_By_Clause())
            ->sort_by('created_at', true);

        if(empty($sort_by)) {
            return $default_sort_by;
        }

        $parsed_sort_by = new Sort_By_Clause();

        foreach ($sort_by as $sort_by_condition) {
            $parsed_sort_by->sort_by($sort_by_condition['id'], $sort_by_condition['desc']);
        }

        return $parsed_sort_by;
    }

    public function get_criteria_from_filters_array(array $filters): Resolved_Quiz_Query_Criteria
    {
        $course = $this->get_filter_value_if_present($filters, 'course');
        $title = $this->get_filter_value_if_present($filters, 'title');
        $user_full_name = $this->get_filter_value_if_present($filters, 'user_full_name');
        $result = $this->get_filter_value_if_present($filters, 'result');

        $date_from = $this->get_filter_value_if_present($filters, 'date')['startDate'] ?? null;
        $date_from = $date_from ? new DateTimeImmutable($date_from) : $date_from;

        $date_to = $this->get_filter_value_if_present($filters, 'date')['endDate'] ?? null;
        $date_to = $date_to ? new DateTimeImmutable($date_to) : $date_to;
        if($date_to) {
            $date_to = $date_to->setTime(23, 59, 59);
        }

        $user_email = $this->get_filter_value_if_present($filters, 'user_email');

        return new Resolved_Quiz_Query_Criteria($course, $title, $user_full_name, $user_email, $result, $date_from, $date_to);
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

    protected function get_result_label(string $result): string
    {
        return $this->translator->translate('quiz.result.' . $result);
    }
}