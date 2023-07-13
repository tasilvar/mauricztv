<?php

namespace bpmj\wpidea\modules\purchase_redirects\core\services;

class Site_Info_Getter
{
    public function get_logo_site_url(): string
    {
        return WPI()->templates->get_app_logo_url();
    }
}
