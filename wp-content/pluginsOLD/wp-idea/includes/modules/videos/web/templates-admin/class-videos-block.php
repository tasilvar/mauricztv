<?php

namespace bpmj\wpidea\modules\videos\web\templates_admin;

use bpmj\wpidea\events\actions\{Action_Name, Interface_Actions};
use bpmj\wpidea\modules\videos\core\repositories\Interface_Video_Repository;
use bpmj\wpidea\modules\videos\core\services\Interface_Video_Player_Renderer;
use bpmj\wpidea\modules\videos\core\services\Video_Embed_Content_Type_Checker;
use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;
use bpmj\wpidea\translator\Interface_Translator;

class Videos_Block
{

    private const BPMJ_WPI_VIDEOS_BLOCK = 'BPMJ_WPI_VIDEOS_BLOCK';
    private const SCRIPT_VIDEOS_BLOCK = 'wpi-videos-block';
    private const NAME_VIDEOS_BLOCK = 'wpi/videos';
    private const MEDIA_BLOCKS_CAT = 'media';
    private const KEY_NAME_IN_ARRAY = 'select_options';

    private Interface_Actions $actions;
    private Interface_Translator $translator;
    private Interface_Video_Player_Renderer $video_player_renderer;
    private Interface_Video_Repository $video_repository;
    private Video_Embed_Content_Type_Checker $video_embed_content_type_checker;

    public function __construct(
        Interface_Actions $actions,
        Interface_Translator $translator,
        Interface_Video_Player_Renderer $video_player_renderer,
        Interface_Video_Repository $video_repository,
        Video_Embed_Content_Type_Checker $video_embed_content_type_checker
    ) {
        $this->actions = $actions;
        $this->translator = $translator;
        $this->video_player_renderer = $video_player_renderer;
        $this->video_repository = $video_repository;
        $this->video_embed_content_type_checker = $video_embed_content_type_checker;
    }

    public function init()
    {
        if ($this->video_embed_content_type_checker->can_video_be_embedded_on_current_page()) {
            $this->actions->add(Action_Name::ENQUEUE_BLOCK_EDITOR_ASSETS, [$this, 'load_videos_block']);
            $this->actions->add(Action_Name::ENQUEUE_BLOCK_EDITOR_ASSETS, [$this, 'load_videos_block_data']);
        }
        $this->actions->add(Action_Name::INIT, [$this, 'register_block_type']);
    }

    public function register_block_type(): void
    {
        register_block_type(self::NAME_VIDEOS_BLOCK, [
            'editor_script' => self::SCRIPT_VIDEOS_BLOCK,
            'render_callback' => function ($atts) {
                return $this->get_player_html($atts['videoID'] ?? null);
            }
        ]);
    }

    public function load_videos_block(): void
    {
        wp_enqueue_script(
            self::SCRIPT_VIDEOS_BLOCK,
            BPMJ_EDDCM_URL . '/includes/modules/videos/web/templates-admin/assets/videos-block.js',
            array('wp-blocks', 'wp-editor'),
            true
        );
    }

    public function load_videos_block_data(): void
    {
        $block_data = [
            'name' => self::NAME_VIDEOS_BLOCK,
            'title' => $this->translator->translate('edit_post.block.video.title'),
            'hint' => $this->translator->translate('edit_post.block.video.hint'),
            'select_hint' => $this->translator->translate('edit_post.block.video.select_hint'),
            'cat' => self::MEDIA_BLOCKS_CAT
        ];

        $select_options = $this->get_select_options();

        wp_localize_script(self::SCRIPT_VIDEOS_BLOCK, self::BPMJ_WPI_VIDEOS_BLOCK, array_merge($block_data, $select_options));
    }

    private function get_select_options(): array
    {
        $videos = $this->video_repository->find_all();

        $options = [];

        foreach ($videos as $video) {
            $options[] = [$video->get_video_id()->get_id() => $video->get_title()];
        }

        $select_options[self::KEY_NAME_IN_ARRAY] = $options;

        return $select_options;
    }

    private function get_player_html(?string $videoID): ?string
    {
        if (!$videoID) {
            return null;
        }

        return $this->video_player_renderer->render_player(new Video_Id($videoID));
    }
}
