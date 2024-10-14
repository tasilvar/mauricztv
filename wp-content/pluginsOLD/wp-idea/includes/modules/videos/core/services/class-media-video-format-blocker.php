<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\videos\core\services;

use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\modules\videos\web\media\Media_Video_Format_Blocker_Popup;

class Media_Video_Format_Blocker
{
    public const MESSAGE_NOT_ALLOWED_FORMAT = 'media.video_format_blocker.error';

    private Interface_Filters $filters;
    private Interface_Translator $translator;
    private Media_Video_Format_Blocker_Popup $video_format_blocker_popup;

    private const NOT_ALLOWED_MIME_TYPES = array(
        'video/mp4',  //.mp4
        'video/x-m4', //.m4v
        'video/x-ms-asf', 'video/x-ms-wmv', //.wmv
        'video/avi', 'application/x-troff-msvideo', 'video/msvideo', 'video/x-msvideo', //.avi
        'video/mpeg', //.mpg
        'video/ogg', //.ogv
        'video/3gpp', 'audio/3gpp', //.3gp
        'video/3gpp2', 'audio/3gpp2', //.3g2
        'video/quicktime' //.mov
    );

    public function __construct(
        Interface_Filters $filters,
        Interface_Translator $translator,
        Media_Video_Format_Blocker_Popup $video_format_blocker_popup
    )
    {
        $this->filters = $filters;
        $this->translator = $translator;
        $this->video_format_blocker_popup = $video_format_blocker_popup;
    }

    public function init(): void
    {
        $this->video_format_blocker_popup->init();

        $this->filters->add(Filter_Name::HANDLE_UPLOAD_PREFILTER, [$this, 'check_the_file_type']);
    }

    public function check_the_file_type(array $file): array
    {
          if (!$this->the_file_type_being_checked_is_allowed($file['type'])){
              $file['error'] = $this->translator->translate(self::MESSAGE_NOT_ALLOWED_FORMAT);
          }

         return $file;
    }

    private function the_file_type_being_checked_is_allowed(string $file_type): bool
    {
        if (in_array($file_type, self::NOT_ALLOWED_MIME_TYPES)) {
            return false;
        }

        return true;
    }

}