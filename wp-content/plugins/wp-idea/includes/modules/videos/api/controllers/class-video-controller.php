<?php

namespace bpmj\wpidea\modules\videos\api\controllers;

use bpmj\wpidea\admin\helpers\utils\Snackbar;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\videos\core\entities\Video;
use bpmj\wpidea\modules\videos\core\providers\Interface_Video_Provider;
use bpmj\wpidea\modules\videos\core\services\Bunny_Net_Video_List_Sync_Service;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\modules\videos\core\repositories\Interface_Video_Repository;
use bpmj\wpidea\data_types\{ID, Url};
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\admin\video\events\Video_Event_Name;
use bpmj\wpidea\infrastructure\io\video\Interface_Video_Space_Checker;

class Video_Controller extends Ajax_Controller
{
    private Interface_Video_Provider $video_provider;
    private Bunny_Net_Video_List_Sync_Service $sync_service;
    private Interface_Video_Repository $video_repository;
    private Snackbar $snackbar;
    private Interface_Events $events;
    private Interface_Video_Space_Checker $video_space_checker;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Video_Provider $video_provider,
        Interface_Video_Repository $video_repository,
        Bunny_Net_Video_List_Sync_Service $sync_service,
        Snackbar $snackbar,
        Interface_Events $events,
        Interface_Video_Space_Checker $video_space_checker
    ) {
        $this->video_provider = $video_provider;
        $this->video_repository = $video_repository;
        $this->sync_service = $sync_service;
        $this->snackbar = $snackbar;
        $this->events = $events;
        $this->video_space_checker = $video_space_checker;
        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => [Caps::ROLE_LMS_CONTENT_MANAGER],
            'caps' => [Caps::CAP_MANAGE_SETTINGS],
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function upload_action(Current_Request $current_request): string
    {
        $file = $current_request->get_file('file');
        $mime = mime_content_type($file['tmp_name']);

        if(!strstr($mime, "video/")){
            return $this->return_as_json(self::STATUS_ERROR, ['error' => $this->translator->translate('video_uploader.invalid_file_type')]);
        }

        if ($this->video_space_checker->will_uploading_file_of_size_exceed_max_usage($file['size'])) {
            return $this->return_as_json(self::STATUS_ERROR, ['error' => $this->translator->translate('video_uploader.storage_not_enough_space')]);
        }

        $title = $file['name'];

        $this->video_provider->upload_video($title, $file['tmp_name']);
        $this->sync_service->sync(false);
        return $this->return_as_json(
            self::STATUS_SUCCESS
        );
    }

    public function delete_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');
        $id = new ID($id);
        $video = $this->video_repository->find_by_id($id);

        if (!$video) {
            return $this->return_as_json(
                self::STATUS_ERROR,
                [
                    'message' => $this->translator->translate('videos.actions.delete.error')
                ]
            );
        }

        $this->delete_the_video_file_and_record_in_the_database($video, $id);

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => $this->translator->translate('videos.actions.delete.success')
            ]
        );
    }

    public function delete_bulk_action(Current_Request $current_request): string
    {
        $request_body = $current_request->get_decoded_raw_post_data();
        $ids = $request_body['ids'] ?? [];

        foreach ($ids as $id) {
            $id = new ID((int)$id);

            $video = $this->video_repository->find_by_id($id);

            if (!$video) {
                return $this->return_as_json(
                    self::STATUS_ERROR,
                    [
                        'message' => $this->translator->translate('videos.actions.delete.error')
                    ]
                );
            }

            $this->delete_the_video_file_and_record_in_the_database($video, $id);
        }

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => $this->translator->translate('videos.actions.delete.bulk.success')
            ]
        );
    }

    public function settings_action(Current_Request $current_request): void
    {
        $video_settings = $current_request->get_request_arg('video_settings');

        if(!$video_settings){
            $this->redirector->redirect_back();
        }

        $video = $this->video_repository->find_by_id(new ID($video_settings['id']));

        if(!$video){
            $this->redirector->redirect_back();
        }

        $thumbnail_url = $video_settings['thumbnail_url'] ? new Url($video_settings['thumbnail_url']) : null;
        $video_title = filter_var($video_settings['title'], FILTER_SANITIZE_STRING);

        if($thumbnail_url){
            $this->video_provider->set_thumbnail($video->get_video_id(), $thumbnail_url);
        }

        $video->change_title($video_title);
        $video->change_thumbnail_url($thumbnail_url);
        
        $this->video_repository->update($video);
        $this->video_provider->update_video_title($video->get_video_id(), $video_title);

        $this->snackbar->display_message_on_next_request($this->translator->translate('videos.actions.settings.success'));
        $this->redirector->redirect($video_settings['redirect_videos_page']);
    }

    private function delete_the_video_file_and_record_in_the_database(Video $video, ID $id): void
    {
        $video_id = $video->get_video_id();
        $this->video_provider->delete_video($video_id);
        $this->video_repository->delete($id);

        $this->events->emit(Video_Event_Name::REMOTE_VIDEO_DELETED, ['remote_video_id' => $video_id]);
    }
}
