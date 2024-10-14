<?php

namespace bpmj\wpidea\admin\pages\bundle_editor\core\configuration;

use bpmj\wpidea\admin\pages\bundle_editor\Bundle_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\bundle_editor\core\fields\Bundle_Content_Field;
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Message;
use bpmj\wpidea\controllers\admin\Admin_Settings_Fields_Ajax_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\sales\product\api\dto\Product_DTO;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\api\Product_API_Search_Criteria;

class Content_Bundle_Group extends Abstract_Settings_Group
{
    public const GROUP_NAME = 'contents';

    private const BUNDLED_PRODUCTS = 'bundled_products';

    private Interface_Product_API $product_api;

    public function __construct(
        Interface_Product_API $product_api
    )
    {
        $this->product_api = $product_api;
    }
    public function get_name(): string
    {
        return self::GROUP_NAME;
    }

    public function register_fields(): void
    {
        $this->add_field($this->get_info_field());
        $this->add_field($this->get_bundle_content_field());
    }

    private function get_bundle_content_field(): Abstract_Setting_Field
    {
        $edited_bundle_id = $this->get_product_id();

        return (new Bundle_Content_Field(self::BUNDLED_PRODUCTS, $this->get_save_bundle_content_endpoint(), $this->get_select_options()))
            ->set_sanitize_callback(function ($value) use ($edited_bundle_id) {
                if(!is_array($value)) {
                    return [];
                }

                $self = array_search($edited_bundle_id, $value, true);

                if( $self !== false ) {
                    unset($value[$self]);
                }

                return array_values(array_unique($value));
            });
    }

    private function get_info_field(): Abstract_Setting_Field
    {
        return new Message($this->translator->translate('bundle_editor.sections.bundle_content.info'));
    }

    private function get_save_bundle_content_endpoint(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_field_value', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            Bundle_Editor_Page_Renderer::BUNDLE_ID_QUERY_ARG_NAME => $this->get_product_id()
        ]);
    }

    private function get_product_id(): int
    {
        $id = $this->current_request->get_query_arg(Bundle_Editor_Page_Renderer::BUNDLE_ID_QUERY_ARG_NAME);

        return (int)$id;
    }

    private function get_select_options(): array
    {
        $criteria = Product_API_Search_Criteria::create()
            ->set_is_bundle(false);

        return $this->product_api->find_by_criteria($criteria)->map(static fn(Product_DTO $product) => [
            'value' => $product->get_id(),
            'name' => htmlspecialchars($product->get_name(), ENT_QUOTES)
        ]);
    }
}