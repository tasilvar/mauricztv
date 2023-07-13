<?php

namespace bpmj\wpidea\commands;

use WP_CLI;
use WP_CLI_Command;
use bpmj\wpidea\admin\subscription\models\Software_Instance_Type;

class Instance_Cli extends WP_CLI_Command
{
    const COMMAND_NAME = 'wpidea instance';

    /**
     * @param $args
     * @param $assoc_args
     * @throws \WP_CLI\ExitException
     *
     */
    public function set_type( $args, $assoc_args ) {

        $type = ($assoc_args['type']) ?? null;

        $installation_type = new Software_Instance_Type();

        if($installation_type->set((int) $type)){
            WP_CLI::success( 'success' );
            return;
        }

        WP_CLI::error( 'error' );
    }

}
