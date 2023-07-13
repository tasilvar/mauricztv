<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\meta_conversion_api\core\services;

interface Interface_Page_Info_Getter
{
    public function get_current_page_url(): string;

    public function get_page_title(): string;
}