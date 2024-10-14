<?php
global $wp;

/** @var bool $show_only_my_courses */
/** @var bool $show_only_my_courses_is_checked */
?>

<div class="widok">
    <div class="contenter">
        <?php if ($show_only_my_courses) : ?>
            <p class="widok__checkbox-filter">
                <?php if ($show_only_my_courses_is_checked) : ?>
                    <a href="<?= home_url( $wp->request ); ?>/?show_my_courses=0"><i class="fa fa-check-square"></i><?php _e( 'My courses only', BPMJ_EDDCM_DOMAIN ); ?></a>
                <?php else : ?>
                    <a href="<?= home_url( $wp->request ); ?>/?show_my_courses=1"><i class="far fa-square"></i><?php _e( 'My courses only', BPMJ_EDDCM_DOMAIN ); ?></a>
                <?php endif; ?>
            </p>
        <?php endif; ?>
        <span class="view-mode">
            <p><?php _e( 'View mode', BPMJ_EDDCM_DOMAIN ) ?></p>
            <div id="kwadrat" class="opcje_widoku active"><i class="fa fa-th-large"></i></div>
            <div id="prostokat" class="opcje_widoku "><i class="fa fa-th"></i></div>
            <div id="lista" class="opcje_widoku"><i class="fa fa-th-list"></i></div>
        </span>
    </div>
</div>
