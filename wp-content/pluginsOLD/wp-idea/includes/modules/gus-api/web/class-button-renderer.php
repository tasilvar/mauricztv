<?php

namespace bpmj\wpidea\modules\gus_api\web;

use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\translator\Interface_Translator;

class Button_Renderer
{
    private Interface_Actions $actions;
    private Interface_Translator $translator;

    public function __construct(
        Interface_Actions $actions,
        Interface_Translator $translator
    ) {
        $this->actions = $actions;
        $this->translator = $translator;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::DISPLAY_BUTTON_GET_DATA_FROM_GUS, [$this, 'display_button_html']);
    }

    public function display_button_html(): void
    {
        ob_start();
        ?>
        <button id="billing-nip-check" class="download_from_gus"><i class="fa fa-download"></i> &nbsp; <?= $this->translator->translate(
                'invoice_data.button.download_from_gus'
            ) ?></button>
        <p class="edd_error" id="message_invalid_nip_format"></p>
        <?php
        echo ob_get_clean();
    }
}
