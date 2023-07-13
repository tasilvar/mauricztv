<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\gus_api\core\services;

interface Interface_Site_Info_Getter
{
    public function get_home_url(): string;
}