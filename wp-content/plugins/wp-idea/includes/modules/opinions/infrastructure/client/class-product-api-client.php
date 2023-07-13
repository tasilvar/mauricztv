<?php

namespace bpmj\wpidea\modules\opinions\infrastructure\client;

use bpmj\wpidea\modules\opinions\core\client\Interface_Product_Api_Client;
use bpmj\wpidea\sales\product\api\dto\Product_DTO_Collection;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\user\User_ID;

class Product_Api_Client implements Interface_Product_Api_Client
{
	private Interface_Product_API $product_API;

	public function __construct(
		Interface_Product_API $product_API
	)
	{
		$this->product_API = $product_API;
	}

	public function find_products_user_has_or_had_access_to( int $user_id ): Product_DTO_Collection
	{
		return $this->product_API->get_products_user_has_or_had_access_to(
			new User_ID($user_id)
		);
	}
}