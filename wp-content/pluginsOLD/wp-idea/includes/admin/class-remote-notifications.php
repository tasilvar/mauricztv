<?php

namespace bpmj\wpidea\admin;

class Remote_Notifications
{
    public const REMOTE_NOTICES_TRANSIENT_NAME = 'wpi_remote_notices';

    public const REMOTE_NOTICES_TRANSIENT_TIME = 60 * 60 * 24 * 3; // 3 days

    private const REMOTE_DISMISSED_NOTICES_OPTION_NAME = 'wpi_remote_dismissed_notices';

    private const REMOTE_NOTICES_TO_NOTICES_RELATION_OPTION_NAME = 'wpi_remote_notices_to_notices_relation';

    private $notices;

    public function __construct( Notices $notices )
    {
        $this->notices = $notices;

        $this->init_hooks();
    }

    public function init_hooks()
    {
        add_action( 'admin_init', [ $this, 'remove_expired_dismissible_notifications' ] );
        add_action( 'admin_init', [ $this, 'display_remote_notifications' ] );
    }

    protected function get_notification_type( $notification )
    {
        if ( ! isset( $notification['type'] ) )
            return Notices::TYPE_INFO;

        switch ( $notification['type'] ) {
            case 'info':
                return Notices::TYPE_INFO;
            case 'error':
                return Notices::TYPE_ERROR;
            case 'warning':
                return Notices::TYPE_WARNING;
            case 'success':
                return Notices::TYPE_SUCCESS;
            default:
                return Notices::TYPE_INFO;
        }
    }

    protected function get_notices()
    {
        $notifications = get_transient( self::REMOTE_NOTICES_TRANSIENT_NAME );

        if ( empty( $notifications ) ) {
            return [];
        }

        return $notifications;
    }

    protected function is_notice_dismissed( $notice_id )
    {
        $dismissed_notices = get_option( self::REMOTE_DISMISSED_NOTICES_OPTION_NAME, [] );

        if ( in_array( $notice_id, $dismissed_notices ) )
            return true;

        return false;
    }

    protected function is_current_time_within_notice_time_span( $notice )
    {
        if ( is_null( $notice['valid_from'] ) && is_null( $notice['valid_to']  ) )
            return true;

        $current_timestamp = current_time( 'timestamp' );

        $notification_timestamp_from = strtotime( $notice['valid_from'] );
        $notification_timestamp_to = strtotime( $notice['valid_to'] );

        if (
            $current_timestamp >= $notification_timestamp_from &&
            (
                ( is_null( $notice['valid_to'] ) ) ||
                ( $current_timestamp <= $notification_timestamp_to )
            )
        )
            return true;

        return false;
    }

    protected function is_notice_intended_to_display_in_future( $notice )
    {
        $current_timestamp = current_time( 'timestamp' );
        $notification_timestamp_from = strtotime( $notice['valid_from'] );
        if ( $notification_timestamp_from > $current_timestamp )
            return true;

        return false;
    }

    protected function render_notice( $notification )
    {
        $notice_type = $this->get_notification_type( $notification );

        if ( isset( $notification['dismissible'] ) && $notification['dismissible'] == true ) {
            $notice = $this->notices->add_dismissible_notice(
                $notification['message'],
                $notice_type
            );

            $this->add_relation_remote_notice_to_notice( $notification['id'], $notice->id );

            self::update_dismissed_notices( $notification['id'] );
        } else {
            $this->notices->display_notice(
                $notification['message'],
                $notice_type
            );
        }
    }

    public function remove_expired_dismissible_notifications(): void
    {
        $remote_notifications_ids_to_remove = $this->find_remote_notifications_ids_to_remove();
        if ( empty( $remote_notifications_ids_to_remove ) ) {
            return;
        }

        $relational_notices = $this->get_relations_remote_notice_to_notice();

        $notices_ids_to_remove = [];
        foreach ( $relational_notices as $notice ) {
            if ( in_array( $notice['remote_notice_id'], $remote_notifications_ids_to_remove ) ) {
                $notices_ids_to_remove[] = $notice['notice_id'];
            }
        }

        $this->remove_expired_notices( $notices_ids_to_remove );
        $this->remove_relations_remote_notice_to_notice( $notices_ids_to_remove );
    }

    protected function find_remote_notifications_ids_to_remove(): array
    {
        $remote_notifications_ids_to_remove = [];
        $notifications = $this->get_notices();
        foreach ( $notifications as $notification ) {
            if ( isset( $notification['dismissible'] ) && $notification['dismissible'] == true ) {
                if ( ! $this->is_current_time_within_notice_time_span( $notification ) ) {
                    $remote_notifications_ids_to_remove[] = $notification['id'];
                }
            }
        }

        return $remote_notifications_ids_to_remove;
    }

    protected function remove_expired_notices( array $ids )
    {
        foreach ( $ids as $id ) {
            $this->notices->remove_notice_by_id( $id );
        }
    }

    protected function remove_relations_remote_notice_to_notice( array $ids )
    {
        $relational_notices = $this->get_relations_remote_notice_to_notice();

        foreach ($relational_notices as $notice) {
            if ( in_array( $notice['notice_id'], $ids ) ) {
                $this->remove_relation_remote_notice_by_notice_id( $notice['notice_id'] );
            }
        }
    }

    public function display_remote_notifications()
    {
        $notifications = $this->get_notices();

        if ( empty( $notifications ) )
            return;

        foreach ( $notifications as $notification ) {
            if ( $this->is_notice_dismissed( $notification['id'] ) ) {
                continue;
            }

            if ( $this->is_current_time_within_notice_time_span( $notification ) ) {
                $this->render_notice( $notification );
            } else {
                if ( ! $this->is_notice_intended_to_display_in_future( $notification ) )
                    self::update_dismissed_notices( $notification['id'] );
            }
        }
    }

    public static function update_dismissed_notices( $id )
    {
        $dismissed_notices = get_option( self::REMOTE_DISMISSED_NOTICES_OPTION_NAME, [] );
        $dismissed_notices[] = $id;
        update_option( self::REMOTE_DISMISSED_NOTICES_OPTION_NAME, $dismissed_notices );
    }

    private function get_relations_remote_notice_to_notice(): array
    {
        return get_option( self::REMOTE_NOTICES_TO_NOTICES_RELATION_OPTION_NAME, [] );
    }

    private function add_relation_remote_notice_to_notice( string $remote_notice_id, string $notice_id )
    {
        $relations_notices = $this->get_relations_remote_notice_to_notice();

        $relations_notices[] = [
            'remote_notice_id' => $remote_notice_id,
            'notice_id'        => $notice_id,
        ];

        $this->update_relations_remote_notice_to_notice( $relations_notices );
    }

    private function remove_relation_remote_notice_by_notice_id( string $id )
    {
        $to_remove = null;

        $relations = $this->get_relations_remote_notice_to_notice();
        foreach ( $relations as $key => $notice ) {
            if ( $notice['notice_id'] == $id ) {
                $to_remove = $key;
            }
        }

        if ( ! is_null( $to_remove ) ) {
            unset( $relations[ $to_remove ] );

            $this->update_relations_remote_notice_to_notice( $relations );
        }
    }

    private function update_relations_remote_notice_to_notice( array $relations_notices )
    {
        update_option( self::REMOTE_NOTICES_TO_NOTICES_RELATION_OPTION_NAME, $relations_notices );
    }
}
