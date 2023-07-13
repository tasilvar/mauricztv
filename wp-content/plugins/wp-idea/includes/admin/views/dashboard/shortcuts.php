<?php

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;

/** @var bool $courses_functionality_enabled */

?>

<div class="wpi-box wpi-shortcuts">
    <div class="wpi-box__head">
        <span class="wpi-box__title"><?= __('Useful WP Idea shortcuts', BPMJ_EDDCM_DOMAIN) ?></span>
    </div>

    <div class="wpi-box__content">
        <h3 class="wpi-box__subhead"><?= __('Management', BPMJ_EDDCM_DOMAIN) ?></h3>

        <div class="wpi-shortcuts__row">
            <?php if($courses_functionality_enabled){ ?>
            <a href="<?= admin_url('admin.php?page=wp-idea') ?>" class="wpi-shortcut-box">
                <span class="dashicons dashicons-welcome-learn-more"></span>
                <span class="wpi-shortcut-box__title"><?= __('Courses', BPMJ_EDDCM_DOMAIN) ?></span>
            </a>
            <a href="<?= admin_url('admin.php?page=' . Admin_Menu_Item_Slug::STUDENTS) ?>" class="wpi-shortcut-box">
                <span class="dashicons dashicons-admin-users"></span>
                <span class="wpi-shortcut-box__title"><?= __('Participants', BPMJ_EDDCM_DOMAIN) ?></span>
            </a>
           <?php } ?>
            <a href="<?= admin_url('admin.php?page=' . Admin_Menu_Item_Slug::PAYMENTS_HISTORY) ?>" class="wpi-shortcut-box">
                <span class="dashicons dashicons-admin-network"></span>
                <span class="wpi-shortcut-box__title"><?= __('Payments', BPMJ_EDDCM_DOMAIN) ?></span>
            </a>
        </div>

        <h3 class="wpi-box__subhead"><?= __('Useful links', BPMJ_EDDCM_DOMAIN) ?></h3>

        <div class="wpi-shortcuts__row">
            <a href="<?= admin_url('admin.php?page=wp-idea-settings') ?>" class="wpi-shortcut-box">
                <span class="dashicons dashicons-admin-generic"></span>
                <span class="wpi-shortcut-box__title"><?= __('Settings', BPMJ_EDDCM_DOMAIN) ?></span>
            </a>
            <a href="<?= admin_url('customize.php?autofocus[section]=bpmj_eddcm_colors_settings') ?>" class="wpi-shortcut-box">
                <span class="dashicons dashicons-admin-customizer"></span>
                <span class="wpi-shortcut-box__title"><?= __('Colors settings', BPMJ_EDDCM_DOMAIN) ?></span>
            </a>
            <a href="<?= admin_url('admin.php?page=wp-idea-support') ?>" class="wpi-shortcut-box">
                <span class="dashicons dashicons-format-status"></span>
                <span class="wpi-shortcut-box__title"><?= __('Support', BPMJ_EDDCM_DOMAIN) ?></span>
            </a>
        </div>
    </div>
</div>