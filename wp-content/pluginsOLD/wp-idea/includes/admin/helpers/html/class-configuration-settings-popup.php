<?php

namespace bpmj\wpidea\admin\helpers\html;

use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Configuration_Settings_Popup
{
    private Interface_Translator $translator;
    private Interface_View_Provider $view_provider;

    private bool $button_disabled = false;

    public function __construct(
        Interface_Translator $translator,
        Interface_View_Provider $view_provider
    ) {
        $this->translator = $translator;
        $this->view_provider = $view_provider;
    }

    public function disable_button(): self
    {
        $this->button_disabled = true;

        return $this;
    }

    public function get_html(string $id, string $title, Additional_Fields_Collection $additional_settings_fields = null): string
    {
        $popup = $this->get_popup($id, $title, $additional_settings_fields);

        $button = Button::create(
            $this->translator->translate('settings.popup.button.configure'),
            Button::TYPE_CLEAN,
            'configuration-button'
        )
            ->open_popup_on_click($popup);

        if($this->button_disabled) {
            $button->set_as_disabled();
        }

        return $button
                ->get_html();
    }

    private function get_popup(string $id, string $title, ?Additional_Fields_Collection $additional_settings_fields): Popup
    {
        return Popup::create(
            $id,
            $this->view_provider->get_admin('/popup/configuration-settings',
                [
                    'id' => $id,
                    'title' => $title,
                    'additional_settings_fields' => $additional_settings_fields,
                    'translator' => $this->translator
                ])
        )->set_classes('configuration-settings-popup');
    }
}
