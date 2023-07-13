<?php

/** @var \bpmj\wpidea\admin\tables\Enhanced_Table $table */
/** @var string $page_title */
/** @var \bpmj\wpidea\admin\helpers\html\Button $settings_button */
/** @var \bpmj\wpidea\admin\helpers\html\Link $color_settings_link */
?>
<div class="wrap group-templates-list-page">
    <h1 class="wp-heading-inline"><?= $page_title ?></h1>

    <hr class="wp-header-end">

    <div class="templates-list-page-top-buttons">
    <?= $color_settings_link->get_html() ?>
    <?= $settings_button->get_html() ?>
    </div>

    <?= $table->render() ?>
</div>