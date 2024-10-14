<?php

namespace bpmj\wpidea\admin;

class Notices {
    const NOTICES_OPTION_NAME = 'wpi_admin_notices';

    const TYPE_INFO = 'info';
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_SUCCESS = 'success';

    private $nondismissible_notices_to_display = [];

    public function __construct() {
        add_action( 'admin_notices', array( $this, 'display_notices' ) );

        add_action('wp_ajax_' . 'wpi_notices_dismiss', array( $this, 'dismiss_notice') );
    }

    /**
     * Add dismissible admin notice.
     * This method should be called once for every notice that should be displayed - NOT on each request.
     *
     * Notice will be stored in the database and will be visible until user dismisses it.
     */
    public function add_dismissible_notice( string $message, string $type = self::TYPE_INFO ): Notice_Options_Item
    {
        return $this->_add_notice_to_options( $message, $type );
    }

    public function remove_notice_by_id( $id ): bool
    {
        $is_removable_notice_found = false;

        $notices = $this->_get_notices_from_options();
        foreach ($notices as $key => $notice){
            if($notice->id == $id) {
                $is_removable_notice_found = true;
                unset($notices[$key]);
            }
        }

        if (! $is_removable_notice_found) {
            return false;
        }

        return $this->_update_notices_options( $notices );
    }

    public function display_notice( $message, $type = self::TYPE_INFO )
    {
        if( ! is_string( $message ) ) return false;

        return $this->_add_notice_to_display( $message, $type );
    }

    public function display_custom_html_notice(string $custom_html_content)
    {
        return $this->_add_notice_to_display(null, null, $custom_html_content);
    }

    private function _add_notice_to_display($message, $type, $custom_html_content = null)
    {
        $notice = new Notice_Options_Item($message, $type, $custom_html_content);

        $notices = $this->_get_nondismissible_notices_to_display();
        $notices[] = $notice;

        return $this->_update_nondismissible_notices_to_display( $notices );
    }

    private function _get_nondismissible_notices_to_display()
    {
        return $this->nondismissible_notices_to_display;
    }

    private function _update_nondismissible_notices_to_display( $new_value )
    {
        return $this->nondismissible_notices_to_display = $new_value;
    }

    private function _add_notice_to_options( $message, $type ): Notice_Options_Item
    {
        $notice = new Notice_Options_Item( $message, $type );

        $notices = $this->_get_notices_from_options();
        $notices[] = $notice;

        $this->_update_notices_options( $notices );

        return $notice;
    }

    private function _get_notices_from_options(): array
    {
        $notices = get_option( self::NOTICES_OPTION_NAME, [] );

        return is_array($notices) ? $notices : [];
    }

    private function _update_notices_options( $new_value )
    {
        return update_option( self::NOTICES_OPTION_NAME, $new_value, true );
    }

    public function display_notices()
    {
        $dismissible_notices = $this->_get_notices_from_options();
        $nondismissible_notices = $this->_get_nondismissible_notices_to_display();

        foreach ($dismissible_notices as $key => $notice) {
            $this->_display_admin_notice( $notice, true );
        }

        foreach ($nondismissible_notices as $key => $notice) {
            $this->_display_admin_notice( $notice, false );
        }
    }

    public function dismiss_notice()
    {

        $notices = $this->_get_notices_from_options();

        $notices = array_filter($notices, array( $this, 'filter_notice_based_on_query_param' ) );

        $this->_update_notices_options( $notices );

        wp_send_json_success($notices);
    }

    protected function filter_notice_based_on_query_param( $notice )
    {
        $id = !empty( $_REQUEST[ 'id' ] ) ? $_REQUEST[ 'id' ] : null;

        if( $notice->id == $id ) return false;

        return true;
    }

    private function _display_admin_notice( Notice_Options_Item $notice, $dismissible = false )
    {
        if(!empty($notice->custom_html_content)){
            echo $notice->custom_html_content;
            return;
        }

        $is_dismissible = $dismissible ? 'is-dismissible' : '';

        echo "<div class='notice notice-{$notice->type} $is_dismissible bpmj-wpi-notice' data-id='{$notice->id}'><p>{$notice->message}</p></div>";
    }
}
