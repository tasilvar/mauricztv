<?php

namespace bpmj\wpidea\admin\helpers\html;

use bpmj\wpidea\translator\Interface_Translator;
use \bpmj\wpidea\view\Interface_View_Provider;

class Explanation_Popup
{
    private Interface_Translator $translator;
    private Interface_View_Provider $view_provider;

    public function __construct(
        Interface_Translator $translator,
        Interface_View_Provider $view_provider
    ) {
        $this->translator = $translator;
        $this->view_provider = $view_provider;
    }

    public function get_html(string $id, string $title_translation_key, string $text_translation_key): void
    {
        $popup = $this->get_popup($id, $title_translation_key, $text_translation_key);

        Button::create('', Button::TYPE_CLEAN, 'help-button')
                ->open_popup_on_click($popup)
                ->add_class('inline-help-button')
                ->set_dashicon(Dashicon::create('editor-help'))
                ->print_html();
    }

    private function get_popup(string $id, string $title_translation_key, string $text_translation_key): Popup
    {
        $popup = Popup::create(
            $id,
            $this->view_provider->get_admin('/popup/explanation',
                [
                    'title' => $this->translator->translate($title_translation_key),
                    'text' => $this->translator->translate($text_translation_key)
                ])
        )
            ->set_classes('explanation-popup')
            ->show_close_button();

        return $popup;
    }

}
