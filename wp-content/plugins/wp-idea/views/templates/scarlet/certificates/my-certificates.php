<?php
use bpmj\wpidea\translator\Interface_Translator;

/* @var Interface_Translator $translator */

use bpmj\wpidea\Info_Message;

$query = new WP_Query([
   'post_type' => 'certificates',
   'meta_query' => [
       [
           'key' => 'user_id',
           'value' => get_current_user_id(),
       ],
   ],
]);

if ( ! $query->have_posts() ) : ?>
    <?php
    $message = new Info_Message( __( 'You do not have any certificate yet.', BPMJ_EDDCM_DOMAIN ) );
    $message->render();
    ?>
<?php else : ?>

	<h2>Certyfikaty do szkoleń generują się automatycznie po 100% ukończenia kursu.</h2>

    <table class="my-certificates-table">
        <thead>
        <tr>
            <th>AA<?= $translator->translate('user_account.my_certificates.product_name') ?></th>
            <th><?= $translator->translate('user_account.my_certificates.download_certificate') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php while ( $query->have_posts() ) : ?>
            <?php $query->the_post(); ?>
            <tr>
                <td>
                    <?= get_the_title() ?>
                </td>
                <td>
                    <a target='_blank' href='<?= WPI()->certificates->get_download_url_for_current_user(
                        get_the_ID()
                    ) ?>'><?= $translator->translate('user_account.my_certificates.download') ?></a>
                </td>
            </tr>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
        </tbody>
    </table>
<?php endif; ?>