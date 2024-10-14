<?php

namespace bpmj\wpidea\admin\media;

use bpmj\wpidea\admin\helpers\html\Popup;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\View;

class Media_Limit_Popup {

    private Interface_Translator $translator;
    private Interface_Actions $actions;

    private const BPMJ_WPI_MEDIA_I18N_JS_VAR = 'BPMJ_WPI_MEDIA_I18N';

    public function __construct(
        Interface_Actions $actions,
        Interface_Translator $translator
    ) {
        $this->actions = $actions;
        $this->translator = $translator;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::AMIN_PRINT_FOOTER_SCRIPTS, [$this, 'print_transalation_strings_as_js_variable']);
    }

    public function print_transalation_strings_as_js_variable(): void
    {
        echo "<script>var " . self::BPMJ_WPI_MEDIA_I18N_JS_VAR . "=" . $this->get_json_translations() . "</script>";
    }

    private function get_json_translations(): string
    {
        return json_encode($this->get_popup_as_variable_in_array()) ?: '[]';
    }

    private function get_popup_as_variable_in_array(): array
    {
        return ['media_limit_popup_html' => $this->get_limit_popup_html()];
    }

    private function get_limit_popup_html(): string
    {
        return Popup::create(
            'media-limit-exceeded-popup',
            View::get_admin('/popup/media-warning', [
                'translator' => $this->translator
            ])
        )->get_html();
    }
}