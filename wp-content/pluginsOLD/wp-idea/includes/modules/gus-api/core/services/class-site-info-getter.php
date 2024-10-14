<?php

namespace bpmj\wpidea\modules\gus_api\core\services;

class Site_Info_Getter implements Interface_Site_Info_Getter
{
    public function get_home_url(): string
    {
        return get_home_url();
    }
}
