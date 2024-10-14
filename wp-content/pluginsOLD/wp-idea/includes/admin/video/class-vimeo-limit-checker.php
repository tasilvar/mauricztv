<?php

namespace bpmj\wpidea\admin\video;

use bpmj\wpidea\admin\media\Abstract_Limit_Checker;
use bpmj\wpidea\admin\video\attachment\Video_Attachment;
use bpmj\wpidea\admin\video\settings\Videos_Settings;
use bpmj\wpidea\infrastructure\io\video\Interface_Video_Space_Checker;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\translator\Interface_Translator;

class Vimeo_Limit_Checker extends Abstract_Limit_Checker
{
    protected Interface_Filters $filters;
    protected Interface_Translator $translator;
    private Interface_Video_Space_Checker $video_space_checker;

    public function __construct(
        Interface_Filters $filters,
        Interface_Translator $translator,
        Interface_Video_Space_Checker $video_space_checker
    ) {
        $this->filters = $filters;
        $this->translator = $translator;
        $this->video_space_checker = $video_space_checker;
    }

    protected function the_file_type_being_checked_is_allowed(string $file_type): bool
    {
        if (! Videos_Settings::is_vimeo_upload_enabled()) {
            return false;
        }

        if (!in_array($file_type, Video_Attachment::SUPPORTED_MIME_TYPES)) {
            return false;
        }

        return true;
    }

    protected function limit_for_the_allowed_file_type_has_been_exceeded(int $file_size): bool
    {
        $usage = $this->video_space_checker->get_usage_info();

        $new_current = $usage->get_current_storage_usage_in_bytes() + $file_size;

        return $new_current >= $usage->get_max_storage_usage_in_bytes();
    }
}