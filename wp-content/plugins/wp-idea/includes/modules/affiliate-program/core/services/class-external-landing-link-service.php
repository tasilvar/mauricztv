<?php

namespace bpmj\wpidea\modules\affiliate_program\core\services;

use bpmj\wpidea\data_types\exceptions\Invalid_Url_Exception;
use bpmj\wpidea\modules\affiliate_program\core\entities\External_Landing_Link;
use bpmj\wpidea\modules\affiliate_program\core\exceptions\External_Landing_Link_Not_Found_Exception;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_External_Landing_Link_Repository;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\{External_Link_ID, External_Url, Product_ID};
use OutOfBoundsException;
use bpmj\wpidea\modules\affiliate_program\core\entities\External_Landing_Link_Collection;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\External_Landing_Link_Query_Criteria;

class External_Landing_Link_Service implements Interface_External_Landing_Link_Service
{
    private Interface_External_Landing_Link_Repository $external_landing_link_repository;

    public function __construct(
        Interface_External_Landing_Link_Repository $external_landing_link_repository
    ) {
        $this->external_landing_link_repository = $external_landing_link_repository;
    }

    /**
     * @throws OutOfBoundsException
     */

    public function add(Product_ID $product_id, External_Url $url): void
    {
        $external_landing_link = new External_Landing_Link(
            null,
            $product_id,
            $url
        );

        $this->external_landing_link_repository->add($external_landing_link);
    }

    public function find_all(): External_Landing_Link_Collection
    {
        return $this->external_landing_link_repository->find_by_criteria(
            new External_Landing_Link_Query_Criteria()
        );
    }

    public function find_by_id(External_Link_ID $id): ?External_Landing_Link
    {
        $link = $this->external_landing_link_repository->find_by_id($id);

        if (!$link) {
            return null;
        }

        return $link;
    }

    public function find_first_with_matching_url(string $landing_url): ?External_Landing_Link
    {
        $criteria = new External_Landing_Link_Query_Criteria();
        $criteria->url = $landing_url;

        $external_landing_link_collection = $this->external_landing_link_repository->find_by_criteria($criteria);

        return $external_landing_link_collection->current();
    }

    /**
     * @throws External_Landing_Link_Not_Found_Exception
     */

    public function update(External_Link_ID $id, Product_ID $product_id, External_Url $url): void
    {
        $external_landing_link = $this->external_landing_link_repository->find_by_id($id);

        if (!$external_landing_link) {
            throw new External_Landing_Link_Not_Found_Exception();
        }

        $external_landing_link->change_product_id($product_id);
        $external_landing_link->change_url($url);

        $this->external_landing_link_repository->update($external_landing_link);
    }

    public function delete(External_Link_ID $id): void
    {
        $this->external_landing_link_repository->delete($id);
    }
}
