<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\meta_conversion_api\core\services;

use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\sales\order\api\dto\Order_DTO;
use bpmj\wpidea\sales\product\api\dto\Product_DTO;

interface Interface_Meta_Conversion_API_Sender
{
    public function send_data_for_page_view_event(): void;

    public function send_data_for_initiate_checkout_event(Cart_API $cart_api): void;

    public function send_data_for_add_to_cart_event(Product_DTO $product): void;

    public function send_data_for_purchase_event(Order_DTO $order): void;
}