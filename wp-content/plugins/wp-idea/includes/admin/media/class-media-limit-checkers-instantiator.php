<?php
declare(strict_types=1);

namespace bpmj\wpidea\admin\media;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\admin\video\Vimeo_Limit_Checker;

class Media_Limit_Checkers_Instantiator implements Interface_Initiable
{
    private Disk_Space_Limit_Checker $disk_space_limit_checker;
    private Vimeo_Limit_Checker $vimeo_limit_checker;
    private Media_Limit_Popup $media_limit_popup;

    public function __construct(
        Disk_Space_Limit_Checker $disk_space_limit_checker,
        Vimeo_Limit_Checker $vimeo_limit_checker,
        Media_Limit_Popup $media_limit_popup
    )
    {
        $this->disk_space_limit_checker = $disk_space_limit_checker;
        $this->vimeo_limit_checker = $vimeo_limit_checker;
        $this->media_limit_popup = $media_limit_popup;
    }

    public function init(): void
    {
        $this->disk_space_limit_checker->init();
        $this->vimeo_limit_checker->init();
        $this->media_limit_popup->init();
    }
}