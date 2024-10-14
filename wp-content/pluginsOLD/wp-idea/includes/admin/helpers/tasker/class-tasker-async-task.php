<?php

namespace bpmj\wpidea\admin\helpers\tasker;

use stdClass;

abstract class Tasker_Async_Task extends Tasker_Handler {

    protected const DEFAULT_LOCK_DURATION_IN_SECONDS = 60;
    protected const MAX_MEMORY_USAGE_PERCENTAGE = 90;
    protected const DEFAULT_TIME_LIMIT = 20;
    protected const DEFAULT_CRON_INTERVAL_IN_MINUTES = 5;

    protected $action = 'background_process';

    protected $start_time = 0;

    protected $cron_hook_identifier;

    protected $cron_interval_identifier;

    public function __construct() {
        parent::__construct();

        $this->cron_hook_identifier     = $this->identifier . '_cron';
        $this->cron_interval_identifier = $this->identifier . '_cron_interval';

        add_action( $this->cron_hook_identifier, [$this, 'check_queue_status']);
        add_filter( 'cron_schedules', [$this, 'schedule_cron_queue_status_check']);
    }

    public function dispatch() {
        $this->run_cron_queue_status_check();

        return parent::dispatch();
    }

    public function push_to_queue($data): self
    {
        $this->data[] = $data;

        return $this;
    }

    public function save(): self
    {
        $key = $this->generate_unique_task_key();

        if ( ! empty( $this->data ) ) {
            update_site_option( $key, $this->data );
        }

        return $this;
    }

    public function update(string $key, array $data): self
    {
        if ( ! empty( $data ) ) {
            update_site_option( $key, $data );
        }

        return $this;
    }

    public function delete(string $key): self
    {
        delete_site_option( $key );

        return $this;
    }

    protected function generate_unique_task_key(int $length = 64): string
    {
        $unique  = md5( microtime() . rand() );
        $prepend = $this->identifier . '_batch_';

        return substr( $prepend . $unique, 0, $length );
    }

    public function maybe_handle(): void
    {
        $this->end_current_session_to_unlock_other_request();

        if ( $this->is_process_running() ) {
            wp_die();
        }

        if ( $this->is_queue_empty() ) {
            wp_die();
        }

        $this->verify_ajax_referer_or_die();

        $this->handle();

        wp_die();
    }

    protected function is_queue_empty(): bool
    {
        return $this->get_queue_items_count() <= 0;
    }

    protected function get_queue_items_count(): int
    {
        global $wpdb;

        $table  = $wpdb->options;
        $column = 'option_name';

        $key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

        $count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*)
			FROM {$table}
			WHERE {$column} LIKE %s
		", $key ) );

