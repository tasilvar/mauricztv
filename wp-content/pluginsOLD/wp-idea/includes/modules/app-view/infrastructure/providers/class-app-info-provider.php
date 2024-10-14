<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\app_view\infrastructure\providers;

use bpmj\wpidea\environment\Interface_Site;
use bpmj\wpidea\modules\app_view\core\providers\Interface_App_Info_Provider;

class App_Info_Provider implements Interface_App_Info_Provider
{

    private Interface_Site $site;

    public function __construct(
        Interface_Site $site
    )
    {
        $this->site = $site;
    }

    public function get_app_name(): string
    {
        return $this->site->get_name();
    }
}