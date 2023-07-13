<?php

namespace bpmj\wpidea\commands;

use WP_CLI;
use WP_CLI_Command;

class License_Cli extends WP_CLI_Command
{
    const COMMAND_NAME = 'wpidea license';

    /**
     * @param $args
     * @param $assoc_args
     * @throws \WP_CLI\ExitException
     *
     */
    public function change_license_key($args, $assoc_args)
    {

        $key = ($assoc_args['key']) ?? null;

        if ($key && WPI()->trial->update_key_and_disable_trial($key)) {
            WP_CLI::line('WpIdea license key changed');
            return;
        }

        WP_CLI::error('ERROR Key not found!');
    }

    public function change_trial_version_expiration_date($args, $assoc_args)
    {
        $date = ($assoc_args['date']) ?? null;

        if ($date && WPI()->trial->change_trial_version_expiration_date($date)) {
            WP_CLI::line('WpIdea trial expiration date changed');
            return;
        }

        WP_CLI::error('ERROR date not found!');
    }

    public function disable_trial()
    {
        if (WPI()->trial->change_trial_version_expiration_date('')) {
            WP_CLI::line('trial disabled');
            return;
        }

        WP_CLI::error('ERROR date not found!');
    }

}
