<?php namespace bpmj\wpidea\admin\notices;

use bpmj\wpidea\admin\support\diagnostics\items\WP_Version;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Wp_Version_Notice implements Interface_Initiable
{
    public function init(): void
    {
        add_filter('after_core_auto_updates_settings', [$this, 'wordpress_version_notice']);
    }

    public function wordpress_version_notice(): void
    {
        if ($this->should_show_notice()) {
            echo "<div class='notice notice-warning inline'><p>{$this->notice_content()}</p></div>";
        }
    }

    public function should_show_notice(): bool
    {
        return $this->is_wordpress_update_available() && !$this->is_latest_wp_version_campatibile_with_wp_idea();
    }

    public function notice_content(): string
    {
        $recommended_version = WP_Version::MAX_RECOMMENDED_VERSION;

        return sprintf(__("Warning: Currently installed version of WP-Idea was tested with WordPress version %s.
        We recommend to update WP-Idea to latest version before updating WordPress.
        "), $recommended_version);
    }

    private function is_wordpress_update_available(): bool
    {
        $current_version = get_bloginfo('version');
        $latest_version = $this->get_latest_available_wp_version_number();

        return version_compare($current_version, $latest_version, '<');
    }

    private function is_latest_wp_version_campatibile_with_wp_idea(): bool
    {
        $recommended_version = $this->get_rounded_version_number(
            (string)WP_Version::MAX_RECOMMENDED_VERSION
        );
        $latest_version = $this->get_rounded_version_number(
            $this->get_latest_available_wp_version_number()
        );

        return version_compare($recommended_version, $latest_version, '>=');
    }

    private function get_latest_available_wp_version_number(): string
    {
        try {
            return get_site_transient('update_core')->updates[0]->version;
        } catch (\Exception $e) {
            return WP_Version::MAX_RECOMMENDED_VERSION;
        }
    }

    private function get_rounded_version_number(string $version): string
    {
        $parts = explode('.', $version);
        return $parts[0] . '.' . $parts[1];
    }
}
