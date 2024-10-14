<?php

namespace bpmj\wpidea\modules\videos\web\media;

use bpmj\wpidea\admin\helpers\html\Popup;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\View;
use bpmj\wpidea\view\Interface_View_Provider;

class Media_Video_Format_Blocker_Popup {

    private Interface_Translator $translator;
    private Interface_Actions $actions;
    private Interface_Url_Generator $url_generator;
    private Interface_View_Provider $view_provider;

    private const BPMJ_WPI_MEDIA_VIDEO_FORMAT_BLOCKER_I18N = 'BPMJ_WPI_MEDIA_VIDEO_FORMAT_BLOCKER_I18N';

    public function __construct(
        Interface_Actions $actions,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Interface_View_Provider $view_provider
    ) {
        $this->actions = $actions;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->view_provider = $view_provider;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::AMIN_PRINT_FOOTER_SCRIPTS, [$this, 'print_transalation_strings_as_js_variable']);
    }

    public function print_transalation_strings_as_js_variable(): void
    {
        echo "<script>var " . self::BPMJ_WPI_MEDIA_VIDEO_FORMAT_BLOCKER_I18N . "=" . $this->get_json_translations() . "</script>";
    }

    private function get_json_translations(): string
    {
        return json_encode($this->get_popup_as_variable_in_array()) ?: '[]';
    }

    private function get_popup_as_variable_in_array(): array
    {
        return ['media_video_format_blocker_popup_html' => $this->get_limit_popup_html()];
    }

    private function get_limit_popup_html(): string
    {
        return Popup::create(
            'media-video-format-blocker-popup',
            $this->view_provider->get('views/popup/media-video-format-warning', [
                'translator' => $this->translator,
                'video_page_url' => $this->get_video_page_url()
            ])
        )->get_html();
    }

    private function get_video_page_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::VIDEOS
        ]);
    }
}