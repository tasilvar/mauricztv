<?php
use bpmj\wpidea\Info_Message;

WPI()->templates->header(); ?>

    <!-- Sekcja pod paskiem z menu -->
    <div id="content">
        <div class="contenter">
            <?php
            the_title( '<h1>', '</h1>' );
            WPI()->templates->breadcrumbs();

            while (have_posts()) {
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
            if ( is_user_logged_in() ) :
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
            <?php else:
                echo '<p>' . __( 'You need to login to see certificates.', BPMJ_EDDCM_DOMAIN ) . '</p>';
                echo edd_login_form();
            endif; ?>
        </div>
        <!-- Koniec sekcji pod paskiem z menu -->

    </div>

<?php WPI()->templates->footer(); ?>
