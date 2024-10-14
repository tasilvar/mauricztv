<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\bundle_creator;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\app\bundles\Bundles_App_Service;
use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\digital_products\model\Digital_Product_ID;
use bpmj\wpidea\digital_products\repository\Interface_Digital_Product_Repository;
use bpmj\wpidea\resources\Resource_Type;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\view\Admin_View_Names;

class Bundle_Creator_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Bundles_App_Service $bundles_app_service;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Bundles_App_Service $bundles_app_service
    )
    {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->bundles_app_service = $bundles_app_service;
    }

    public function get_rendered_page(): string
    {
        return $this->view_provider->get_admin(Admin_View_Names::BUNDLE_CREATOR, [
            'items' => $this->bundles_app_service->get_all_bundlable_items(),
            'translator' => $this->translator
        ]);
    }
}