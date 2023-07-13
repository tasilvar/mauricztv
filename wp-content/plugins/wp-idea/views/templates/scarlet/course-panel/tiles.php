<?php
use \bpmj\wpidea\View;
?>
<?php if ( ! empty( $modules ) ) : ?>
	<div class="row panel_kursu_moduly">
		<?php foreach ( $modules as $id => $module ) : ?>
            <div class="col-sm-4">
                <div class="modul_lekcja<?php if ( $module->should_be_grayed_out() ) echo ' lek_niedostepna'; ?>">
                    <?php if ( $module->should_be_grayed_out() ): ?>
                        <div class="modul_lekcja_zdjecie">
                            <?php $drip = $module->get_calculated_drip();
                            if( !empty( $drip ) ) {

                            ?>
                            <div class="lekcja_niedostepna" data-drip="<?php echo $drip ?>">
                                <p><?php if( $module->is_lesson() ) { _e( 'The lesson will be available in', BPMJ_EDDCM_DOMAIN ); } else {
                                    _e( 'The module will be available in', BPMJ_EDDCM_DOMAIN ); } ?>:</p>
                                <div>
                                    <div class="lekcja_niedostepna_zegar"><i class="fas fa-clock"></i></div>
                                    <div class="lekcja_niedostepna_time">
                                        <div class="drip_time_d"><?php echo bpmj_eddcm_seconds_to_time( $drip, 'a' ) ?></div>
                                        <div><?php _e( 'days', BPMJ_EDDCM_DOMAIN ) ?></div>
                                    </div>
                                    <div class="lekcja_niedostepna_time">:</div>
                                    <div class="lekcja_niedostepna_time">
                                        <div class="drip_time_h"><?php echo bpmj_eddcm_seconds_to_time( $drip, 'h' ) ?></div>
                                        <div><?php _e( 'hr', BPMJ_EDDCM_DOMAIN ) ?></div>
                                    </div>
                                    <div class="lekcja_niedostepna_time">:</div>
                                    <div class="lekcja_niedostepna_time">
                                        <div class="drip_time_m"><?php echo bpmj_eddcm_seconds_to_time( $drip, 'i' ) ?></div>
                                        <div><?php _e( 'min', BPMJ_EDDCM_DOMAIN ) ?></div>
                                    </div>
                                    <div class="lekcja_niedostepna_time">:</div>
                                    <div class="lekcja_niedostepna_time">
                                        <div class="drip_time_s"><?php echo bpmj_eddcm_seconds_to_time( $drip, 's' ) ?></div>
                                        <div><?php _e( 'sec', BPMJ_EDDCM_DOMAIN ) ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <img src="<?php if ( $module->get_thumbnail() ) { echo $module->get_thumbnail(); } else { echo bpmj_eddcm_template_get_file( 'assets/img/box1.jpg' ); } ?>" />
                        </div>
                        <div class="modul_lekcja_tytul">
                            <?php echo $module->post_title; ?>
                        </div>
                        <?php if ( $module->get_subtitle() ) { ?>
                            <?php echo '<div class="modul_lekcja_opis">' . $module->get_subtitle() . '</div>'; ?><?php } ?>
                    <?php else: ?>
                        <a href="<?php echo get_permalink( $module->unwrap() ); ?>" class="modul_lekcja_link">
                            <div class="modul_lekcja_zdjecie">
                                <img src="<?php if ( $module->get_thumbnail() ) { echo $module->get_thumbnail(); } else { echo bpmj_eddcm_template_get_file( 'assets/img/box1.jpg' ); } ?>" />
                            </div>
                        </a>
                        <div class="modul_lekcja_tytul">
                            <a href="<?php echo get_permalink( $module->unwrap() ); ?>" class="modul_lekcja_link"><?php echo $module->post_title; ?></a>
                        </div>
                        <?php if ( $module->get_subtitle() ) { ?><?php echo '<div class="modul_lekcja_opis">' . $module->get_subtitle() . '</div>'; ?>
                            <?php } ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
	</div>

    <?php echo View::get('/course-panel/drip-counting-script'); ?>
<?php endif; ?>
