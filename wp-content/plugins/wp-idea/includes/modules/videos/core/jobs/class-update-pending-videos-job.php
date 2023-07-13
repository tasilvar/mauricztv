<?php

namespace bpmj\wpidea\modules\videos\core\jobs;

use bpmj\wpidea\admin\video\events\Video_Event_Name;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\infrastructure\scheduler\Interface_Schedulable;
use bpmj\wpidea\infrastructure\scheduler\Interface_Scheduler;
use bpmj\wpidea\modules\videos\core\entities\Video;
use bpmj\wpidea\modules\videos\core\providers\Interface_Video_Provider;
use bpmj\wpidea\modules\videos\core\repositories\Interface_Video_Repository;
use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;
use bpmj\wpidea\modules\videos\infrastructure\persistence\Video_Query_Criteria;
use DateInterval;
use DateTime;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;

class Update_Pending_Videos_Job implements Interface_Schedulable
{
    private const STATUS_CREATED = 0;
    private const STATUS_UPLOADED = 1;
    private const STATUS_PROCESSING = 2;
    private const STATUS_TRANSCODING = 3;
    private const STATUS_FINISHED = 4;
    private const STATUS_ERROR = 5;
    private const STATUS_UPLOAD_FAILED = 6;

    private Interface_Video_Repository $video_repository;

    private Interface_Video_Provider $video_provider;

    private Interface_Scheduler $scheduler;

    private Interface_Events $events;

    public function __construct(
        Interface_Video_Repository $video_repository,
        Interface_Video_Provider $video_provider,
        Interface_Scheduler $scheduler,
        Interface_Events $events
    ) {
        $this->video_repository = $video_repository;
        $this->video_provider = $video_provider;
        $this->scheduler = $scheduler;
        $this->events = $events;
    }

    public function get_method_to_run(): callable
    {
        return [$this, 'update_pending_records'];
    }

    public function get_first_run_time(): DateTime
    {
        return new DateTime();
    }

    public function get_interval(): DateInterval
    {
        return new DateInterval(self::INTERVAL_1MINUTE);
    }

    public function get_args(): array
    {
        return [];
    }

    public function update_pending_records()
    {
        $criteria = new Video_Query_Criteria();
        $criteria->set_file_size([0, 1]);
        $videos = $this->video_repository->find_by_criteria($criteria);

        if (count($videos) === 0) {
            $this->scheduler->unschedule($this);
        }

        foreach ($videos as $video) {
            try {
                $details = $this->video_provider->get_video_details(new Video_Id($video->get_video_id()));
            } catch (ClientException $exception) {
                if ($exception->getMessage() === '404 Not Found') {
                    $this->video_repository->delete($video->get_id());
                    continue;
                }
            } catch (ConnectException|ServerException $exception) {
                $this->events->emit(Event_Name::EXCEPTION_CAUGHT, $exception);
            }

            if ($details['status'] === self::STATUS_FINISHED) {
                $video = new Video(
                    $video->get_id(),
                    $video->get_title(),
                    $video->get_video_id(),
                    $details['storageSize'],
                    $details['length'],
                    $details['width'],
                    $details['height'],
                    null,
                    $video->get_created_at()
                );
                $this->video_repository->update($video);
            }

            if (in_array($details['status'], [self::STATUS_ERROR, self::STATUS_UPLOAD_FAILED])) {
                $this->video_repository->delete($video->get_id());
            }
        }

        $this->events->emit(Video_Event_Name::VIDEO_STATUSES_CHECK_FINISHED);
    }


}