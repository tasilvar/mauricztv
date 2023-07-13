<?php

namespace bpmj\wpidea\modules\videos\core\providers;

use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\modules\videos\core\entities\Video_Collection;
use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;

interface Interface_Video_Provider
{
    public function upload_video(string $title, string $source_path): bool;

    public function upload_video_from_url(Url $video_url): bool;

    public function set_thumbnail(Video_Id $video_id, Url $thumbnail_url): bool;

    public function update_video_title(Video_Id $video_id, string $title): bool;

    public function delete_video(Video_Id $video_id): bool;

    public function get_video_collections(int $page = 1, int $per_page = 100): Video_Collection;

    public function get_video_details(Video_Id $video_id): array;

    public function count_videos_in_collection(): int;
}