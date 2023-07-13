<?php

namespace bpmj\wpidea\admin\helpers\utils;

use bpmj\wpidea\admin\helpers\session\Session_Helper;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Snackbar implements Interface_Initiable
{
    private const SNACKBAR_MESSAGE_SESSION_PARAM = 'snackbar_message_string';
    private const SNACKBAR_MESSAGE_TYPE_SESSION_PARAM = 'snackbar_message_type';

    public const TYPE_DEFAULT = 'default';
    public const TYPE_ERROR = 'error';
    public const TYPE_WARNING = 'warning';

    private $message;

    private $type;

    /**
     * @var Session_Helper
     */
    private $session_helper;

    private $session_status_modified = false;

    public function __construct(
        Session_Helper $session_helper
    ) {
        $this->session_helper = $session_helper;
    }

    public function init(): void
    {
        $this->maybe_get_message_from_session();

        add_action('admin_enqueue_scripts', [$this, 'maybe_display_snackbar']);
    }

    public function display_message(string $message, string $type = self::TYPE_DEFAULT): void
    {
        $this->message = $message;
        $this->type = $type;
    }

    public function display_message_on_next_request(string $message, string $type = self::TYPE_DEFAULT): void
    {
        $this->maybe_start_session();

        $_SESSION[self::SNACKBAR_MESSAGE_SESSION_PARAM] = $message;
        $_SESSION[self::SNACKBAR_MESSAGE_TYPE_SESSION_PARAM] = $type;

        $this->maybe_restore_session_status();
    }

    public function has_message_to_display(): bool
    {
        return $this->get_message() !== null;
    }

    public function get_message(): ?string
    {
        return $this->message;
    }

    public function get_type(): ?string
    {
        return $this->type;
    }

    public function maybe_display_snackbar(): void
    {
        if (!$this->has_message_to_display()) {
            return;
        }

        wp_localize_script('bpmj-eddpc-admin-script', 'snackbarDataObject', [
            'message' => $this->get_message(),
            'type' => $this->get_type()
        ]);
    }

    public function maybe_get_message_from_session(): void
    {
        $this->maybe_start_session();

        $message = $_SESSION[self::SNACKBAR_MESSAGE_SESSION_PARAM] ?? null;
        $type = $_SESSION[self::SNACKBAR_MESSAGE_TYPE_SESSION_PARAM] ?? null;
        if ($message !== null) {
            unset(
                $_SESSION[self::SNACKBAR_MESSAGE_SESSION_PARAM],
                $_SESSION[self::SNACKBAR_MESSAGE_TYPE_SESSION_PARAM]
            );

            $this->message = $message;
            $this->type = $type;
        }

        $this->maybe_restore_session_status();
    }

    private function maybe_start_session(): void
    {
        $session_was_already_started = $this->session_helper->is_session_started();

        $this->session_helper->start_session();

        if($this->session_helper->is_session_started() && !$session_was_already_started) {
            $this->session_status_modified = true;
        }
    }

    private function maybe_restore_session_status(): void
    {
        if ($this->session_status_modified === false) {
            return;
        }

        $this->session_helper->close_session();

        $this->session_status_modified = false;
    }
}
