<?php
namespace bpmj\wpidea\modules\videos\web\templates_admin;

use bpmj\wpidea\events\actions\{Interface_Actions, Action_Name};

class Old_Videos_Block
{
    private const SCRIPT_VIDEOS_BLOCK = 'wpi-old-videos-block';
    private Interface_Actions $actions;

    public function __construct(
        Interface_Actions $actions
    )
    {
        $this->actions = $actions;
    }

    public function init()
    {
        $this->actions->add(Action_Name::ENQUEUE_BLOCK_EDITOR_ASSETS, [$this, 'remove_old_videos_block_by_js']);
    }

    public function remove_old_videos_block_by_js(): void
    {
        wp_enqueue_script(
            self::SCRIPT_VIDEOS_BLOCK,
            BPMJ_EDDCM_URL. '/includes/modules/videos/web/templates-admin/assets/old-videos-block.js',
            array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' )
        );
    }
}
