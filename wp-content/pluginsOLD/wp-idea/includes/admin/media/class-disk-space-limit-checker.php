<?php
declare(strict_types=1);

namespace bpmj\wpidea\admin\media;

use bpmj\wpidea\admin\video\attachment\Video_Attachment;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\infrastructure\io\Interface_Disk_Space_Checker;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\helpers\Bytes_Formatter;

class Disk_Space_Limit_Checker extends Abstract_Limit_Checker
{
    protected Interface_Filters $filters;
    private Interface_Disk_Space_Checker $disk_space_checker;
    protected Interface_Translator $translator;
    private Bytes_Formatter $bytes_formatter;

    public function __construct(
        Interface_Disk_Space_Checker $disk_space_checker,
        Interface_Filters $filters,
        Interface_Translator $translator,
        Bytes_Formatter $bytes_formatter
    )
    {
        $this->disk_space_checker = $disk_space_checker;
        $this->filters = $filters;
        $this->translator = $translator;
        $this->bytes_formatter = $bytes_formatter;
    }

    protected function the_file_type_being_checked_is_allowed(string $file_type): bool
    {
        if (in_array($file_type, Video_Attachment::SUPPORTED_MIME_TYPES)) {
          return false;
        }

        return true;
    }

    protected function limit_for_the_allowed_file_type_has_been_exceeded(int $file_size): bool
    {
        $max_space_in_byte = $this->bytes_formatter->gb_to_bytes($this->disk_space_checker->get_max());

        $sum_used_space_in_byte = $file_size + $this->disk_space_checker->get_used();

        return $sum_used_space_in_byte >= $max_space_in_byte;
    }

}