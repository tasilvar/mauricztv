<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\logs\handler;

use bpmj\wpidea\infrastructure\logs\model\Log;
use bpmj\wpidea\infrastructure\logs\model\Log_Level;
use bpmj\wpidea\infrastructure\logs\model\Log_Source;
use bpmj\wpidea\infrastructure\logs\repository\Interface_Log_Repository;
use DateTimeImmutable;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class Monolog_Db_Log_Handler extends AbstractProcessingHandler
{
    /**
     * @var Interface_Log_Repository
     */
    private $repository;

    public function __construct(
        Interface_Log_Repository $repository,
        $level = Logger::DEBUG,
        bool $bubble = true
    ) {
        $this->repository = $repository;

        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        $log = new Log(
            new DateTimeImmutable($record['datetime']->format('Y-m-d H:i:s')),
            new Log_Level($record['level']),
            $record['message'],
            $record['context']['source'] ?? Log_Source::DEFAULT
        );

        $this->repository->save($log);
    }
}