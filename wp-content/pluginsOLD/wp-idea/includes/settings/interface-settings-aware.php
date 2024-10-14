<?php
/**
 * This file is licensed under proprietary license
 */

namespace bpmj\wpidea\settings;

interface Interface_Settings_Aware
{
    public function set_settings(Interface_Settings $settings): void;
}

