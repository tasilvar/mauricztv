<?php

namespace bpmj\wpidea\admin\helpers\html;

use bpmj\wpidea\View;

class Button extends Abstract_Renderable_Element
{
    public const ACTION_NONE = 'none';
    public const ACTION_OPEN_POPUP = 'open_popup';

    public const TYPE_CLEAN = 'clean';
    public const TYPE_MAIN = 'main';
    public const TYPE_SECONDARY = 'secondary';
    public const TYPE_WARNING = 'warning';

    public const TYPE_MAIN_SMALL = 'main wpi-button--small';

    private $text;

    private $action = self::ACTION_NONE;

    private $type;

    /**
     * @var null|Popup
     */
    private $popup;

    private bool $disabled = false;

    private function __construct(string $text, string $type, string $classes)
    {
        $this->text = $text;
        $this->type = $type;
        $this->set_classes($classes);
    }


    public static function create(string $text, string $type = self::TYPE_CLEAN, string $classes = ''): self
    {
        return new self($text, $type, $classes);
    }

    public function open_popup_on_click(Popup $popup): self
    {
        $this->action = self::ACTION_OPEN_POPUP;
        $this->popup = $popup;

        return $this;
    }

    public function get_html(): string
    {
        $has_popup = $this->has_popup();
        $popup_id_attr = $has_popup
            ? 'data-popup-id=' . $this->popup->get_id()
            : '';
        $data = $popup_id_attr . ' ' . $this->get_data();

        return View::get_admin('/helpers/html/button', [
            'action' => $this->action,
            'type' => $this->type,
            'classes' => $this->get_classes(),
            'data' => $data,
            'text' => $this->text,
            'has_popup' => $has_popup,
            'popup_html' => $has_popup ? $this->popup->get_html() : '',
            'dashicon' => $this->get_dashicon() !== null ? $this->get_dashicon()->get_html() : '',
            'disabled' => $this->disabled
        ]);
    }

    private function has_popup(): bool
    {
        return $this->action === self::ACTION_OPEN_POPUP && ($this->popup instanceof Popup);
    }

    public function close_popup_on_click(): self
    {
        $this->add_data('close-popup-on-click');

        return $this;
    }

    public function set_as_disabled(): self
    {
        $this->disabled = true;

        return $this;
    }
}
