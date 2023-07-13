<?php

namespace bpmj\wpidea\modules\opinions\core\client;

use bpmj\wpidea\sales\product\api\dto\Product_DTO_Collection;

interface Interface_Product_Api_Client
{
	public function find_products_user_has_or_had_access_to( int $user_id ): Product_DTO_Collection;
}