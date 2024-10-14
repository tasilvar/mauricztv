<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\service;

use bpmj\wpidea\courses\acl\Interface_Variable_Prices_ACL;
use bpmj\wpidea\courses\core\dto\Course_DTO;
use bpmj\wpidea\courses\core\entities\Course_With_Product;
use bpmj\wpidea\courses\core\repositories\Interface_Course_With_Product_Repository;
use bpmj\wpidea\learning\course\{Course_ID, Page_ID};
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;
use DateTime;

class Course_Creator_Service
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Interface_Product_API $product_api;
    private Interface_Course_With_Product_Repository $course_with_product_repository;
    private Interface_Product_Repository $product_repository;
    private Interface_Variable_Prices_ACL $variable_prices_acl;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Interface_Product_API $product_api,
        Interface_Course_With_Product_Repository $course_with_product_repository,
        Interface_Product_Repository $product_repository,
        Interface_Variable_Prices_ACL $variable_prices_acl
    ) {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->product_api = $product_api;
        $this->course_with_product_repository = $course_with_product_repository;
        $this->product_repository = $product_repository;
        $this->variable_prices_acl = $variable_prices_acl;
    }

    public function save_course(Course_DTO $dto): ?Course_ID
    {
        $model = $this->create_model($dto);

        if(!$model){
            return null;
        }

        return $this->course_with_product_repository->save($model);
    }

    public function save_variable_prices(int $product_id, array $fields): array
    {
        return $this->variable_prices_acl->save($product_id, $fields);
    }

    public function get_variable_prices(int $post_id): string
    {
       return $this->variable_prices_acl->get_variable_prices($post_id);
    }

    public function get_variable_prices_to_array(int $product_id): array
    {
        $price_variants = $this->product_api->get_price_variants($product_id);

        return $price_variants->variable_prices;
    }

    public function get_variable_prices_add_to_cart_links_html(int $product_id): ?string
    {
        $price_variants = $this->product_api->get_price_variants($product_id);

        if(!$price_variants->has_pricing_variants){
            return null;
        }

        return $this->view_provider->get_admin('/course/variable-prices-add-to-cart-links', [
            'product_id' => $product_id,
            'variable_prices' => $price_variants->variable_prices,
            'translator' => $this->translator
        ]);
    }

    private function create_model(Course_DTO $dto): ?Course_With_Product
    {
        $product = $this->product_repository->find(new Product_ID($dto->product_id));

        if(!$product){
            return null;
        }

        $post_date = $dto->post_date ? new DateTime($dto->post_date) : null;
        $post_date_gmt = $dto->post_date_gmt ? new DateTime($dto->post_date_gmt) : null;

        return Course_With_Product::create(
            $dto->id ? new Course_ID($dto->id) : null,
            $product->get_id(),
            $dto->page_id ? new Page_ID($dto->page_id) : null,
            $dto->redirect_page,
            $dto->redirect_url,
            $dto->certificate_template_id,
            $dto->drip_value,
            $dto->drip_unit,
            $post_date,
            $post_date_gmt,
            $dto->cloned_from_id ? new Course_ID($dto->cloned_from_id) : null,
        );
    }
}