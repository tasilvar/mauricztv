<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\digital_product_creator;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\view\Admin_View_Names;

class Digital_Product_Creator_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;

    public function __construct(
        Interface_View_Provider $view_provider
    )
    {
        $this->view_provider = $view_provider;
    }

    public function get_rendered_page(): string
    {
        return $this->view_provider->get_admin(Admin_View_Names::DIGITAL_PRODUCT_CREATOR);
    }
}