<?php

use bpmj\wpidea\modules\purchase_redirects\core\repositories\Interface_Purchase_Redirect_Repository;
use bpmj\wpidea\modules\purchase_redirects\infrastructure\repositories\Purchase_Redirect_Repository;
use bpmj\wpidea\modules\purchase_redirects\acl\Interface_Cart_Module_ACL;
use bpmj\wpidea\modules\purchase_redirects\acl\Cart_Module_ACL;

return [
    Interface_Purchase_Redirect_Repository::class => DI\autowire(Purchase_Redirect_Repository::class),
    Interface_Cart_Module_ACL::class => DI\autowire(Cart_Module_ACL::class)
];
