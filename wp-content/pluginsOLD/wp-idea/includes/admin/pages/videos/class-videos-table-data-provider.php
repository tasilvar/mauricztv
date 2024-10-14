<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\videos;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\helpers\{Bytes_Formatter, Video_Length_Formatter};
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\videos\api\controllers\Video_Controller;
use bpmj\wpidea\modules\videos\core\entities\{Video, Video_Collection};
use bpmj\wpidea\modules\videos\core\repositories\Interface_Video_Repository;
use bpmj\wpidea\modules\videos\infrastructure\persistence\Video_Query_Criteria;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;

class Videos_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{

    private const EMPTY = '--';
    private Interface_Url_Generator $url_generator;
    private Interface_Video_Repository $video_repository;
    private Bytes_Formatter $bytes_formatter;
    private Video_Length_Formatter $video_length_formatter;
    private Interface_Translator $translator;

    public function __construct(
        Interface_Url_Generator $url_generator,
        Interface_Video_Repository $video_repository,
        Bytes_Formatter $bytes_formatter,
        Video_Length_Formatter $video_length_formatter,
        Interface_Translator $translator
    ) {
        $this->url_generator = $url_generator;
        $this->video_repository = $video_repository;
        $this->bytes_formatter = $bytes_formatter;
        $this->video_length_formatter = $video_length_formatter;
        $this->translator = $translator;
    }

    public function get_total(array $filters): int
    {
        return $this->video_repository->count_by_criteria(
            $this->get_criteria_from_filters($filters)
        );
    }

    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array {

        $entities = $this->video_repository->find_by_criteria(
            $this->get_criteria_from_filters($filters),
            $per_page,
            $page,
            $sort_by
        );

        return $this->parse_entities_collection_to_array($entities);
    }

    private function parse_entities_collection_to_array(
        Video_Collection $entities
    ): array {

        return array_map(fn(Video $entity) => [
            'id' => $entity->get_id()->to_int(),
            'title' => $entity->get_title(),
            'size' => $this->format_size($entity),
            'length' => $this->format_length($entity),
            'created_at' => $this->format_date($entity),
            'delete_video' => $this->get_delete_video_url($entity),
            'edit_settings' => $this->get_edit_settings_url($entity)
        ], iterator_to_array($entities));
    }

    private function format_size(Video $video): string
    {
        if(!$this->is_processing($video)){
            return $this->bytes_formatter->to_formatted_string($video->get_file_size());
        }

        return $this->translator->translate('videos.column.actions.processing');
    }

    private function format_length(Video $video): string
    {
        if(!$this->is_processing($video)){
            return $this->video_length_formatter->to_formatted_string($video->get_length());
        }

        return self::EMPTY;
    }

    private function format_date(Video $video): string
    {
        if(!$this->is_processing($video)){

            return $video->get_created_at()->format('Y-m-d H:i:s');
        }

        return self::EMPTY;
    }

    private function is_processing(Video $video): bool
    {
       if($video->get_file_size()){
         return false;
       }
        return true;
    }

    private function get_delete_video_url(Video $video): string
    {
        return $this->url_generator->generate(Video_Controller::class, 'delete', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $video->get_id()->to_int()
        ]);
    }

    private function get_edit_settings_url(Video $video): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::VIDEO_SETTINGS,
            'id' => $video->get_id()->to_int(),
        ]);
    }

    private function get_criteria_from_filters(array $filters): Video_Query_Criteria
    {
        $criteria = new Video_Query_Criteria();

        $criteria->set_title($this->get_filter_value_if_present($filters, 'title'));
        $criteria->set_file_size($this->get_filter_value_if_present($filters, 'size'));
        $criteria->set_created_at($this->get_filter_value_if_present($filters, 'created_at'));

        return $criteria;
    }

    private function get_filter_value_if_present(array $filters, string $filter_name)
    {
        return array_values(
                array_filter($filters, static function ($filter, $key) use ($filter_name) {
                    return $filter['id'] === $filter_name;
                }, ARRAY_FILTER_USE_BOTH)
            )[0]['value'] ?? null;
    }
}