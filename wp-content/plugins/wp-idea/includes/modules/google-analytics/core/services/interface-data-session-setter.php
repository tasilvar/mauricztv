<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\google_analytics\core\services;

use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\modules\google_analytics\core\entities\Event;
use bpmj\wpidea\sales\order\api\dto\Order_DTO;
use bpmj\wpidea\sales\product\api\dto\Product_DTO;

interface Interface_Data_Session_Setter
{
    public function add_event_to_session(Event $event): void;
}