<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\video\events;

class Video_Event_Name
{
    public const REMOTE_VIDEO_DELETED = 'video_deleted_from_remote';

    public const VIDEO_STATUSES_CHECK_FINISHED = 'videos_statuses_check_finished';

    public const VIDEO_STATUS_UPDATED = 'video_status_updated';
}