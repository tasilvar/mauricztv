<?php

namespace bpmj\wpidea\commands;

use bpmj\wpidea\assets\Assets;
use WP_CLI;
use WP_CLI_Command;

class Minify_Cli extends WP_CLI_Command
{
    const COMMAND_NAME = 'wpidea minify';

    public function current( $args, $assoc_args ) {
        $this->regenerate_assets();

        $this->minify_assets();

        WP_CLI::success( 'Assets regenerated and minified successfully!' );
    }

    private function regenerate_assets(): void
    {
        $assets = new Assets(BPMJ_EDDCM_TEMPLATES_DIR . WPI()->templates->get_current_template());
        $assets->regenerate();

        WP_CLI::log( 'Assets regenerated!' );
    }

    private function minify_assets(): void
    {
        WPI()->templates->minify_css();
        WPI()->templates->minify_js();

        WP_CLI::log( 'Assets minified!' );
    }

}
