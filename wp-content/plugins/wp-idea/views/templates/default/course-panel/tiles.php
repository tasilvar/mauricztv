<?php
/** @var array $modules */
?>

<?php if ( ! empty( $modules ) ) : ?>
    <section class="modules bg">
        <div class="wrapper">
            <h2 class="bg center"><?php _e( 'Course Panel', BPMJ_EDDCM_DOMAIN ); ?></h2>
            <?php

            foreach ( $modules as $id => $module ) {
                ?>
                <div class="box-wrapper">
                    <div class="box">
                        <?php if ( $module->should_be_grayed_out() ): ?>
                            <div class="thumb note blurred"
                                 <?php if ( $module->get_thumbnail() ) { ?>style="background-image: url(<?php echo $module->get_thumbnail(); ?>); background-repeat: no-repeat; background-size: cover; background-position: center center;" <?php } ?>></div>
                            <h3>
                                <?php echo $module->post_title; ?>
                            </h3>
                            <?php if ( $module->get_subtitle() ) { ?>
                                <p><?php echo $module->get_subtitle(); ?></p><?php } ?>
                        <?php else: ?>
                            <a href="<?php echo get_permalink( $module->unwrap() ); ?>">
                                <div class="thumb note"
                                     <?php if ( $module->get_thumbnail() ) { ?>style="background-image: url(<?php echo $module->get_thumbnail(); ?>); background-repeat: no-repeat; background-size: cover; background-position: center center;" <?php } ?>></div>
                            </a>
                            <h3>
                                <a href="<?php echo get_permalink( $module->unwrap() ); ?>"><?php echo $module->post_title; ?></a>
                            </h3>
                            <?php if ( $module->get_subtitle() ) { ?><p><?php echo $module->get_subtitle(); ?> <a
                                    href="<?php echo $module->get_permalink(); ?>"><span
                                        class="arrow">&rsaquo;</span></a>
                                </p><?php } ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </section>
<?php endif; ?>
