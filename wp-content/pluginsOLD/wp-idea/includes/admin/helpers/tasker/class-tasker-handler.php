<?php

namespace bpmj\wpidea\admin\helpers\tasker;

abstract class Tasker_Handler {

    private const AJAX_ACTION_PREFIX = 'wp';

    protected $action = 'async_request';

    protected $identifier;

    protected $data = array();

    public function __construct() {
        $this->identifier = self::AJAX_ACTION_PREFIX . '_' . $this->action;

        add_action( 'wp_ajax_' . $this->identifier, [$this, 'maybe_handle']);
        add_action( 'wp_ajax_nopriv_' . $this->identifier, [$this, 'maybe_handle']);
    }

    public function set_data(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array|WP_Error
     */
    public function dispatch() {
        $url  = add_query_arg( $this->get_query_args(), $this->get_query_url() );
        $args = $this->get_post_args();

        return wp_remote_post( esc_url_raw( $url ), $args );
    }

    protected function get_query_args(): array
    {
        if ( property_exists( $this, 'query_args' ) ) {
            return $this->query_args;
        }

        return [
            'action' => $this->identifier,
            'nonce'  => wp_create_nonce( $this->identifier ),
        ];
    }

    protected function get_query_url(): string
    {
        if ( property_exists( $this, 'query_url' ) ) {
            return $this->query_url;
        }

        return admin_url( 'admin-ajax.php' );
    }

    protected function get_post_args(): array
    {
        if ( property_exists( $this, 'post_args' ) ) {
            return $this->post_args;
        }

        return [
            'timeout'   => 0.01,
            'blocking'  => false,
            'body'      => $this->data,
            'cookies'   => $_COOKIE,
            'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
        ];
    }

    public function maybe_handle() {
        // Don't lock up other requests while processing
        session_write_close();

        check_ajax_referer( $this->identifier, 'nonce' );

        $this->handle();

        wp_die();
    }

    abstract protected function handle();
}
