<?php
use bpmj\wpidea\admin\support\Support;
use bpmj\wpidea\wolverine\user\backend\User as BackendUser;

/** @var Support $support */

$current_user = BackendUser::getCurrent();
?>

<div class="support-rules">
    <h3 class="support__title"><?= $support->get_rules()->get_title() ?></h3>

    <p class="support-rules__info dashicons-before dashicons-warning"><?= $support->get_rules()->get_info() ?></p>

    <div class="support-rules__list">
        <?php foreach( $support->get_rules()->get_rules() as $rule ): ?>
        <div class="support-rules__item">
            <div class="dashicons dashicons-<?= $rule->icon ?> support-rules__item__icon"></div>
            <div class="support-rules__item__content">
                <h3><?= $rule->title ?></h3>
                
                <?php if( $rule->subtitle ): ?>
                    <p><?= $rule->subtitle ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="moving-dashes-wrapper">
            <div class="moving-dashes active-animatioon"></div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if( Support::current_user_has_active_support() ): ?>
        <div class="get-help">
            <?= __('Can\'t find an answer?', BPMJ_EDDCM_DOMAIN ) ?> 
            <a href="mailto:wsparcie@publigo.pl?subject=Zgłoszenie z platformy <?= $support->get_site_url() ?>" target="_blank" class="get-help__button get-help__button">
                <span class="dashicons dashicons-format-status"></span>    
                <?= __('Contact Us!', BPMJ_EDDCM_DOMAIN ) ?>
            </a>
        </div>

    <?php endif; ?>

</div>