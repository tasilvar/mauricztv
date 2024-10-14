<?php

namespace bpmj\wpidea\templates_system;

use bpmj\wpidea\settings\Interface_Settings;

class Experimental_Cart_View_Handler
{

    private const USE_EXPERIMENTAL_CART_VIEW = 'use_experimental_cart_view';
    private const EXPERIMENTAL_CART_GO_BACK_BUTTON_ENABLED = 'experimental_cart_go_back_button_enabled';
    private const EXPERIMENTAL_CART_BUTTON_TEXT = 'experimental_cart_button_text';
    private const EXPERIMENTAL_CART_BUTTON_URL = 'experimental_cart_button_url';
    private Interface_Settings $settings;

    public function __construct(Interface_Settings $settings)
    {
        $this->settings = $settings;
    }

    public function should_use_experimental_view(): bool
    {
        return $this->settings->get(self::USE_EXPERIMENTAL_CART_VIEW) === 'experimental';
    }

    public function should_show_go_back_button(): bool
    {
        return (bool)$this->settings->get(self::EXPERIMENTAL_CART_GO_BACK_BUTTON_ENABLED);
    }

    public function get_button_text(): ?string
    {
        return $this->settings->get(self::EXPERIMENTAL_CART_BUTTON_TEXT);
    }

    public function get_button_url(): ?string
    {
        return $this->settings->get(self::EXPERIMENTAL_CART_BUTTON_URL);
    }
}