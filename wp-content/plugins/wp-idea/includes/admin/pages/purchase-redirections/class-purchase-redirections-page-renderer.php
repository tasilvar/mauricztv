<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\purchase_redirections;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\modules\purchase_redirects\api\controllers\Admin_Purchase_Redirects_Ajax_Controller;
use bpmj\wpidea\modules\purchase_redirects\core\repositories\Interface_Purchase_Redirect_Repository;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\routing\Url_Generator;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Admin_View_Names;
use bpmj\wpidea\view\Interface_View_Provider;
use JsonException;

class Purchase_Redirections_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Interface_Product_Repository $product_repository;
    private Interface_Purchase_Redirect_Repository $purchase_redirect_repository;
    private Url_Generator $url_generator;
    private Interface_Packages_API $packages_api;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Interface_Product_Repository $product_repository,
        Interface_Purchase_Redirect_Repository $purchase_redirect_repository,
        Url_Generator $url_generator,
        Interface_Packages_API $packages_api
    ) {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->product_repository = $product_repository;
        $this->purchase_redirect_repository = $purchase_redirect_repository;
        $this->url_generator = $url_generator;
        $this->packages_api = $packages_api;
    }

    public function get_rendered_page(): string
    {
        return $this->get_purchase_redirection_page_html();
    }

    private function get_purchase_redirection_page_html(string $info_upgrade_license = ''): string
    {
        $this->register_assets();

        return $this->view_provider->get_admin(Admin_View_Names::PURCHASE_REDIRECTIONS, [
            'page_title' => $this->translator->translate('purchase_redirections.page_title'),
            'json_config' => $this->get_table_json_config(),
            'info_upgrade_license' => $info_upgrade_license
        ]);
    }

    public function render_page_wrong_plan(): void
    {
        $info_upgrade_license =  $this->packages_api->render_no_access_to_feature_info(Packages::FEAT_AFTER_PURCHASE_REDIRECTIONS);

        echo $this->get_purchase_redirection_page_html($info_upgrade_license);
    }

    private function register_assets(): void
    {
        wp_enqueue_script('table', BPMJ_EDDCM_URL . '/assets/admin/js/purchase-redirections-table.js', [],
            BPMJ_EDDCM_VERSION, true);
    }

    private function get_table_config(): array
    {
        return [
            'products' => $this->get_product_list(),
            'redirections' => $this->get_redirections(),
            'save_data_endpoint' => $this->get_save_data_endpoint(),
            'i18n' => $this->get_translations()
        ];
    }

    private function get_table_json_config(): string
    {
        try {
            return str_replace("\u0022","\\\\\"",json_encode($this->get_table_config(), JSON_THROW_ON_ERROR | JSON_HEX_QUOT));
        } catch (JsonException $e) {
            return '{}';
        }
    }

    private function get_product_list(): array
    {
        return $this->product_repository->find_all()->map(static fn(Product $product) => [
            'value' => $product->get_id()->to_int(),
            'name' => $product->get_name()->get_value()
        ]);
    }

    private function get_redirections(): array
    {
        return $this->purchase_redirect_repository->get_redirections_in_array();
    }

    private function get_save_data_endpoint(): string
    {
        return $this->url_generator->generate(Admin_Purchase_Redirects_Ajax_Controller::class, 'update', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
        ]);
    }

    private function get_translations(): array
    {
        return [
            'purchased_product' => $this->translator->translate('purchase_redirects.purchased_product'),
            'redirect_url' => $this->translator->translate('purchase_redirects.redirect_url'),
            'priority' => $this->translator->translate('purchase_redirects.priority'),
            'active_rules' => $this->translator->translate('purchase_redirects.active_rules'),
            'no_active_rules' => $this->translator->translate('purchase_redirects.no_active_rules'),
            'new_rule' => $this->translator->translate('purchase_redirects.new_rule'),
            'new_condition' => $this->translator->translate('purchase_redirects.new_condition'),
            'remove_condition' => $this->translator->translate('purchase_redirects.remove_condition'),
            'condition.and' => $this->translator->translate('purchase_redirects.condition.and'),
            'condition.or' => $this->translator->translate('purchase_redirects.condition.or'),
            'select_product' => $this->translator->translate('purchase_redirects.select_product'),
            'enter_url' => $this->translator->translate('purchase_redirects.enter_url'),
            'save' => $this->translator->translate('purchase_redirects.save'),
            'saving' => $this->translator->translate('purchase_redirects.saving'),
            'be_careful' => $this->translator->translate('purchase_redirects.be_careful'),
            'you_have_unsaved_changes' => $this->translator->translate('purchase_redirects.you_have_unsaved_changes'),
            'reset_changes' => $this->translator->translate('purchase_redirects.reset_changes'),
            'save_success' => $this->translator->translate('purchase_redirects.save_success'),
            'save_error' => $this->translator->translate('purchase_redirects.save_error'),
            'remove_rule' => $this->translator->translate('purchase_redirects.remove_rule'),
        ];
    }
}