<?php

use bpmj\wpidea\View;

/** @var string $message */
/** @var array $buttons_settings */
/** @var bool $show_timer */
/** @var string $expiration_date */

echo View::get('/admin/trial-bar/notice-with-timer', [
    'message' => $message,
    'buttons' => $buttons_settings,
    'upper_button_text' => __('Select your WP Idea', BPMJ_EDDCM_DOMAIN),
    'timestamp' => $show_timer ? $expiration_date : 0,
]);
