<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\cart\infrastructure\filters;

use bpmj\wpidea\modules\cart\core\filters\external\handlers\Ajax_Discount_Response_Handler;

class Filter_Handlers_Initiator
{
    public function __construct(
        Ajax_Discount_Response_Handler $ajax_discount_response_handler
    ) {
        $ajax_discount_response_handler->init();
    }
}