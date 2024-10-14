<?php

namespace bpmj\wpidea\modules\opinions\core\services;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\modules\opinions\core\entities\Opinion;
use bpmj\wpidea\modules\opinions\core\repositories\Interface_Opinion_Repository;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Status;
use bpmj\wpidea\modules\opinions\infrastructure\persistence\Opinions_Query_Criteria;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\user\User_ID;

class Opinions_Service
{
    private Interface_Opinion_Repository $opinion_repository;

    public function __construct(
        Interface_Opinion_Repository $opinion_repository
    )
    {
        $this->opinion_repository = $opinion_repository;
    }

    public function create(Opinion $opinion): void
    {
        $this->opinion_repository->create($opinion);
    }

    public function change_status(ID $id, Opinion_Status $new_status): void
    {
        $opinion = $this->opinion_repository->find_by_id($id);

        if (!$opinion) {
            return;
        }

        $opinion->change_status($new_status);

        $this->opinion_repository->update($opinion);
    }

    public function product_is_already_rated_by_user(User_ID $user_id, Product_ID $product_id): bool
    {
        $criteria = new Opinions_Query_Criteria();
        $criteria->set_user_id($user_id->to_int());
        $criteria->set_product_id_in([$product_id->to_int()]);

        return $this->opinion_repository->count_by_criteria($criteria) > 0;
    }
}