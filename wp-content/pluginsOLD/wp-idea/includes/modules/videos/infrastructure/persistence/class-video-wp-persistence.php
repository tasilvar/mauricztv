<?php

namespace bpmj\wpidea\modules\videos\infrastructure\persistence;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\modules\videos\core\entities\Video;
use bpmj\wpidea\modules\videos\core\persistence\Interface_Video_Persistence;
use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\helpers\Bytes_Formatter;

class Video_Wp_Persistence implements Interface_Video_Persistence
{
    private const TABLE_NAME = 'wpi_videos';
    private const MAX_VAL = 9999;

    private Interface_Database $db;
    private Bytes_Formatter $bytes_formatter;

    public function __construct(
        Interface_Database $db,
        Bytes_Formatter $bytes_formatter
    )
    {
        $this->db = $db;
        $this->bytes_formatter = $bytes_formatter;
    }

    public function insert(Video $video): void
    {
        $this->db->insert(self::TABLE_NAME, [
            'title' => $video->get_title(),
            'video_id' => $video->get_video_id()->get_id(),
            'size' => $video->get_file_size(),
            'length' => $video->get_length(),
            'width' => $video->get_width(),
            'height' => $video->get_height(),
            'created_at' => $video->get_created_at()->format('Y-m-d H:i:s')
        ]);
    }

    public function update(Video $video): void
    {
        $thumbnail_url = $video->get_thumbnail_url() ? $video->get_thumbnail_url()->get_value() : null;

        $this->db->update_rows(self::TABLE_NAME, [
            ['title', $video->get_title()],
            ['video_id', $video->get_video_id()->get_id()],
            ['size', $video->get_file_size()],
            ['length', $video->get_length()],
            ['width', $video->get_width()],
            ['height', $video->get_height()],
            ['thumbnail_url', $thumbnail_url],
            ['created_at', $video->get_created_at()->format('Y-m-d H:i:s')]
        ], [['id', '=', $video->get_id()->to_int()]]);
    }

    public function count_by_criteria(Video_Query_Criteria $criteria): int
    {
        $where = $this->parse_criteria_to_where_clause($criteria);
        return $this->db->count(self::TABLE_NAME, $where);
    }

    public function find_by_id(ID $video_id): array
    {
        return $this->db->get_results(
            self::TABLE_NAME,
            ['id', 'title', 'video_id', 'size', 'length','width','height', 'thumbnail_url', 'created_at'],
            [['id', '=', $video_id->to_int()]],
            1
        );
    }

    public function find_by_video_id(Video_Id $video_id): array
    {
        return $this->db->get_results(
            self::TABLE_NAME,
            ['id', 'title', 'video_id', 'size', 'length','width','height', 'thumbnail_url', 'created_at'],
            [['video_id', '=', $video_id->get_id()]],
            1
        );
    }

    public function find_all(int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array
    {
        $is_paginated = $per_page > 0;
        $skip = !$is_paginated ? 0 : ($per_page * ($page - 1));

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'title',
            'video_id',
            'size',
            'length',
            'width',
            'height',
            'thumbnail_url',
            'created_at',
        ], [], $per_page, $skip, $sort_by);
    }

    public function find_by_criteria(Video_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array
    {
        $is_paginated = $per_page > 0;
        $skip = !$is_paginated ? 0 : ($per_page * ($page - 1));
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'title',
            'video_id',
            'size',
            'length',
            'width',
            'height',
            'thumbnail_url',
            'created_at',
        ], $where, $per_page, $skip, $sort_by);
    }

    public function delete(ID $video_id): void
    {
        $this->db->delete_rows(
            self::TABLE_NAME,
            [['id', '=', $video_id->to_int()]]
        );
    }

    public function setup(): void
    {
        $this->db->create_table_if_not_exists(
            self::TABLE_NAME,
            [
                'id int UNSIGNED NOT NULL AUTO_INCREMENT',
                'title varchar(255) NOT NULL',
                'video_id varchar(50) NOT NULL',
                'size int UNSIGNED NOT NULL',
                'width int UNSIGNED DEFAULT NULL',
                'height int UNSIGNED DEFAULT NULL',
                'length int UNSIGNED NOT NULL',
                'thumbnail_url varchar(255)',
                'created_at datetime NOT NULL',
            ],
            'id',
            [
                "KEY (video_id)",
            ]
        );
    }

    private function parse_criteria_to_where_clause(Video_Query_Criteria $criteria): array
    {
        $where = [];

        if($criteria->get_title()) {
            $where[] = ['title', 'LIKE', $criteria->get_title()];
        }

        if ($file_size = $criteria->get_file_size()) {
            $min = $file_size[0] ?? 0;
            $max = $file_size[1] ?? self::MAX_VAL;

            if ($min) {
                $where[] = ['size', 'MIN', $this->bytes_formatter->mb_to_bytes($min)];
            }

            if ($max) {
                $where[] = ['size', 'MAX', $this->bytes_formatter->mb_to_bytes($max)];
            }
        }

        if ($criteria->get_created_at()) {
            $date = $criteria->get_created_at();
            $startDate = $date['startDate'];
            $endDate = $date['endDate'];

            if ($startDate) {
                $where[] = ['created_at', '>=', $startDate];
            }

            if ($endDate) {
                $where[] = ['created_at', '<=', $endDate];
            }
        }

        return $where;
    }
}