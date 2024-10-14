<?php

namespace bpmj\wpidea\admin\purchase_redirections;

use bpmj\wpidea\admin\pages\purchase_redirections\Purchase_Redirections_Page_Renderer;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;

class Purchase_Redirections_View
{
    private Purchase_Redirections_Page_Renderer $purchase_redirections_page_renderer;
    private Interface_Packages_API $packages_api;

    public function __construct(
        Purchase_Redirections_Page_Renderer $purchase_redirections_page_renderer,
        Interface_Packages_API $packages_api
    ) {
        $this->purchase_redirections_page_renderer = $purchase_redirections_page_renderer;
        $this->packages_api = $packages_api;
    }

    public function render()
    {
        if (!$this->packages_api->has_access_to_feature(Packages::FEAT_AFTER_PURCHASE_REDIRECTIONS)) {
            $this->purchase_redirections_page_renderer->render_page_wrong_plan();
            return;
        }

        $this->purchase_redirections_page_renderer->render_page();
    }
}
