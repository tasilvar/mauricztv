<?php
namespace bpmj\wpidea\modules\videos\infrastructure\persistence;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\modules\videos\core\value_objects\Video;
use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;

class Video_Query_Criteria
{
    private ?ID $id = null;
    private ?string $title = null;
    private ?Video_Id $video_id = null;
    private ?array $file_size = null;
    private ?int $length = null;
    private ?array $created_at = null;

    public function get_id(): ID
    {
        return $this->id;
    }

    public function set_id(?ID $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function get_title(): ?string
    {
        return $this->title;
    }

    public function set_title(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function get_video_id(): ?Video_Id
    {
        return $this->video_id;
    }

    public function set_video_id(?Video_Id $video_id): self
    {
        $this->video_id = $video_id;
        return $this;
    }

    public function get_file_size(): ?array
    {
        return $this->file_size;
    }

    public function set_file_size(?array $file_size): self
    {
        $this->file_size = $file_size;
        return $this;
    }

    public function get_length(): int
    {
        return $this->length;
    }

    public function set_length(?int $length): self
    {
        $this->length = $length;
        return $this;
    }

    public function get_created_at(): ?array
    {
        return $this->created_at;
    }

    public function set_created_at(?array $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

}