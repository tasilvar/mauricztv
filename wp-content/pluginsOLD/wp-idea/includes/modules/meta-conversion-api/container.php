<?php

use bpmj\wpidea\modules\meta_conversion_api\core\io\Interface_Cookie_Based_Data_Provider;
use bpmj\wpidea\modules\meta_conversion_api\core\providers\{Interface_User_Data_Provider, User_Data_Provider};
use bpmj\wpidea\modules\meta_conversion_api\core\services\{Interface_Meta_Conversion_API_Sender,
    Interface_Page_Info_Getter,
    Meta_Conversion_API_Sender,
    Page_Info_Getter
};
use bpmj\wpidea\modules\meta_conversion_api\infrastructure\io\Cookie_Based_Data_Provider;

return [
    Interface_Meta_Conversion_API_Sender::class => DI\autowire(Meta_Conversion_API_Sender::class),
    Interface_Cookie_Based_Data_Provider::class => DI\autowire(Cookie_Based_Data_Provider::class),
    Interface_User_Data_Provider::class => DI\autowire(User_Data_Provider::class),
    Interface_Page_Info_Getter::class => DI\autowire(Page_Info_Getter::class)
];
