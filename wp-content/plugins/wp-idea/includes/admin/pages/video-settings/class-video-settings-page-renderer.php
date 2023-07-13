<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\video_settings;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\modules\videos\api\controllers\Video_Controller;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\modules\videos\core\repositories\Interface_Video_Repository;
use bpmj\wpidea\data_types\ID;

class Video_Settings_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;
    private Interface_Url_Generator $url_generator;
    private Interface_Translator $translator;
    private Current_Request $current_request;
    private Interface_Video_Repository $video_repository;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Url_Generator $url_generator,
        Interface_Translator $translator,
        Current_Request $current_request,
        Interface_Video_Repository $video_repository
    )
    {
        $this->view_provider = $view_provider;
        $this->url_generator = $url_generator;
        $this->translator = $translator;
        $this->current_request = $current_request;
        $this->video_repository = $video_repository;
    }

    public function get_rendered_page(): string
    {
        $fields_form = [];

        if($this->current_request->query_arg_exists('id')){
            $id = (int) $this->current_request->get_request_arg('id');
            $fields_form = $this->get_data_to_the_form_by_id($id);
        }

        return $this->view_provider->get_admin('/pages/video-settings/index', [
            'fields' => $fields_form,
            'save_settings_url' => $this->url_generator->generate(Video_Controller::class, 'settings', [
                Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
            ]),
            'go_back_url' => $this->url_generator->generate_admin_page_url('admin.php', [
                'page' => Admin_Menu_Item_Slug::VIDEOS
            ]),
            'translator' => $this->translator
        ]);
    }

    private function get_data_to_the_form_by_id(int $id): array
    {
        if(!$id){
            return [];
        }

       $video = $this->video_repository->find_by_id(new ID($id));

        if(!$video){
            return [];
        }

        return [
            'id' => $id,
            'title' => $video->get_title(),
            'thumbnail_url' => $video->get_thumbnail_url() ? $video->get_thumbnail_url()->get_value() : null
        ];
    }
}