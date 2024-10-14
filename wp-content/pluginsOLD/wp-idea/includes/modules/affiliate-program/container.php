<?php
use bpmj\wpidea\modules\affiliate_program\core\helpers\Interface_Encoder;
use bpmj\wpidea\modules\affiliate_program\infrastructure\helpers\Encoder;
use bpmj\wpidea\modules\affiliate_program\core\services\Interface_External_Landing_Link_Service;
use bpmj\wpidea\modules\affiliate_program\core\services\External_Landing_Link_Service;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Interface_External_Landing_Link_Persistence;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\External_Landing_Link_Persistence;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_External_Landing_Link_Repository;
use bpmj\wpidea\modules\affiliate_program\infrastructure\repositories\External_Landing_Link_Repository;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Partner_Repository;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Interface_Partner_Persistence;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Partner_Persistence;
use bpmj\wpidea\modules\affiliate_program\infrastructure\repositories\Commission_Wp_Repository;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Commission_Repository;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Interface_Commission_Persistence;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Commission_Persistence;
use bpmj\wpidea\modules\affiliate_program\core\io\Interface_Cookie_Based_Data_Provider;
use bpmj\wpidea\modules\affiliate_program\infrastructure\io\Cookie_Based_Data_Provider;
use bpmj\wpidea\modules\affiliate_program\infrastructure\repositories\Partner_WP_Repository;
use bpmj\wpidea\modules\affiliate_program\infrastructure\io\WP_Settings_Commission_Rules_Provider;
use bpmj\wpidea\modules\affiliate_program\core\io\Interface_Commission_Rules_Provider;
use bpmj\wpidea\modules\affiliate_program\core\io\Interface_Orders_Client;
use bpmj\wpidea\modules\affiliate_program\infrastructure\io\Orders_Client;

return [
    Interface_Encoder::class => DI\autowire(Encoder::class),
    Interface_External_Landing_Link_Service::class => DI\autowire(External_Landing_Link_Service::class),
    Interface_External_Landing_Link_Repository::class => DI\autowire(External_Landing_Link_Repository::class),
    Interface_External_Landing_Link_Persistence::class => DI\autowire(External_Landing_Link_Persistence::class),
    Interface_Commission_Repository::class => DI\autowire(Commission_Wp_Repository::class),
    Interface_Commission_Persistence::class => DI\autowire(Commission_Persistence::class),
    Interface_Cookie_Based_Data_Provider::class => DI\autowire(Cookie_Based_Data_Provider::class),
    Interface_Partner_Repository::class => DI\autowire(Partner_WP_Repository::class),
    Interface_Partner_Persistence::class => DI\autowire(Partner_Persistence::class),
    Interface_Commission_Rules_Provider::class => DI\autowire(WP_Settings_Commission_Rules_Provider::class),
    Interface_Orders_Client::class => DI\autowire(Orders_Client::class)
];
