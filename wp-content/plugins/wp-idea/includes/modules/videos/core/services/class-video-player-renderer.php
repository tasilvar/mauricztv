<?php

namespace bpmj\wpidea\modules\videos\core\services;

use bpmj\wpidea\Info_Message;
use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;
use bpmj\wpidea\modules\videos\core\providers\Interface_Video_Config_Provider;
use bpmj\wpidea\modules\videos\core\repositories\Interface_Video_Repository;
use bpmj\wpidea\translator\Interface_Translator;

class Video_Player_Renderer implements Interface_Video_Player_Renderer
{

    private const PERCENT = '%';
    private const RATIO_16_9 = '56.25';
    private const VIDEO_MESSAGE_ICON = 'format-video';
    private Interface_Video_Config_Provider $video_config_provider;
    private Interface_Video_Repository $video_repository;
    private Interface_Translator $translator;

    public function __construct(
        Interface_Video_Config_Provider $video_config_provider,
        Interface_Video_Repository $video_repository,
        Interface_Translator $translator
    )
    {
        $this->video_config_provider = $video_config_provider;
        $this->video_repository = $video_repository;
        $this->translator = $translator;
    }

    public function render_player(Video_Id $video_id): string
    {
        $video = $this->video_repository->find_by_video_id($video_id);
        if ($video && $video->is_processing()) {
            $info_message = new Info_Message($this->translator->translate('video.course_page.video_is_processing'), null, self::VIDEO_MESSAGE_ICON);
            return $info_message->get();
        }

        $video_aspect_ratio = $this->get_video_aspect_ratio_in_percent($video_id);

        return'
            <div style="position: relative; padding-top: '.$video_aspect_ratio.'">
                <iframe src="https://iframe.mediadelivery.net/embed/'.$this->get_library_id().'/'.$video_id->get_id().'?autoplay=false&preload=false"
                       loading="lazy" 
                       style="border: none; position: absolute; top: 0; height: 100%; width: 100%;" 
                       allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" 
                       allowfullscreen="true">    
                </iframe>
            </div>
           ';
    }

    private function get_library_id(): string
    {
        return $this->video_config_provider->get_configuration()['library_id'];
    }

    private function get_video_dimensions(Video_Id $video_id): ?array
    {
        $video = $this->video_repository->find_by_video_id($video_id);

        if(!$video){
            return null;
        }

        return [
           'width' => $video->get_width(),
           'height' => $video->get_height()
        ];
    }

   private function get_video_aspect_ratio_in_percent(Video_Id $video_id): string
   {
      $dimensions = $this->get_video_dimensions($video_id);

      if(!$dimensions){
         return self::RATIO_16_9.self::PERCENT;
      }

      $video_aspect_ratio = $this->calculate_video_aspect_ratio_in_percent($dimensions);

       if(!$video_aspect_ratio){
           return self::RATIO_16_9.self::PERCENT;
       }

      return $video_aspect_ratio.self::PERCENT;
   }

   private function calculate_video_aspect_ratio_in_percent(array $dimensions): ?float
   {
       $width = $dimensions['width'];
       $height = $dimensions['height'];

       if(!$width || !$height){
          return null;
       }

       return ($height / $width) * 100;
   }
}