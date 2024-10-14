<?php

namespace bpmj\wpidea\commands;

use WP_CLI;

class WPIdea_Commands
{
    const COMMANDS_PATH = BPMJ_EDDCM_DIR . 'includes/commands';

    public function __construct() {
        if(!defined( 'WP_CLI' ) || !WP_CLI) return;

        $this->include_commands_files();
        $this->register_commands();
    }

    protected function include_commands_files()
    {
        require_once self::COMMANDS_PATH . '/class-license-cli.php';
        require_once self::COMMANDS_PATH . '/class-assistant-cli.php';
        require_once self::COMMANDS_PATH . '/class-minify-cli.php';
        require_once self::COMMANDS_PATH . '/class-instance-cli.php';
    }

    protected function register_commands()
    {
        WP_CLI::add_command(Assistant_CLI::COMMAND_NAME, Assistant_CLI::class);
        WP_CLI::add_command(License_Cli::COMMAND_NAME, License_Cli::class);
        WP_CLI::add_command(Minify_Cli::COMMAND_NAME, Minify_Cli::class);
        WP_CLI::add_command(Instance_Cli::COMMAND_NAME, Instance_Cli::class);
    }
}
