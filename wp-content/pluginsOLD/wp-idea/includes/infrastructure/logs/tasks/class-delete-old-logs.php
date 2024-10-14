<?php
/**
 * This file is licenses under proprietary license
 */
declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\logs\tasks;

use bpmj\wpidea\infrastructure\logs\persistence\Log_Query_Criteria;
use bpmj\wpidea\infrastructure\logs\repository\Interface_Log_Repository;
use bpmj\wpidea\infrastructure\scheduler\Interface_Schedulable;
use DateInterval;
use DateTime;

class Delete_Old_Logs implements Interface_Schedulable
{
    private const NUMBER_OF_LOGS_TO_KEEP_STORED = 20000;
    private const MAX_LOG_LIFETIME_IN_DAYS = 60;

    /**
     * @var Interface_Log_Repository
     */
    private $repository;

    public function __construct(
        Interface_Log_Repository $repository
    )
    {
        $this->repository = $repository;
    }


    public function get_method_to_run(): callable
    {
        return [$this, 'run'];
    }

    public function run(): void
    {
        $this->delete_logs_that_exceed_limit();
        $this->remove_logs_that_are_older_than_max_log_lifetime();
    }

    public function get_first_run_time(): DateTime
    {
        return new DateTime();
    }

    public function get_interval(): DateInterval
    {
        return new DateInterval(Interface_Schedulable::INTERVAL_1DAY);
    }

    public function get_args(): array
    {
        return [];
    }

    private function delete_logs_that_exceed_limit(): void
    {
        $this->repository->remove_oldest(self::NUMBER_OF_LOGS_TO_KEEP_STORED);
    }

    private function remove_logs_that_are_older_than_max_log_lifetime(): void
    {
        $lifetime_in_days = self::MAX_LOG_LIFETIME_IN_DAYS;

        $this->repository->remove_by_criteria(new Log_Query_Criteria(
            null,
            (new \DateTimeImmutable())->modify("-{$lifetime_in_days} days")
        ));
    }
}