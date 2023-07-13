<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\affiliate_program_redirections;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\modules\affiliate_program\api\Affiliate_Program_API;
use bpmj\wpidea\modules\affiliate_program\api\dto\collections\External_Landing_Link_DTO_Collection;
use bpmj\wpidea\sales\product\api\Product_API;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\modules\affiliate_program\api\controllers\Admin_External_Landing_Link_Ajax_Controller;

class Affiliate_Redirections_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{

    private Affiliate_Program_API $affiliate_program_API;
    private Interface_Product_API $product_API;
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Affiliate_Program_API $affiliate_program_API,
        Interface_Product_API $product_API,
        Interface_Url_Generator $url_generator
    ) {
        $this->affiliate_program_API = $affiliate_program_API;
        $this->product_API = $product_API;
        $this->url_generator = $url_generator;
    }

    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array {
        $data_array = $this->dto_collection_to_array(
            $this->affiliate_program_API->get_external_landing_links()
        );

        $data_array = $this->add_delete_url_to_array($data_array, $this->url_generator);

        return $this->add_product_names_to_array($data_array, $this->product_API);
    }

    private function dto_collection_to_array(
        External_Landing_Link_DTO_Collection $external_landing_links
    ): array {
        $array = [];

        foreach ($external_landing_links as $link) {
            $array[] = [
                'id' => $link->get_id(),
                'product_id' => $link->get_product_id(),
                'url' => $link->get_landing_url()
            ];
        }

        return $array;
    }

    private function add_delete_url_to_array(array $data_array, Interface_Url_Generator $url_generator): array
    {
        foreach ($data_array as $index => $item) {
            $item['delete_url'] = $url_generator->generate(
                Admin_External_Landing_Link_Ajax_Controller::class,
                'delete',
                [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
                    'id' => $item['id']
                ]
            );

            $data_array[$index] = $item;
        }

        return $data_array;
    }

    private function add_product_names_to_array(array $data_array, Product_API $product_API): array
    {
        foreach ($data_array as $index => $item) {
            $product = $product_API->find($item['product_id']);
            $product_name = $product ? $product->get_name() : '';

            $item['product'] = $product_name;

            $data_array[$index] = $item;
        }

        return $data_array;
    }

    public function get_total(array $filters): int
    {
        return 0;
    }
}