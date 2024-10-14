<?php

use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

/* @var Interface_View_Provider $view */
/* @var Interface_Translator $translator */

?>

<div class="user-account-app-view">
    <?= $view->get('../components/tabbed-view/tabbed-view', [
        'tabs' => [
            [
                'tab-name' => $translator->translate('user_account.account_settings'),
                'tab-info' => $translator->translate('user_account.account_settings.details'),
                'tab-content' => do_shortcode('[edd_profile_editor]'),
                'class' => 'account-settings',
                'tab-id' => 'account-settings',
                'disabled' => false
            ],
        ]
    ]) ?>
</div>

<style>
    .user-account-app-view .tabbed-view__tabs-menu {
        display: none;
    }
</style>
