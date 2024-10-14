<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\service_creator;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\app\services\Services_App_Service;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\view\Admin_View_Names;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\admin\pages\services\Services_Page_Renderer;

class Service_Creator_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Services_Page_Renderer $services_page_renderer;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Services_Page_Renderer $services_page_renderer
    )
    {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->services_page_renderer = $services_page_renderer;
    }

    public function get_rendered_page(): string
    {
        return $this->view_provider->get_admin(Admin_View_Names::SERVICE_CREATOR, [
            'translator' => $this->translator,
            'services_page_url' => $this->services_page_renderer->get_page_url(),
            'integrations' => []
        ]);
    }
}