        return $count;
    }

    protected function is_process_running(): bool
    {
        return !empty(get_site_transient( $this->identifier . '_process_lock' ));
    }

    /**
     * Lock the process so that multiple instances can't run simultaneously.
     * Override if applicable, but the duration should be greater than that
     * defined in the time_exceeded() method.
     */
    protected function lock_process(): void
    {
        $this->start_time = time();

        $lock_duration = ( property_exists( $this, 'queue_lock_time' ) ) ? $this->queue_lock_time : self::DEFAULT_LOCK_DURATION_IN_SECONDS;

        set_site_transient( $this->identifier . '_process_lock', microtime(), $lock_duration );
    }

    protected function unlock_process(): self
    {
        delete_site_transient( $this->identifier . '_process_lock' );

        return $this;
    }

    protected function get_next_batch_from_queue(): stdClass
    {
        global $wpdb;

        $table        = $wpdb->options;
        $column       = 'option_name';
        $key_column   = 'option_id';
        $value_column = 'option_value';

        $key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

        $query = $wpdb->get_row( $wpdb->prepare( "
			SELECT *
			FROM {$table}
			WHERE {$column} LIKE %s
			ORDER BY {$key_column} ASC
			LIMIT 1
		", $key ) );

        $batch       = new stdClass();
        $batch->key  = $query->$column;
        $batch->data = maybe_unserialize( $query->$value_column );

        return $batch;
    }

    /**
     * Pass each queue item to the task handler, while remaining
     * within server memory and time limit constraints.
     */
    protected function handle(): void
    {
        $this->lock_process();

        do {
            $this->process_next_batch();
        } while ( ! $this->time_exceeded() && ! $this->memory_exceeded() && ! $this->is_queue_empty() );

        $this->unlock_process();

        $this->start_next_batch_or_complete_process();

        wp_die();
    }

    /**
     * Ensures the batch process never exceeds 90%
     * of the maximum WordPress memory.
     */
    protected function memory_exceeded(): bool
    {
        $memory_limit   = $this->get_memory_limit() * (self::MAX_MEMORY_USAGE_PERCENTAGE / 100);
        $current_memory = memory_get_usage( true );

        return $current_memory >= $memory_limit;
    }

    /**
     * Get memory limit
     */
    protected function get_memory_limit(): int
    {
        if ( function_exists( 'ini_get' ) ) {
            $memory_limit = ini_get( 'memory_limit' );
        } else {
            // Sensible default.
            $memory_limit = '128M';
        }

        if ( ! $memory_limit || - 1 === (int)$memory_limit) {
            // Unlimited, set to 32GB.
            $memory_limit = '32000M';
        }

        return wp_convert_hr_to_bytes( $memory_limit );
    }

    /**
     * Ensures the batch never exceeds a sensible time limit.
     * A timeout limit of 30s is common on shared hosting.
     */
    protected function time_exceeded(): bool
    {
        $finish = $this->start_time + self::DEFAULT_TIME_LIMIT;

        return time() >= $finish;
    }

    /**
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete(): void
    {
        $this->clear_scheduled_event();
    }

    public function schedule_cron_queue_status_check( $schedules ) {
        $interval = self::DEFAULT_CRON_INTERVAL_IN_MINUTES;

        if ( property_exists( $this, 'cron_interval' ) ) {
            $interval = $this->cron_interval;
        }

        $schedules[ $this->identifier . '_cron_interval' ] = [
            'interval' => MINUTE_IN_SECONDS * $interval,
            'display'  => sprintf( __('Every %d Minutes', BPMJ_EDDCM_DOMAIN), $interval ),
        ];

        return $schedules;
    }

    public function check_queue_status(): void
    {
        if ( $this->is_process_running() ) {
            exit;
        }

        if ( $this->is_queue_empty() ) {
            $this->clear_scheduled_event();
            exit;
        }

        $this->handle();

        exit;
    }

    protected function run_cron_queue_status_check(): void
    {
        if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
            wp_schedule_event( time(), $this->cron_interval_identifier, $this->cron_hook_identifier );
        }
    }

    protected function clear_scheduled_event(): void
    {
        $timestamp = wp_next_scheduled( $this->cron_hook_identifier );

        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, $this->cron_hook_identifier );
        }
    }

    public function cancel_process(): void
    {
        if ( ! $this->is_queue_empty() ) {
            $batch = $this->get_next_batch_from_queue();

            $this->delete( $batch->key );

            wp_clear_scheduled_hook( $this->cron_hook_identifier );
        }

    }

    private function verify_ajax_referer_or_die(): void
    {
        check_ajax_referer( $this->identifier, 'nonce' );
    }

    private function end_current_session_to_unlock_other_request(): void
    {
        session_write_close();
    }

    private function process_next_batch(): void
    {
        $batch = $this->get_next_batch_from_queue();

        foreach ( $batch->data as $key => $value ) {
            $task = $this->task( $value );

            if ( false !== $task ) {
                $batch->data[ $key ] = $task;
            } else {
                unset( $batch->data[ $key ] );
            }

            if ( $this->time_exceeded() || $this->memory_exceeded() ) {
                break;
            }
        }

        if ( ! empty( $batch->data ) ) {
            $this->update( $batch->key, $batch->data );
        } else {
            $this->delete( $batch->key );
        }
    }

    private function start_next_batch_or_complete_process(): void
    {
        if ( ! $this->is_queue_empty() ) {
            $this->dispatch();
        } else {
            $this->complete();
        }
    }

    /**
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over.
     *
     * @return mixed
     */
    abstract protected function task( $item );
}