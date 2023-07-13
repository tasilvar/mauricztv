<?php

namespace bpmj\wpidea\modules\search_engine\api;

use bpmj\wpidea\modules\search_engine\core\services\Interface_Search_Engine;
use bpmj\wpidea\modules\search_engine\core\value_objects\Search_Results_Collection;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module_API;

class Search_Engine_API
{
    private Interface_Search_Engine $search_engine;

    public function __construct(Interface_Search_Engine $search_engine)
    {
        $this->search_engine = $search_engine;
    }

    public function search(string $query, int $user_id = null): Search_Results_Collection
    {
        return $this->search_engine->search($query, $user_id);
    }
}