<?php

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\io;

use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Disk_Space_Checker implements Interface_Disk_Space_Checker, Interface_Initiable
{
    private const MAX_DISC_SPACE_FOR_START_PLAN_IN_GB = 2;
    private const MAX_DISC_SPACE_FOR_PLUS_PLAN_IN_GB = 4;
    private const MAX_DISC_SPACE_FOR_PRO_PLAN_IN_GB = 6;

    private const EXCLUDED_DIR = 'deleted';
    private const DIRSIZE_CACHE = 'dirsize_cache';
    private const BASEDIR = 'basedir';

    private int $max_disc_space_in_gb;

    private Interface_Actions $actions;
    private Interface_Filters $filters;
    private Subscription $subscription;

    public function __construct(
        Interface_Actions $actions,
        Interface_Filters $filters,
        Subscription $subscription
    ) {
        $this->actions = $actions;
        $this->filters = $filters;
        $this->subscription = $subscription;

        $this->max_disc_space_in_gb = $this->get_max_disc_space_for_current_plan_in_gb();
    }

    public function init(): void
    {
        $this->filters->add(Filter_Name::FILES_UPLOADED, [$this, 'files_uploaded']);
        $this->actions->add(Action_Name::FILES_DELETED, [$this, 'files_deleted']);
    }

    public function files_uploaded(array $file): array
    {
        $this->clear_cache();

        return $file;
    }

    public function files_deleted(int $post_id): void
    {
        $this->clear_cache();
    }

    public function get_used_percentage(): string
    {
        return $this->calculate_the_percentage_of_space_used();
    }

    public function get_used(): int
    {
        return $this->get_size_in_bytes_upload_dir();
    }

    public function get_max(): int
    {
        return $this->max_disc_space_in_gb;
    }

    private function clear_cache(): void
    {
        delete_transient(self::DIRSIZE_CACHE);
    }

    private function get_upload_basedir(): string
    {
        $upload_dir = wp_get_upload_dir();

        return $upload_dir[self::BASEDIR];
    }

    private function get_size_in_bytes_upload_dir(): int
    {
        $exclude = $this->get_upload_basedir() . "/" . self::EXCLUDED_DIR;

        $used_disc_space = recurse_dirsize($this->get_upload_basedir(), $exclude);

        return $used_disc_space ?? 0;
    }

    private function calculate_the_percentage_of_space_used(): string
    {
        $used_disc_space_from_cache = $this->get_size_in_bytes_upload_dir();

        $used_disc_space_in_gb = ($used_disc_space_from_cache / (1024 * 1024 * 1024));

        return number_format((($used_disc_space_in_gb / $this->max_disc_space_in_gb) * 100), 2);
    }

    private function get_max_disc_space_for_current_plan_in_gb(): int
    {
        switch ($this->subscription->get_plan()) {
            case Subscription_Const::PLAN_PRO:
                return self::MAX_DISC_SPACE_FOR_PRO_PLAN_IN_GB;
            case Subscription_Const::PLAN_PLUS:
                return self::MAX_DISC_SPACE_FOR_PLUS_PLAN_IN_GB;
            default:
                return self::MAX_DISC_SPACE_FOR_START_PLAN_IN_GB;
        }
    }
}