<?php

use bpmj\wpidea\Info_Message;

WPI()->templates->header(); ?>

    <!-- Sekcja pod paskiem z menu -->
    <section class="<?php echo apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?>">
        <div class="wrapper">
            <?php the_title( '<h2 class="bg center">', '</h2>' ); ?>
            <?php
            while ( have_posts() ) {
                the_post();
                the_content();
            }

            $query = new WP_Query( array(
                'post_type' => 'certificates',
                'meta_query' => array(
                    array(
                        'key' => 'user_id',
                        'value' => get_current_user_id(),
                    ),
                ),
            ) );

            if ( ! $query->have_posts() ) : ?>
                <?php
                    $message = new Info_Message( __( 'You do not have any certificate yet.', BPMJ_EDDCM_DOMAIN ) );
                    $message->render();
                ?>
            <?php else : ?>
                <table>
                    <thead>
                    <tr>
                        <th><?php _e( 'Course', BPMJ_EDDCM_DOMAIN); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ( $query->have_posts() ) : ?>
                        <?php $query->the_post(); ?>
                        <tr>
                            <td>
                                <a target="_blank" href="<?= WPI()->certificates->get_download_url_for_current_user(get_the_ID()); ?>"><?= get_the_title(); ?></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>
    <!-- Koniec sekcji pod paskiem z menu -->

<?php WPI()->templates->footer(); ?>
