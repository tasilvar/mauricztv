<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\sales\orders\api\mappers;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\MappingOperation\Operation;
use bpmj\wpidea\modules\sales\orders\api\dto\Cart_Content_DTO;
use bpmj\wpidea\modules\sales\orders\api\dto\Client_DTO;
use bpmj\wpidea\modules\sales\orders\api\dto\Order_DTO;
use bpmj\wpidea\sales\order\cart\Cart_Content;
use bpmj\wpidea\sales\order\client\Client;
use bpmj\wpidea\sales\order\Order;

class Mapper
{
    private AutoMapper $auto_mapper;

    public function __construct()
    {
        $config = new AutoMapperConfig();
        $config->registerMapping(Client::class, Client_DTO::class);
        $config->registerMapping(Cart_Content::class, Cart_Content_DTO::class);
        $config->registerMapping(Order::class, Order_DTO::class)
            ->forMember('client', Operation::mapTo(Client_DTO::class))
            ->forMember('cart_content', Operation::mapTo(Cart_Content_DTO::class));

        $this->auto_mapper = new AutoMapper($config);
    }

    public function map($source, string $targetClass)
    {
        return $this->auto_mapper->map($source, $targetClass);
    }
}