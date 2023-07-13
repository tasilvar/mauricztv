<?php

namespace bpmj\wpidea\modules\search_engine;

use bpmj\wpidea\modules\search_engine\api\Search_Engine_API;
use bpmj\wpidea\modules\search_engine\api\Search_Engine_API_Static_Helper;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;

class Search_Engine_Module implements Interface_Module
{
    private Search_Engine_API $search_engine_api;

    public function __construct(Search_Engine_API $search_engine_api)
    {
        $this->search_engine_api = $search_engine_api;
    }

    public function init(): void
    {
        Search_Engine_API_Static_Helper::init($this->search_engine_api);
    }

    public function get_routes(): array
    {
        return [];
    }

    public function get_translations(): array
    {
        return [];
    }
}
