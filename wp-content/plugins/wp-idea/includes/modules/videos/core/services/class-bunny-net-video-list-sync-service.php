<?php

namespace bpmj\wpidea\modules\videos\core\services;

use bpmj\wpidea\infrastructure\scheduler\Interface_Scheduler;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\modules\videos\core\entities\Video;
use bpmj\wpidea\modules\videos\core\jobs\Update_Pending_Videos_Job;
use bpmj\wpidea\modules\videos\core\providers\Interface_Video_Provider;
use bpmj\wpidea\modules\videos\core\repositories\Interface_Video_Repository;
use bpmj\wpidea\modules\videos\infrastructure\persistence\Video_Query_Criteria;
use Psr\Container\ContainerInterface;

class Bunny_Net_Video_List_Sync_Service implements Interface_Initiable
{

    private Interface_Video_Provider $bunny_net_video_provider;

    private Interface_Video_Repository $video_repository;

    private Interface_Scheduler $scheduler;

    private ContainerInterface $container;

    public function __construct(
        Interface_Video_Provider $bunny_net_video_provider,
        Interface_Video_Repository $video_repository,
        Interface_Scheduler $scheduler,
        ContainerInterface $container
    ) {
        $this->bunny_net_video_provider = $bunny_net_video_provider;
        $this->video_repository = $video_repository;
        $this->scheduler = $scheduler;
        $this->container = $container;
    }

    public function init(): void
    {
        $this->schedule_update_pending_videos_job_if_needed();
    }

    public function sync(bool $full_sync = true)
    {
        $page = 1;
        $processed_items = 0;
        $total_items = $this->bunny_net_video_provider->count_videos_in_collection();

        if ($total_items > 100 && !$full_sync) {
            $total_items = 100;
        }

        while ($processed_items < $total_items) {
            $response = $this->bunny_net_video_provider->get_video_collections($page);

            foreach ($response as $video) {
                $this->create_or_update($video);
                $processed_items++;
            }

            $page++;
        }
    }

    public function schedule_update_pending_videos_job_if_needed()
    {
        $criteria = new Video_Query_Criteria();
        $criteria->set_file_size([0, 1]);
        $videos = $this->video_repository->find_by_criteria($criteria);

        if (count($videos)) {
            $job = $this->container->get(Update_Pending_Videos_Job::class);
            $this->scheduler->schedule($job);
        }
    }

    private function create_or_update(Video $video): void
    {
        if ($video_model = $this->video_repository->find_by_video_id($video->get_video_id())) {
            $video_model = new Video(
                $video_model->get_id(),
                $video->get_title(),
                $video->get_video_id(),
                $video->get_file_size(),
                $video->get_length(),
                $video->get_width(),
                $video->get_height(),
                $video->get_thumbnail_url(),
                $video->get_created_at()
            );
            $this->video_repository->update($video_model);

        } else {
            $video_model = new Video(
                null,
                $video->get_title(),
                $video->get_video_id(),
                $video->get_file_size(),
                $video->get_length(),
                $video->get_width(),
                $video->get_height(),
                null,
                $video->get_created_at()
            );
            $this->video_repository->create($video_model);
        }
    }
}