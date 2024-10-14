<?php
namespace bpmj\wpidea\modules\videos\core\services;

use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;

interface Interface_Video_Player_Renderer
{
    public function render_player(Video_Id $video_id): string;
}