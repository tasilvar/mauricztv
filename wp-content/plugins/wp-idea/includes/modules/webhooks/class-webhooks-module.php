<?php

namespace bpmj\wpidea\modules\webhooks;

use bpmj\wpidea\modules\webhooks\api\controllers\Admin_Webhooks_Ajax_Controller;
use bpmj\wpidea\modules\webhooks\api\controllers\Admin_Webhooks_Controller;
use bpmj\wpidea\modules\webhooks\api\controllers\Resthooks_Controller;
use bpmj\wpidea\modules\webhooks\infrastructure\events\Event_Handlers_Initiator;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use Psr\Container\ContainerInterface;

class Webhooks_Module implements Interface_Module
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function init(): void
    {
        $this->container->get(Event_Handlers_Initiator::class);
        $this->container->get(Resthooks_Controller::class);
    }

    public function get_routes(): array
    {
        return [
            'admin/webhooks_ajax' => Admin_Webhooks_Ajax_Controller::class,
            'admin/webhooks' => Admin_Webhooks_Controller::class
        ];
    }

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
                'webhooks.actions.delete.success' => __('Webhook has been successfully deleted!', BPMJ_EDDCM_DOMAIN),
                'webhooks.actions.status.error.message' => 'Podczas zmiany statusu wystąpił błąd.',
            ],
            'en_US' => [
                'webhooks.actions.delete.success' => 'Webhook has been successfully deleted!',
                'webhooks.actions.status.error.message' => 'An error occurred while changing status.',
            ]
        ];
    }
}