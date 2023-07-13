<?php

namespace bpmj\wpidea\modules\opinions\api;

use bpmj\wpidea\data_types\exceptions\Object_Uninitialized_Exception;
use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\opinions\core\client\Interface_Product_Api_Client;
use bpmj\wpidea\modules\opinions\core\collections\Opinion_Collection;
use bpmj\wpidea\modules\opinions\core\collections\Product_To_Rate_Collection;
use bpmj\wpidea\modules\opinions\core\entities\Opinion;
use bpmj\wpidea\modules\opinions\core\providers\Interface_Opinions_Config_Provider;
use bpmj\wpidea\modules\opinions\core\repositories\Interface_Opinion_Repository;
use bpmj\wpidea\modules\opinions\core\services\Opinions_Service;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Status;
use bpmj\wpidea\modules\opinions\core\value_objects\Product_To_Rate;
use bpmj\wpidea\modules\opinions\infrastructure\persistence\Opinions_Query_Criteria;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\user\User_ID;

class Opinions_API
{
    private static ?Opinions_API $instance = null;
    private Interface_Opinion_Repository $opinion_repository;
    private Interface_Opinions_Config_Provider $opinions_config_provider;
	private Interface_Product_Api_Client $product_api_client;
    private Opinions_Service $opinions_service;

	public function __construct(
        Interface_Opinion_Repository $opinion_repository,
        Interface_Opinions_Config_Provider $opinions_config_provider,
	    Interface_Product_Api_Client $product_api_client,
        Opinions_Service $opinions_service
    ) {
        $this->opinion_repository = $opinion_repository;
        $this->opinions_config_provider = $opinions_config_provider;
		$this->product_api_client = $product_api_client;
        $this->opinions_service = $opinions_service;

		self::$instance = $this;
	}

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            throw new Object_Uninitialized_Exception();
        }

        return self::$instance;
    }

    public function get_opinions_by_product_id(int $product_id, int $per_page = 0, int $page = 1): Opinion_Collection
    {
        $criteria = new Opinions_Query_Criteria();

        $criteria->set_product_id_in([$product_id]);
        $criteria->set_statuses([$this->get_accepted_status()]);

        $sort_by = (new Sort_By_Clause())->sort_by('date_of_issue', true);

        return $this->opinion_repository->find_by_criteria($criteria, $per_page, $page, $sort_by);
    }

    public function get_count_opinions_by_product_id(int $product_id): int
    {
        $criteria = new Opinions_Query_Criteria();

        $criteria->set_product_id_in([$product_id]);
        $criteria->set_statuses([$this->get_accepted_status()]);

        return $this->opinion_repository->count_by_criteria($criteria);
    }

    public function is_enabled(): bool
    {
      return $this->opinions_config_provider->is_enabled();
    }

	public function get_products_user_can_rate(int $user_id): Product_To_Rate_Collection
	{
		$products_user_has_or_had_access_to = $this->product_api_client->find_products_user_has_or_had_access_to($user_id);
		$products_available_to_rate = Product_To_Rate_Collection::create();
		$rated_product_ids = $this->get_rated_product_ids($user_id);

		foreach ($products_user_has_or_had_access_to as $product) {
			if( in_array( $product->get_id(), $rated_product_ids, true ) ) {
				continue;
			}

			$products_available_to_rate->add(
				new Product_To_Rate(
					$product->get_id(),
					$product->get_name()
				)
			);
		}

		return $products_available_to_rate;
	}

    public function create(Opinion $opinion): void
    {
        $this->opinions_service->create($opinion);
    }

    public function change_status(ID $id, Opinion_Status $new_status): void
    {
        $this->opinions_service->change_status($id, $new_status);
    }

    public function product_is_already_rated_by_user(User_ID $user_id, Product_ID $product_id): bool
    {
        return $this->opinions_service->product_is_already_rated_by_user($user_id, $product_id);
    }

    private function get_accepted_status(): string
    {
        return (new Opinion_Status(Opinion_Status::ACCEPTED))->get_value();
    }

	private function get_rated_product_ids(int $user_id): array
	{
		$user_opinions = $this->get_opinions_by_user_id($user_id);
		$ids = [];

		foreach ($user_opinions as $opinion) {
			$ids[] = $opinion->get_product_id()->to_int();
		}

		return $ids;
	}

	private function get_opinions_by_user_id(int $user_id): Opinion_Collection
	{
		$criteria = new Opinions_Query_Criteria();

		$criteria->set_user_id($user_id);

		return $this->opinion_repository->find_by_criteria($criteria);
	}

    public function count_waiting_opinions(): int
    {
        $criteria = new Opinions_Query_Criteria();
        $criteria->set_statuses([$this->get_waiting_status()]);

        return $this->opinion_repository->count_by_criteria($criteria);
    }

    private function get_waiting_status(): string
    {
        return (new Opinion_Status(Opinion_Status::WAITING))->get_value();
    }
}