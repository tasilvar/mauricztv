<?php

namespace bpmj\wpidea\controllers;

use Exception;

class Ajax_Controller extends Base_Controller
{
    public const ERROR_CODE = 500;

    protected function check_returned_params($json): bool
    {
        return $this->is_json($json);
    }

    private function is_json($json): bool
    {
        json_decode($json);

        if (json_last_error() === JSON_ERROR_NONE) {
            return true;
        }

        return false;
    }

    protected function send_success($return_params): void
    {
        if ($this->status != self::STATUS_SUCCESS) {
            http_response_code(self::ERROR_CODE);
        }

        echo $return_params;
        /** ! do not delete to exit, you will turn on unnecessary components ! */
        $this->do_exit();
    }

    protected function send_error(Exception $exception): void
    {
        http_response_code($exception->getCode());
        echo json_encode(['error_message' => $exception->getMessage()]);
        /** ! do not delete to exit, you will turn on unnecessary components ! */
        $this->do_exit();
    }

    private function admin_init_detection(): void
    {
        add_action('admin_init', function () {
            echo 'admin init detection!';
        });
    }

    protected function do_exit(): void
    {
        if (!$this->action->is_admin()) {
            $this->admin_init_detection();
        }
        exit();
    }

    protected function fail(string $error_message = ''): string
    {
        return $this->return_as_json(
            self::STATUS_ERROR,
            [
                'message' => $error_message
            ]
        );
    }

    protected function success_with_warning_message(array $data = []): string
    {
        return $this->return_as_json(
            self::STATUS_SUCCESS,
            $data
        );
    }

    protected function success(array $data = []): string
    {
        return $this->return_as_json(
            self::STATUS_SUCCESS,
            $data
        );
    }

}
