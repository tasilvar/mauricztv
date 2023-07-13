<?php

namespace bpmj\wpidea\modules\search_engine\api;

use bpmj\wpidea\modules\search_engine\core\value_objects\Search_Results_Collection;

class Search_Engine_API_Static_Helper
{
    private static Search_Engine_API $search_engine_api;

    public static function init(Search_Engine_API $search_engine_api): void
    {
        self::$search_engine_api = $search_engine_api;
    }

    public static function search(string $query, int $user_id = null): Search_Results_Collection
    {
        return self::$search_engine_api->search($query, $user_id);
    }
}