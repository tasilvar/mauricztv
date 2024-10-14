<?php

namespace bpmj\wpidea\admin\tables\dynamic;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;

interface Interface_Dynamic_Table_Factory
{
    public function create(Dynamic_Table_Config $config): Interface_Dynamic_Table;
}