<?php

namespace bpmj\wpidea\modules\search_engine\core\services;

use bpmj\wpidea\modules\search_engine\core\value_objects\Search_Results_Collection;

interface Interface_Search_Engine
{
    public function search(string $query, ?int $user_id = null): Search_Results_Collection;
}
