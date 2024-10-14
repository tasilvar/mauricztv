<?php

declare(strict_types=1);

namespace bpmj\wpidea\app\digital_products;

use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\sales\product\model\Product_ID;

class Digital_Product_Download_Protector implements Interface_Initiable
{
    private const DOWNLOAD_METHOD_DIRECT = 'direct';

    private Interface_Actions $actions;
    private Interface_Filters $filters;
    private Digital_Products_App_Service $digital_products_app_service;

    public function __construct(
        Interface_Actions $actions,
        Interface_Filters $filters,
        Digital_Products_App_Service $digital_products_app_service
    )
    {
        $this->actions = $actions;
        $this->filters = $filters;
        $this->digital_products_app_service = $digital_products_app_service;
    }

    public function init(): void
    {
        $this->force_direct_download_to_protect_file_path();
    }

    private function force_direct_download_to_protect_file_path(): void
    {
        $this->actions->add(Action_Name::PROCESS_VERIFIED_DOWNLOAD, function ($download_id){
            $is_digital_product_download = $this->digital_products_app_service->find_digital_product_by_offer_id(
                new Product_ID((int)$download_id)
            );

            if(!$is_digital_product_download){
                return;
            }

            $this->filters->add(Filter_Name::FILE_DOWNLOAD_METHOD, function() {
                return self::DOWNLOAD_METHOD_DIRECT;
            });
        });
    }
}