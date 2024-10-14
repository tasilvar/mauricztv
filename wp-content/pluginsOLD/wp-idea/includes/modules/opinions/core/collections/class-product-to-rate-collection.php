<?php

namespace bpmj\wpidea\modules\opinions\core\collections;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;
use bpmj\wpidea\modules\opinions\core\value_objects\Product_To_Rate;

class Product_To_Rate_Collection extends Abstract_Iterator
{
	public function add(Product_To_Rate $item): self
	{
		return $this->add_item($item);
	}

	public function current(): Product_To_Rate
	{
		return $this->get_current_item();
	}
}