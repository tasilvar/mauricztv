<?php

namespace bpmj\wpidea\modules\purchase_redirects;

use bpmj\wpidea\modules\purchase_redirects\api\controllers\Admin_Purchase_Redirects_Ajax_Controller;
use bpmj\wpidea\modules\purchase_redirects\infrastructure\events\Event_Handlers_Initiator;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use Psr\Container\ContainerInterface;

class Purchase_Redirects_Module implements Interface_Module
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function init(): void
    {
        $this->container->get(Event_Handlers_Initiator::class);
    }

    public function get_routes(): array
    {
        return [
            'admin/purchase_redirects_ajax' => Admin_Purchase_Redirects_Ajax_Controller::class,
        ];
    }

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
                'purchase_redirects.redirect_notice' => 'Za chwilę nastąpi przekierowanie ...'
            ],
            'en_US' => [
                'purchase_redirects.redirect_notice' => 'You will be redirected shortly ...'
            ]
        ];
    }
}