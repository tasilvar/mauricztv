<?php

namespace bpmj\wpidea\modules\google_analytics\web;

use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\modules\google_analytics\Google_Analytics_Module;
use bpmj\wpidea\modules\google_analytics\api\Google_Analytics_API;

class Google_Analytics_Scripts_Renderer
{
    private Google_Analytics_API $google_analytics_api;
    private Interface_Actions $actions;

    public function __construct(
        Google_Analytics_API $google_analytics_api,
        Interface_Actions $actions
    ) {
        $this->google_analytics_api = $google_analytics_api;
        $this->actions = $actions;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::HEAD, [$this, 'before_head_close_tag_scripts'], 1000);
        $this->actions->add(Action_Name::FOOTER, [$this, 'send_events_to_analytics'], 100);
    }

    public function before_head_close_tag_scripts(): void
    {
        echo $this->get_ga_script($this->google_analytics_api->get_ga4_id());
    }

    public function send_events_to_analytics(): void
    {
        if (!$this->get_session_data()) {
            return;
        }
        ?>
        <script>
            const events = <?= $this->esc_json(json_encode($this->get_session_data()), true) ?>;
            for (let i = 0; i < events.length; i++) {
                let event = events[i];
                if (window.gtag) {
                    window.gtag('event', event.type, event.content);
                }
            }
        </script>
        <?php
        $_SESSION[Google_Analytics_Module::PARAM_SESSION_NAME] = [];
    }

    private function get_session_data(): ?array
    {
        return !empty($_SESSION[Google_Analytics_Module::PARAM_SESSION_NAME]) ? $_SESSION[Google_Analytics_Module::PARAM_SESSION_NAME] : null;
    }

    private function esc_json($json, $html = false): string
    {
        return _wp_specialchars($json, $html ? ENT_NOQUOTES : ENT_QUOTES, 'UTF-8', true);
    }

    private function get_ga_script(string $id): string
    {
        ob_start();
        ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $id ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('config', '<?= $id ?>'<?php
                if ($this->get_ga_config()) {
                    echo ' ,' . $this->get_ga_config();
                }
                ?>);
            gtag('js', new Date());
        </script>

        <?php
        return ob_get_clean();
    }

    private function get_ga_config(): ?string
    {
        $user_hashed_id_option = $this->get_user_hashed_id_for_logged_in_user_option();
        $debug_view_option = $this->get_ga4_debug_view_option();

        $options = array_merge($user_hashed_id_option, $debug_view_option);

        if (empty($options)) {
            return null;
        }

        return json_encode($options);
    }

    private function get_ga4_debug_view_option(): array
    {
        $debug_view = $this->google_analytics_api->get_ga4_debug_view();

        $option = [];

        if ($debug_view) {
            $option = [
                'debug_mode' => true
            ];
        }

        return $option;
    }

    private function get_user_hashed_id_for_logged_in_user_option(): array
    {
        $user_id = $this->google_analytics_api->get_user_hashed_id_for_logged_in_user();

        $option = [];

        if ($user_id) {
            $option = [
                'user_id' => $user_id
            ];
        }

        return $option;
    }
}
