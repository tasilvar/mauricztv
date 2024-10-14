<?php

namespace bpmj\wpidea\modules\search_engine\core\services;

use bpmj\wpidea\data_types\exceptions\Invalid_Url_Exception;
use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\learning\course\content\Interface_Readable_Course_Content_Repository;
use bpmj\wpidea\learning\services\Interface_Url_Resolver;
use bpmj\wpidea\modules\app_view\api\App_View_API;
use bpmj\wpidea\modules\search_engine\core\clients\access\Interface_Access_Module_Client;
use bpmj\wpidea\modules\search_engine\core\clients\sales\Interface_Sales_Module_Client;
use bpmj\wpidea\modules\search_engine\core\value_objects\Search_Result;
use bpmj\wpidea\modules\search_engine\core\value_objects\Search_Results_Collection;

class Search_Engine implements Interface_Search_Engine
{
    private Interface_Sales_Module_Client $sales_module_client;
    private Interface_Access_Module_Client $access_module_client;
    private Interface_Readable_Course_Content_Repository $course_content_repository;
    private Interface_Url_Resolver $url_resolver;
    private App_View_API $app_view_api;

    private string $sanitized_query;
    private ?int $user_id;
    private Search_Results_Collection $results;

    public function __construct(
        Interface_Sales_Module_Client $sales_module_client,
        Interface_Access_Module_Client $access_module_client,
        Interface_Readable_Course_Content_Repository $course_content_repository,
        Interface_Url_Resolver $url_resolver,
        App_View_API $app_view_api
    ) {
        $this->sales_module_client = $sales_module_client;
        $this->access_module_client = $access_module_client;
        $this->course_content_repository = $course_content_repository;
        $this->url_resolver = $url_resolver;
        $this->app_view_api = $app_view_api;
    }

    public function search(string $query, ?int $user_id = null): Search_Results_Collection
    {
        $this->sanitized_query = htmlspecialchars(strip_tags($query));
        $this->user_id = $user_id;
        $this->results = new Search_Results_Collection();

        $this->get_results_for_courses_content();
        $this->get_results_for_products();

        return $this->results;
    }

    private function get_results_for_courses_content(): void
    {
        if (!$this->user_id) {
            return;
        }

        $course_content_collection = $this->course_content_repository->find_by_query($this->sanitized_query);
        foreach ($course_content_collection as $course_content) {
            if (!$this->access_module_client->check_if_user_has_access_to_content($course_content->get_id()->to_int(), $this->user_id)) {
                continue;
            }

            $result = new Search_Result(
                $course_content->get_title(),
                $this->url_resolver->get_by_course_content_id($course_content->get_id())
            );

            $this->results->add($result);
        }
    }

    private function get_results_for_products(): void
    {
        $is_app_view_active = $this->app_view_api->is_active();

        if($is_app_view_active) {
            return;
        }

        $product_collection_dto = $this->sales_module_client->find_accessible_products_by_query($this->sanitized_query);

        foreach ($product_collection_dto as $product_dto) {
            if ($this->user_id && $this->access_module_client->check_if_user_has_access_to_course_product($product_dto->id, $this->user_id)) {
                continue;
            }

            try {
                $result = new Search_Result(
                    $product_dto->name,
                    new Url($product_dto->url)
                );

                $this->results->add($result);
            } catch (Invalid_Url_Exception $exception) {
            }
        }
    }
}