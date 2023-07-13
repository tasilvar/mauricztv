<?php

namespace bpmj\wpidea\commands;

use WP_CLI;
use WP_CLI_Command;

class Assistant_CLI extends WP_CLI_Command
{
    const COMMAND_NAME = 'wpidea assistant';

    public function enable()
    {
        WP_CLI::line(__('WP Idea Assistant is no longer supported.', BPMJ_EDDCM_DOMAIN));
    }
}
