<?php

namespace bpmj\wpidea\modules\opinions\core\value_objects;

class Product_To_Rate
{

	private int $product_id;
	private string $product_name;

	public function __construct(
		int $product_id,
		string $product_name
	)
	{
		$this->product_id = $product_id;
		$this->product_name = $product_name;
	}

	public function get_product_id(): int
	{
		return $this->product_id;
	}

	public function get_product_name(): string
	{
		return $this->product_name;
	}
}