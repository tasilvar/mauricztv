<?php

namespace bpmj\wpidea\modules\videos\web\video_changes_info;

use bpmj\wpidea\admin\Notices;
use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;

class Video_Changes_Info
{
    private const INFO_SHOWING_START_TIMESTAMP_OPTION_NAME = 'publigo_video_changes_info_showing_start_timestamp';

    private const INFO_LIFESPAN_IN_DAYS = 60;

    private Notices $notices;
    private Interface_Options $options;
    private Interface_View_Provider $view_provider;
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Notices $notices,
        Interface_Options $options,
        Interface_View_Provider $view_provider,
        Interface_Url_Generator $url_generator
    )
    {
        $this->notices = $notices;
        $this->options = $options;
        $this->view_provider = $view_provider;
        $this->url_generator = $url_generator;
    }

    public function start_showing_info(): void
    {
        $this->options->set(self::INFO_SHOWING_START_TIMESTAMP_OPTION_NAME, time());
    }

    public function init(): void
    {
        if(!$this->should_info_be_displayed()) {
            return;
        }

        $this->notices->display_custom_html_notice($this->get_info_message_content());
    }

    private function get_info_showing_end_timestamp(): ?int
    {
        $info_showing_start_timestamp = $this->options->get(self::INFO_SHOWING_START_TIMESTAMP_OPTION_NAME);

        if(!$info_showing_start_timestamp) {
            return null;
        }

        return time() + (self::INFO_LIFESPAN_IN_DAYS * 24 * 60 * 60);
    }

    private function should_info_be_displayed(): bool
    {
        if(!$this->is_on_media_page()) {
            return false;
        }

        if(!$this->is_info_in_display_time_range()) {
            return false;
        }

        return true;
    }

    private function is_info_in_display_time_range(): bool
    {
        $info_showing_end_timestamp = $this->get_info_showing_end_timestamp();

        if(!$info_showing_end_timestamp) {
            return false;
        }

        if(time() > $info_showing_end_timestamp) {
            return false;
        }

        return true;
    }

    private function is_on_media_page(): bool
    {
        global $pagenow;

        return $pagenow === 'upload.php';
    }

    protected function get_info_message_content(): string
    {
        return $this->view_provider->get('views/info', [
            'video_page_url' => $this->url_generator->generate_admin_page_url('admin.php', [
                'page' => Admin_Menu_Item_Slug::VIDEOS
            ]),
            'docs_url' => 'https://publigo.pl/blog/nowy-hosting-wideo/'
        ]);
    }
}