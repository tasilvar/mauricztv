<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\bundle_list;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\tables\dynamic\Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Admin_View_Names;
use bpmj\wpidea\view\Interface_View_Provider;

class Bundles_List_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Bundles_Table_Config_Provider $config_provider;
    private Interface_Packages_API $packages_api;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Dynamic_Tables_Module $dynamic_tables_module,
        Bundles_Table_Config_Provider $config_provider,
        Interface_Packages_API $packages_api
    ) {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->config_provider = $config_provider;
        $this->packages_api = $packages_api;
    }


    public function get_rendered_page(): string
    {
        return $this->view_provider->get_admin(Admin_View_Names::BUNDLES, [
            'page_title' => $this->translator->translate('bundles_list.page_title'),
            'table' => $this->prepare_table(),
            'translator' => $this->translator,
            'has_access_for_bundles' => $this->has_access_for_bundles(),
            'no_access_info' => $this->packages_api->render_no_access_to_feature_info(Packages::FEAT_COURSE_BUNDLING)
        ]);
    }

    private function prepare_table(): Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->config_provider->get_config()
        );
    }

    private function has_access_for_bundles(): bool
    {
        return $this->packages_api->has_access_to_feature(Packages::FEAT_COURSE_BUNDLING);
    }
}
