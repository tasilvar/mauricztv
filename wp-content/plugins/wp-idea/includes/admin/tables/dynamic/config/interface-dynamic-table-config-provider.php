<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\config;

interface Interface_Dynamic_Table_Config_Provider
{
    public function get_config(): Dynamic_Table_Config;

    public function get_table_id(): string;
}