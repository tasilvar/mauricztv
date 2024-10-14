<?php

namespace bpmj\wpidea\modules\webhooks\api\controllers;

use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\modules\webhooks\core\exceptions\Missing_Argument_Exception;
use bpmj\wpidea\modules\webhooks\core\exceptions\Webhook_Already_Exists_Exception;
use bpmj\wpidea\modules\webhooks\core\exceptions\Webhook_Not_Found_Exception;
use bpmj\wpidea\modules\webhooks\core\services\Interface_Webhook_Registration_Service;
use bpmj\wpidea\shared\abstractions\controllers\rest\Rest_Controller;
use bpmj\wpidea\shared\infrastructure\controllers\Interface_Rest_Registration_Service;
use bpmj\wpidea\shared\infrastructure\controllers\Rest_Router;

class Resthooks_Controller extends Rest_Controller
{
    private const URL_ROUTE_ME = '/me/';
    private const URL_ROUTE_SUBSCRIBE = '/subscribe/';
    private const URL_ROUTE_UNSUBSCRIBE = '/unsubscribe/';

    private Interface_Webhook_Registration_Service $interface_webhook_registration_service;

    public function __construct
    (
        Interface_Actions $actions,
        Interface_Rest_Registration_Service $rest_service,
        Rest_Router $rest_router,
        Interface_Webhook_Registration_Service $interface_webhook_registration_service
    ) {
        $this->interface_webhook_registration_service = $interface_webhook_registration_service;

        parent::__construct($actions, $rest_service, $rest_router);
    }

    protected function register_routes(): void
    {
        $this->register_route(
            self::URL_ROUTE_ME,
            Rest_Controller::READABLE,
            'me'
        );

        $this->register_route(
            self::URL_ROUTE_SUBSCRIBE,
            Rest_Controller::CREATABLE,
            'subscribe'
        );

        $this->register_route(
            self::URL_ROUTE_UNSUBSCRIBE,
            Rest_Controller::DELETABLE,
            'unsubscribe'
        );
    }

    public function me(): array
    {
        return self::RETURN_ARRAY_SUCCESS;
    }

    /**
     * @throws Missing_Argument_Exception
     * @throws Webhook_Already_Exists_Exception
     */
    public function subscribe(): array
    {
        $request_data = json_decode(file_get_contents('php://input'), true);
        $validated_request_data = $this->validate_and_prepare_request_data($request_data);

        $add_webhook = $this->interface_webhook_registration_service->subscribe(
            $validated_request_data['name'],
            $validated_request_data['url']
        );

        if (!$add_webhook) {
            throw new Webhook_Already_Exists_Exception();
        }

        return self::RETURN_ARRAY_SUCCESS;
    }

    /**
     * @throws Missing_Argument_Exception
     * @throws Webhook_Not_Found_Exception
     */
    public function unsubscribe(): array
    {
        $request_data = json_decode(file_get_contents('php://input'), true);
        $validated_request_data = $this->validate_and_prepare_request_data($request_data);

        $remove_webhook = $this->interface_webhook_registration_service->unsubscribe(
            $validated_request_data['name'],
            $validated_request_data['url']
        );

        if (!$remove_webhook) {
            throw new Webhook_Not_Found_Exception();
        }

        return self::RETURN_ARRAY_SUCCESS;
    }

    /**
     * @throws Missing_Argument_Exception
     */
    public function validate_and_prepare_request_data($request_data): array
    {
        $url = $request_data['hookUrl'] ?? '';
        $name = $request_data['hookName'] ?? '';
        $host = $request_data['hookHost'] ?? '';

        if (empty($url) || empty($name) || empty($host)) {
            throw new Missing_Argument_Exception();
        }
        return [
            'url' => $url,
            'name' => $name,
            'host' => $host,
        ];
    }
}
