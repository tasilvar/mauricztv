<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\video_uploader;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\modules\videos\api\controllers\Video_Controller;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;

class Video_Uploader_Page_Renderer extends Abstract_Page_Renderer
{
    private const SUPPORTED_MIME_TYPES = [
        'video/mp4', //.mp4
        'video/x-ms-asf', 'video/x-ms-wmv', //.wmv
        'video/avi', 'application/x-troff-msvideo', 'video/msvideo', 'video/x-msvideo', //.avi
        'video/quicktime' //.mov
    ];

    private Interface_View_Provider $view_provider;
    private Interface_Url_Generator $url_generator;
    private Interface_Translator $translator;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Url_Generator $url_generator,
        Interface_Translator $translator
    )
    {
        $this->view_provider = $view_provider;
        $this->url_generator = $url_generator;
        $this->translator = $translator;
    }

    public function get_rendered_page(): string
    {
        return $this->view_provider->get_admin('/pages/video-uploader/video-uploader', [
            'upload_url' => $this->url_generator->generate(Video_Controller::class, 'upload', [
                Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
            ]),
            'go_back_url' => $this->url_generator->generate_admin_page_url('admin.php', [
                'page' => Admin_Menu_Item_Slug::VIDEOS
            ]),
            'placeholder_text' => $this->translator->translate('video_uploader.drop_files_here_or_click'),
            'invalid_file_type_text' => $this->translator->translate('video_uploader.invalid_file_type'),
            'file_too_big_text' => $this->translator->translate('video_uploader.file_too_big'),
            'go_back_text' => $this->translator->translate('video_uploader.go_back'),
            'header_text' => $this->translator->translate('video_uploader.page_title'),
            'upload_still_in_progress_text' => $this->translator->translate('video_uploader.upload_still_in_progress_text'),
            'max_file_size_in_mb' => $this->get_max_file_size_from_server_settings(),
            'accepted_files' => $this->get_accepted_file_types_comma_separated(),
            'storage_not_enough_space_popup_content' => $this->view_provider->get_admin('/pages/video-uploader/bunnynet-storage-not-enough-space', [
                'translator' => $this->translator,
            ]),
            'response_error_text' => $this->translator->translate('video_uploader.response_error'),
        ]);
    }

    private function get_accepted_file_types_comma_separated(): string
    {
        return implode(',', self::SUPPORTED_MIME_TYPES);
    }

    private function get_max_file_size_from_server_settings(): int
    {
        return $this->server_upload_size_to_mib(ini_get('upload_max_filesize'));
    }

    private function server_upload_size_to_mib($upload_max_filesize): int
    {
        $bytes_value = $this->server_upload_size_to_bytes($upload_max_filesize);

        return $bytes_value / 1024 / 1024;
    }

    private function server_upload_size_to_bytes($upload_max_filesize): int
    {
        if(is_int($upload_max_filesize)) {
            return $upload_max_filesize;
        }

        $val = trim($upload_max_filesize);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            case 'g':
                $val = (int)$val * (1024 * 1024 * 1024); //1073741824
                break;
            case 'm':
                $val = (int)$val * (1024 * 1024); //1048576
                break;
            case 'k':
                $val = (int)$val * 1024;
                break;
        }

        return $val;
    }
}