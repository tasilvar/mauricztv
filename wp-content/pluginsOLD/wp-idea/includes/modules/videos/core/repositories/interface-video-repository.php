<?php

namespace bpmj\wpidea\modules\videos\core\repositories;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\modules\videos\core\entities\Video;
use bpmj\wpidea\modules\videos\core\entities\Video_Collection;
use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;
use bpmj\wpidea\modules\videos\infrastructure\persistence\Video_Query_Criteria;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;

interface Interface_Video_Repository
{
    public function count_by_criteria(Video_Query_Criteria $criteria): int;

    public function find_all(int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): Video_Collection;

    public function find_by_criteria(Video_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): Video_Collection;

    public function find_by_id(ID $id): ?Video;

    public function find_by_video_id(Video_Id $id): ?Video;

    public function create(Video $video): void;

    public function update(Video $video): void;

    public function delete(ID $id): void;
}