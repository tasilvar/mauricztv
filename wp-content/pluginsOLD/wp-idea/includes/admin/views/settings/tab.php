<?php
use bpmj\wpidea\admin\settings\core\services\Settings_Api;

/* @var string $tab */
/* @var Settings_Api $settings_api */

        $groups = $settings_api->get_settings_group_by_name($tab);
        foreach ($groups->get_settings_collection() as $field) {
            echo $field->render_to_string();
        }
?>
<style>

    .wp-editor-area {
        max-width: 100% !important;
        height: auto !important;
        color: black !important;
    }

</style>