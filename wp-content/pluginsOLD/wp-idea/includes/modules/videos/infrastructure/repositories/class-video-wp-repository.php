<?php

namespace bpmj\wpidea\modules\videos\infrastructure\repositories;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\videos\core\entities\Video;
use bpmj\wpidea\modules\videos\core\entities\Video_Collection;
use bpmj\wpidea\modules\videos\core\persistence\Interface_Video_Persistence;
use bpmj\wpidea\modules\videos\core\repositories\Interface_Video_Repository;
use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;
use bpmj\wpidea\modules\videos\infrastructure\persistence\Video_Query_Criteria;

class Video_Wp_Repository implements Interface_Video_Repository
{
    private Interface_Video_Persistence $video_persistence;

    public function __construct(Interface_Video_Persistence $video_persistence, Interface_Actions $actions)
    {
        $this->video_persistence = $video_persistence;
    }

    public function count_by_criteria(Video_Query_Criteria $criteria): int
    {
        return $this->video_persistence->count_by_criteria($criteria);
    }

    public function find_all(int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): Video_Collection
    {
        $results = $this->video_persistence->find_all($per_page, $page, $sort_by);

        return $this->table_rows_to_videos_model($results);
    }

    public function find_by_criteria(Video_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): Video_Collection
    {
        $results = $this->video_persistence->find_by_criteria($criteria, $per_page, $page, $sort_by);

        return $this->table_rows_to_videos_model($results);
    }

    public function find_by_id(ID $id): ?Video
    {
        $result = $this->video_persistence->find_by_id($id);

        if(!$result){
            return null;
        }

        return $this->table_row_to_video_model($result[0]);
    }

    public function find_by_video_id(Video_Id $id): ?Video
    {
        $result = $this->video_persistence->find_by_video_id($id);

        if(!$result){
            return null;
        }

        return $this->table_row_to_video_model($result[0]);
    }

    public function create(Video $video): void
    {
        $this->video_persistence->insert($video);
    }

    public function update(Video $video): void
    {
        $this->video_persistence->update($video);
    }

    public function delete(ID $id): void
    {
        $this->video_persistence->delete($id);
    }

    private function table_rows_to_videos_model(array $rows): Video_Collection
    {
        $videos = new Video_Collection();
        foreach ($rows as $row) {
            $videos->add( $this->table_row_to_video_model($row) );
        }

        return $videos;
    }

    private function table_row_to_video_model(array $row): Video
    {
        $id = new ID($row['id']);
        $video_id = new Video_Id($row['video_id']);
        $thumbnail_url = $row['thumbnail_url'] ? new Url($row['thumbnail_url']) : null;
        $created_at = new \DateTime($row['created_at']);

        return new Video(
            $id,
            $row['title'],
            $video_id,
            (int)$row['size'],
            (int)$row['length'],
            $row['width'],
            $row['height'],
            $thumbnail_url,
            $created_at
        );
    }

}