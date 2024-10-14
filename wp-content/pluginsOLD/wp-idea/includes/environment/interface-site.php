<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\environment;

interface Interface_Site
{
    public function get_base_url(): string;
    public function get_admin_url(): string;
    public function get_ajax_url(): string;
    public function get_name(): string;
}
