<?php

namespace bpmj\wpidea\modules\sales\orders\api\controllers;

use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\sales\orders\api\dto\Order_DTO;
use bpmj\wpidea\modules\sales\orders\api\mappers\Mapper;
use bpmj\wpidea\modules\sales\orders\core\exceptions\Order_Not_Found_Exception;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;
use bpmj\wpidea\sales\order\Order_Query_Criteria;
use bpmj\wpidea\sales\order\services\Interface_Orders_Service;
use bpmj\wpidea\shared\abstractions\controllers\rest\Rest_Controller;
use bpmj\wpidea\shared\exceptions\Missing_Argument_Exception;
use bpmj\wpidea\shared\infrastructure\controllers\Interface_Rest_Registration_Service;
use bpmj\wpidea\shared\infrastructure\controllers\Rest_Router;

class Orders_Controller extends Rest_Controller
{
    private const URL_ROUTE_ORDERS = '/orders/';
    private const URL_ROUTE_ORDER = '/orders/(?P<id>[\d]+)/';
    private const URL_ROUTE_ORDER_REVOKE = '/orders/(?P<id>[\d]+)/revoke';

    private Interface_Orders_Repository $orders_repository;
    private Interface_Orders_Service $orders_service;
    private Mapper $mapper;

    public function __construct
    (
        Interface_Actions $actions,
        Interface_Rest_Registration_Service $rest_service,
        Rest_Router $rest_router,
        Interface_Orders_Repository $orders_repository,
        Interface_Orders_Service $orders_service,
        Mapper $mapper
    ) {
        $this->orders_repository = $orders_repository;
        $this->orders_service = $orders_service;
        $this->mapper = $mapper;

        parent::__construct($actions, $rest_service, $rest_router);
    }

    protected function register_routes(): void
    {
        $this->register_route(
            self::URL_ROUTE_ORDERS,
            Rest_Controller::READABLE,
            'get'
        );

        $this->register_route(
            self::URL_ROUTE_ORDER,
            Rest_Controller::READABLE,
            'get'
        );

        $this->register_route(
            self::URL_ROUTE_ORDER_REVOKE,
            Rest_Controller::EDITABLE,
            'revoke'
        );
    }

    public function get(object $request): array
    {
        if (!empty($request['id'])) {
            $order = $this->orders_repository->find_by_id($request['id']);
            $order_dto = $this->mapper->map($order, Order_DTO::class);
            return (array)$order_dto;
        }

        $email = $request['email'] ?? '';

        $criteria_array = [
            'perPage' => -1,
            'page' => 1,
            'filters' => [
                [
                    'id' => 'user_email',
                    'value' => $email
                ]
            ],
            'sortBy' => (new Sort_By_Clause())->sort_by('date', true)
        ];

        $orders = $this->orders_repository->find_by_criteria(new Order_Query_Criteria($criteria_array));

        $result = [
            'orders' => []
        ];
        foreach ($orders as $order) {
            $order_dto = $this->mapper->map($order, Order_DTO::class);
            $result['orders'][] = (array)$order_dto;
        }

        return $result;
    }

    /**
     * @throws Missing_Argument_Exception
     * @throws Order_Not_Found_Exception
     */
    public function revoke(object $request): array
    {
        if (empty($request['id'])) {
            throw new Missing_Argument_Exception();
        }

        $order_id = (int)$request['id'];

        $order = $this->orders_repository->find_by_id($order_id);
        if ($order === null) {
            throw new Order_Not_Found_Exception();
        }

        $this->orders_service->revoke($order_id);

        return self::RETURN_ARRAY_SUCCESS;
    }
}
