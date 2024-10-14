<?php

namespace bpmj\wpidea\modules\videos\core\persistence;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\videos\core\entities\Video;
use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;
use bpmj\wpidea\modules\videos\infrastructure\persistence\Video_Query_Criteria;

interface Interface_Video_Persistence
{
    public function insert(Video $video): void;

    public function update(Video $video): void;

    public function count_by_criteria(Video_Query_Criteria $criteria): int;

    public function find_by_id(ID $video_id): array;

    public function find_by_video_id(Video_Id $video_id): array;

    public function delete(ID $video_id): void;

    public function find_all(int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array;

    public function find_by_criteria(Video_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array;

    public function setup(): void;

}