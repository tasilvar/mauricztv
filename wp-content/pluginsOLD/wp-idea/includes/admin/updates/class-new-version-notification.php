<?php

namespace bpmj\wpidea\admin\updates;

use bpmj\wpidea\admin\Notices;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\Wpi_Version;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\View;
use Psr\SimpleCache\CacheInterface;

class New_Version_Notification implements Interface_Initiable
{
    private $actions;

    private $cache;

    private $wpi_version;

    private $redirector;

    private $current_request;

    private $notices;

    private $subscription;

    private const DISMISSED_NEW_VERSION_NOTICE_TRANSIENT = 'wpi_dismissed_new_version_notice';

    private const REDISPLAY_NEW_VERSION_NOTICE_TIME = 86400;

    public function __construct(
        Interface_Actions $actions,
        CacheInterface $cache,
        Wpi_Version $version,
        Interface_Redirector $redirector,
        Current_Request $current_request,
        Notices $notices,
        Subscription $subscription
    ) {
        $this->actions = $actions;
        $this->cache = $cache;
        $this->wpi_version = $version;
        $this->redirector = $redirector;
        $this->current_request = $current_request;
        $this->notices = $notices;
        $this->subscription = $subscription;
    }

    public function init(): void
    {
        if ( $this->subscription->is_go() ) {
            return;
        }

        $this->actions->add('admin_init', [$this, 'admin_new_version_notice']);
        $this->actions->add('admin_init', [$this, 'dismiss_notice']);
    }

    public function admin_new_version_notice(): void
    {
        if (isset( $_GET['action'] ) && 'upgrade-plugin' === $_GET['action']) {
            return;
        }
        
        if ($this->is_notice_dismissed()) {
            return;
        }

        if (! $this->wpi_version->is_new_version_available()) {
            return;
        }

        $wp_idea_transient_new_version = $this->wpi_version->get_new_version();
        $this->notices->display_custom_html_notice($this->get_notice_content($wp_idea_transient_new_version));
    }

    private function is_notice_dismissed(): bool
    {
        $dismissed_transient = $this->cache->get(self::DISMISSED_NEW_VERSION_NOTICE_TRANSIENT, false);

        if ($dismissed_transient === false) {
            return false;
        }

        return true;
    }

    public function dismiss_notice()
    {
        if ( isset( $_GET['dismiss-new-version-notice'] ) ) {
            $this->cache->set(self::DISMISSED_NEW_VERSION_NOTICE_TRANSIENT, 1, self::REDISPLAY_NEW_VERSION_NOTICE_TIME);
            $this->redirector->redirect( $this->current_request->get_referer() );
        }
    }

    private function get_notice_content(string $new_version): string
    {
        return View::get( '/notices/new-wpi-version', [
            'new_version' => $new_version,
        ] );
    }
}