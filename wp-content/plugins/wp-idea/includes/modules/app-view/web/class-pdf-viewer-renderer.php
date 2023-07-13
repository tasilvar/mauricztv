<?php

namespace bpmj\wpidea\modules\app_view\web;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\infrastructure\assets\Interface_Script_Loader;
use bpmj\wpidea\translator\Interface_Translator;

class PDF_Viewer_Renderer
{
    public const PDF_VIEWER_URL_PARAM_NAME = 'viewer_pdf';

    private Current_Request $current_request;
    private Interface_Translator $translator;
    private Interface_Actions $actions;
    private Interface_Script_Loader $script_loader;

    public function __construct(
        Current_Request $current_request,
        Interface_Translator $translator,
        Interface_Actions $actions,
        Interface_Script_Loader $script_loader
    ) {
        $this->current_request = $current_request;
        $this->translator = $translator;
        $this->actions = $actions;
        $this->script_loader = $script_loader;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::AFTER_BODY_OPEN_TAG, [$this, 'pdf_viewer_renderer']);
    }

    public function pdf_viewer_renderer(): void
    {
        $pdf_url = $this->current_request->get_query_arg(self::PDF_VIEWER_URL_PARAM_NAME);

        if (!$pdf_url) {
            return;
        }

        $this->enqueue_assets($pdf_url);

        echo $this->get_pdf_viewer_html();
    }

    private function enqueue_assets(string $pdf_url): void
    {
        $this->script_loader->enqueue_script(
            'pdf_viewer_app_script',
            BPMJ_EDDCM_URL . 'includes/modules/app-view/web/assets/js/pdf.viewer.app.min.js',
            [],
            BPMJ_EDDCM_VERSION
        );

        $this->script_loader->localize_script(
            'pdf_viewer_app_script',
            'pdf_viewer',
            [
                'url_pdf' => $pdf_url,
                'url_js_pdf_worker' => BPMJ_EDDCM_URL . 'includes/modules/app-view/web/assets/js/pdf.worker.min.js'
            ]
        );
    }

    private function get_pdf_viewer_html(): string
    {
        ob_start();
        ?>
        <div class="viewer-pdf-overlay">

            <div class="toolbarTop">
                <button class="toolbarButton pageUp" id="previous"></button>
                <button class="toolbarButton pageDown" id="next"></button>

                <input type="text" id="pageNumber" class="toolbarField pageNumber" value="1" size="4" min="1">

                <button class="toolbarButton zoomOut" id="zoomOut"></button>
                <button class="toolbarButton zoomIn" id="zoomIn"></button>
            </div>

            <div id="viewerContainer">
                <div id="viewer" class="pdfViewer"></div>
            </div>

            <div id="loadingBar">
                <div class="progress"></div>
                <div class="glimmer"></div>
            </div>

            <div id="errorWrapper" hidden="true">
                <div id="errorMessageLeft">
                    <span id="errorMessage"></span>
                    <button id="errorShowMore">
                        More Information
                    </button>
                    <button id="errorShowLess">
                        Less Information
                    </button>
                </div>
                <div id="errorMessageRight">
                    <button id="errorClose">
                        Close
                    </button>
                </div>
                <div class="clearBoth"></div>
                <textarea id="errorMoreInfo" hidden="true" readonly="readonly"></textarea>
            </div>

            <div class="viewer-pdf-close">
                <button onclick="window.history.back(); return false;"><?= $this->translator->translate('app_view.pdf_viewer.close') ?></button>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }
}
