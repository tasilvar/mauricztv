<?php

namespace bpmj\wpidea\admin\helpers\html;

use bpmj\wpidea\View;

class Popup extends Abstract_Renderable_Element
{
    private const TYPE_STANDARD = 'standard';
    private const TYPE_AJAX = 'ajax';

    private $id;

    private $content;

    private $ajax_action;

    private $ajax_params;

    private $type;

    private $timeout;

    private $auto_open;

    private $show_close_button;

    private function __construct(string $type, string $id, ?string $content, ?string $ajax_action, ?array $ajax_params)
    {
        $this->id = $id;
        $this->content = $content;
        $this->ajax_action = $ajax_action;
        $this->ajax_params = $ajax_params;
        $this->type = $type;
    }

    public static function create_ajax(string $id, string $ajax_action, array $ajax_params): self
    {
        return new self(self::TYPE_AJAX, $id, null, $ajax_action, $ajax_params);
    }

    public static function create(string $id, string $content): self
    {
        return new self(self::TYPE_STANDARD, $id, $content, null, null);
    }

    public function set_timeout($timeout): void
    {
        $this->timeout = $timeout;
    }

    public function get_html(): string
    {
        if ($this->type === self::TYPE_STANDARD) {
            return View::get_admin('/helpers/html/popup', [
                'id' => $this->get_id(),
                'classes' => $this->get_classes(),
                'content' => $this->content,
                'timeout' => $this->timeout,
                'auto_open' => $this->auto_open,
                'show_close_button' => $this->show_close_button
            ]);
        }

        if ($this->type === self::TYPE_AJAX) {
            return View::get_admin('/helpers/html/popup-ajax', [
                'action' => $this->ajax_action,
                'ajax_params' => $this->ajax_params,
                'id' => $this->get_id(),
                'classes' => $this->get_classes(),
                'timeout' => $this->timeout,
                'auto_open' => $this->auto_open,
                'show_close_button' => $this->show_close_button
            ]);
        }
    }

    public function get_id(): string
    {
        return $this->id;
    }

    public function auto_open(): self
    {
        $this->auto_open = true;
        return $this;
    }

    public function show_close_button(): self
    {
        $this->show_close_button = true;
        return $this;
    }
}
