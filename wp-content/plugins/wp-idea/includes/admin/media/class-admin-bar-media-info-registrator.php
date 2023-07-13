<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\media;

use bpmj\wpidea\admin\bar\Admin_Bar;
use bpmj\wpidea\admin\bar\Admin_Bar_Item_Position;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\helpers\Bytes_Formatter;
use bpmj\wpidea\infrastructure\io\Interface_Disk_Space_Checker;
use bpmj\wpidea\infrastructure\io\video\Interface_Video_Space_Checker;
use bpmj\wpidea\infrastructure\io\video\Video_Space_Usage_Info;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Bar_Media_Info_Registrator implements Interface_Initiable
{
    private const ITEM_ID_AVAILABLE_DISK_SPACE_INFO = 'available-space-info';
    public const ITEM_ID_AVAILABLE_VIDEO_SPACE_INFO = 'available-video-space-info';

    private Subscription $subscription;
    private Admin_Bar $admin_bar;
    private Interface_Disk_Space_Checker $disk_space_checker;
    private Interface_Translator $translator;
    private Bytes_Formatter $bytes_formatter;
    private Interface_Video_Space_Checker $video_space_checker;

    public function __construct(
        Subscription $subscription,
        Admin_Bar $admin_bar,
        Interface_Disk_Space_Checker $disk_space_checker,
        Interface_Translator $translator,
        Bytes_Formatter $bytes_formatter,
        Interface_Video_Space_Checker $video_space_checker
    )
    {
        $this->subscription = $subscription;
        $this->admin_bar = $admin_bar;
        $this->disk_space_checker = $disk_space_checker;
        $this->translator = $translator;
        $this->bytes_formatter = $bytes_formatter;
        $this->video_space_checker = $video_space_checker;
    }

    public function init(): void
    {
        $this->register_items();
    }

    private function register_items(): void
    {
        if (!$this->subscription->is_go()) {
            return;
        }

        $this->register_available_files_disk_space_info();
        $this->register_available_video_disk_space_info();
    }

    private function register_available_video_disk_space_info(): void
    {
        $video_space_usage_info = $this->video_space_checker->get_usage_info();

        $this->admin_bar->register_item(
            self::ITEM_ID_AVAILABLE_VIDEO_SPACE_INFO,
            $this->get_available_video_space_info_title($video_space_usage_info),
            Admin_Bar_Item_Position::from_string(Admin_Bar_Item_Position::BEFORE_USER_INFO),
            null,
            ['title' => $this->get_available_video_space_info_explanation($video_space_usage_info)],
        );
    }

    private function register_available_files_disk_space_info(): void
    {
        $this->admin_bar->register_item(
            self::ITEM_ID_AVAILABLE_DISK_SPACE_INFO,
            $this->disk_space_checker->get_used_percentage().'%',
            Admin_Bar_Item_Position::from_string(Admin_Bar_Item_Position::BEFORE_USER_INFO),
            null,
            ['title' => $this->get_available_disc_space_info_explanation()],
        );
    }

    private function get_available_disc_space_info_explanation(): string
    {
        $get_used = $this->disk_space_checker->get_used();

        $used_space = $this->bytes_formatter->to_formatted_string($get_used);

        $max_disk_space = $this->disk_space_checker->get_max();

        return sprintf( $this->translator->translate('admin_bar.free_space'), $used_space, $max_disk_space);
    }

    private function get_available_video_space_info_explanation(Video_Space_Usage_Info $space_usage_info): string
    {
        $i18n_string = !is_null($space_usage_info->get_traffic_usage_percentage())
            ? 'admin_bar.free_video_storage_space_and_traffic'
            : 'admin_bar.free_video_storage_space';

        return sprintf(
            $this->translator->translate($i18n_string),
            $this->bytes_formatter->to_formatted_string($space_usage_info->get_current_storage_usage_in_bytes()),
            $this->bytes_formatter->to_formatted_string($space_usage_info->get_max_storage_usage_in_bytes()),
            $this->bytes_formatter->to_formatted_string($space_usage_info->get_current_traffic_usage_in_bytes() ?? 0),
            $this->bytes_formatter->to_formatted_string($space_usage_info->get_max_traffic_usage_in_bytes() ?? 0),
        );
    }

    private function get_available_video_space_info_title(Video_Space_Usage_Info $space_usage_info): string
    {
        $storage_usage_formatted = $space_usage_info->get_storage_usage_percentage() . '%';
        $traffic_usage_formatted = ($space_usage_info->get_traffic_usage_percentage() ?? 0) . '%';

        return !is_null($space_usage_info->get_traffic_usage_percentage())
            ? $storage_usage_formatted . ' | ' . $traffic_usage_formatted
            : $storage_usage_formatted;
    }
}