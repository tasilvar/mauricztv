<?php
declare(strict_types=1);

namespace bpmj\wpidea\admin\media;

use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\translator\Interface_Translator;

abstract class Abstract_Limit_Checker
{
    protected Interface_Translator $translator;
    protected Interface_Filters $filters;

    public const MESSAGE_LIMIT_EXCEEDED = 'media.limit_checker.error';

    public function init(): void
    {
        $this->filters->add(Filter_Name::HANDLE_UPLOAD_PREFILTER, [$this, 'check_limit']);
    }

    public function check_limit(array $file): array
    {
         if ( !$this->the_file_type_being_checked_is_allowed( $file['type'] ) ) {
             return $file;
         }

        if ( $this->limit_for_the_allowed_file_type_has_been_exceeded( $file['size'] ) ) {
            $file['error'] = $this->translator->translate(self::MESSAGE_LIMIT_EXCEEDED);
        }

        return $file;
    }

    abstract protected function the_file_type_being_checked_is_allowed(string $file_type): bool;

    abstract protected function limit_for_the_allowed_file_type_has_been_exceeded(int $file_size): bool;
}