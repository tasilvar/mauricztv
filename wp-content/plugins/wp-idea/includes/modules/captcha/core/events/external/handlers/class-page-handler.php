<?php

namespace bpmj\wpidea\modules\captcha\core\events\external\handlers;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\modules\captcha\web\Captcha_Scripts_Renderer;
use bpmj\wpidea\settings\Interface_Settings;

class Page_Handler implements Interface_Initiable
{
    private const CONTACT_PAGE = 'contact_page';

    private Captcha_Scripts_Renderer $captcha_scripts_renderer;
    private Interface_Settings $settings;
    private Interface_Events $events;
    private Current_Request $current_request;

    public function __construct(
        Captcha_Scripts_Renderer $captcha_scripts_renderer,
        Interface_Settings $settings,
        Interface_Events $events,
        Current_Request $current_request
    ) {
        $this->captcha_scripts_renderer = $captcha_scripts_renderer;
        $this->settings = $settings;
        $this->events = $events;
        $this->current_request = $current_request;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::PAGE_VIEWED, [$this, 'render_captcha_for_contact_page']);
    }

    public function render_captcha_for_contact_page(): void
    {
        $current_page_id = $this->current_request->get_current_page_id();

        if (!$current_page_id) {
            return;
        }

        $contact_page = (int)$this->settings->get(self::CONTACT_PAGE);

        if ($current_page_id === $contact_page) {
            $this->captcha_scripts_renderer->init();
        }
    }
}