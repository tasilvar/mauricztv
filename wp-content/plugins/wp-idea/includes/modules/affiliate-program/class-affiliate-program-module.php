<?php

namespace bpmj\wpidea\modules\affiliate_program;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\modules\affiliate_program\api\Affiliate_Program_API;
use bpmj\wpidea\modules\affiliate_program\api\controllers\Admin_Affiliate_Ajax_Controller;
use bpmj\wpidea\modules\affiliate_program\api\controllers\Admin_External_Landing_Link_Ajax_Controller;
use bpmj\wpidea\modules\affiliate_program\api\controllers\Admin_External_Landing_Link_Controller;
use bpmj\wpidea\modules\affiliate_program\core\services\Affiliate_Program_Cookie_Setter;
use bpmj\wpidea\modules\affiliate_program\core\services\Affiliate_Redirector;
use bpmj\wpidea\modules\affiliate_program\core\services\New_Partner_Instantiator;
use bpmj\wpidea\modules\affiliate_program\core\services\Partner_To_Payment_Assigner;
use bpmj\wpidea\modules\affiliate_program\infrastructure\events\Event_Handlers_Initiator;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use Psr\Container\ContainerInterface;

class Affiliate_Program_Module implements Interface_Module
{
    public const AFFILIATE_PROGRAM_HTTP_GET_PARAMETER_NAME = 'afp';
    public const AFFILIATE_PROGRAM_HTTP_GET_REDIRECT_NAME = 'afp_redir';
    public const AFFILIATE_PROGRAM_HTTP_GET_CAMPAIGN_NAME = 'afp-campaign';

    public const COOKIE_LIFE_TIME = 60 * 60 * 24 * 30;

    public const COOKIE_NAME = 'publigo_ap_cookie';
    public const COOKIE_CAMPAIGN_NAME = 'publigo_ap_campaign_cookie';

    public const COMMISION_ATTRIBUTED = 'commision_attributed';

    private Affiliate_Program_Cookie_Setter $cookie_setter;
    private Affiliate_Redirector $affiliate_redirector;

    private Interface_Settings $settings;

    private New_Partner_Instantiator $new_partner_instantiator;

    private Subscription $subscription;

    private ContainerInterface $container;

    private Partner_To_Payment_Assigner $partner_to_payment_assigner;

    private Interface_Actions $actions;

    public function __construct(
        Affiliate_Program_Cookie_Setter $cookie_setter,
        Affiliate_Redirector $affiliate_redirector,
        Interface_Settings $settings,
        New_Partner_Instantiator $new_partner_instantiator,
        Partner_To_Payment_Assigner $partner_to_payment_assigner,
        Subscription $subscription,
        ContainerInterface $container,
        Interface_Actions $actions
    ) {
        $this->cookie_setter = $cookie_setter;
        $this->affiliate_redirector = $affiliate_redirector;
        $this->settings = $settings;
        $this->new_partner_instantiator = $new_partner_instantiator;
        $this->partner_to_payment_assigner = $partner_to_payment_assigner;
        $this->subscription = $subscription;
        $this->container = $container;
        $this->actions = $actions;
    }

    public function init(): void
    {
        if ($this->is_active()) {
            $this->cookie_setter->init();
            $this->affiliate_redirector->init();
            $this->partner_to_payment_assigner->init();
            $this->new_partner_instantiator->init();
            $this->container->get(Event_Handlers_Initiator::class);
            $this->enqueue_assets();
        }
        $this->container->get(Affiliate_Program_API::class);
    }

    public function get_routes(): array
    {
        return [
            'admin/affiliate_ajax' => Admin_Affiliate_Ajax_Controller::class,
            'admin/external_landing_link' => Admin_External_Landing_Link_Controller::class,
            'admin/external_landing_link_ajax' => Admin_External_Landing_Link_Ajax_Controller::class
        ];
    }

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
                'affiliate_program.actions.change_status.success' => 'Status został zmieniony!',
                'affiliate_program.actions.change_status.bulk.success' => 'Statusy zostały zmienione!',
                'affiliate_program.actions.delete.success' => 'Wybrana pozycja została usunięty!',
                'affiliate_program.actions.delete.bulk.success' => 'Wybrane pozycje zostały usunięte!',
                'affiliate_program_redirections.actions.add.success' => 'Link został dodany!',
            ],
            'en_US' => [
                'affiliate_program.actions.change_status.success' => 'The status has changed!',
                'affiliate_program.actions.change_status.bulk.success' => 'Statuses have changed!',
                'affiliate_program.actions.delete.success' => 'The selected item has been deleted!',
                'affiliate_program.actions.delete.bulk.success' => 'The selected items have been deleted!',
                'affiliate_program_redirections.actions.add.success' => 'The link has been added!',
            ]
        ];
    }

    private function is_active(): bool
    {
        return $this->subscription->get_plan() === Subscription_Const::PLAN_PRO &&
            $this->settings->get(Settings_Const::PARTNER_PROGRAM);
    }

    private function enqueue_assets(): void
    {
        $this->actions->add('wp_enqueue_scripts', function () {
            wp_enqueue_script(
                'wpi_affiliate_program',
                BPMJ_EDDCM_URL . 'includes/modules/affiliate-program/web/assets/affiliate-program-cookie.js',
                [],
                BPMJ_EDDCM_VERSION
            );
        });
    }
}