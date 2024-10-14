<?php

namespace bpmj\wpidea\modules\videos\core\entities;

use bpmj\wpidea\data_types\{ID, Url};
use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;

class Video
{
    private ?ID $id;
    private ?string $title;
    private Video_Id $video_id;
    private int $file_size;
    private int $length;
    private ?int $width;
    private ?int $height;
    private ?Url $thumbnail_url;
    private ?\DateTime $created_at;

    public function __construct(
        ?ID $id,
        ?string $title,
        Video_Id $video_id,
        int $file_size,
        int $length,
        ?int $width,
        ?int $height,
        ?Url $thumbnail_url,
        ?\DateTime $created_at
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->video_id = $video_id;
        $this->file_size = $file_size;
        $this->length = $length;
        $this->thumbnail_url = $thumbnail_url;
        $this->created_at = $created_at;
        $this->width = $width;
        $this->height = $height;
    }

    public function get_width(): ?int
    {
        return $this->width;
    }

    public function get_height(): ?int
    {
        return $this->height;
    }

    public function change_title(string $title): void
    {
        $this->title = $title;
    }

    public function change_thumbnail_url(?Url $thumbnail_url): void
    {
        $this->thumbnail_url = $thumbnail_url;
    }

    public function get_id(): ?ID
    {
        return $this->id;
    }

    public function get_title(): ?string
    {
        return $this->title;
    }

    public function get_video_id(): Video_Id
    {
        return $this->video_id;
    }

    public function get_file_size(): int
    {
        return $this->file_size;
    }

    public function get_length(): int
    {
        return $this->length;
    }

    public function get_thumbnail_url(): ?Url
    {
        return $this->thumbnail_url;
    }

    public function get_created_at(): ?\DateTime
    {
        return $this->created_at;
    }

    public function is_processing(): bool
    {
        return $this->get_file_size() == 0;
    }
}