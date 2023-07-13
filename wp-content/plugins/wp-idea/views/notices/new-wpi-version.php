<?php
/** @var string $new_version */
?>
<div id="wpi-new-version-notice" class="notice notice-error bpmj-wpi-notice notice-wpi-update-available">
    <h4 class="notice-wpi-update-available__header"><?= sprintf( __( 'A new version (%s) of WP Idea is available.', BPMJ_EDDCM_DOMAIN ), $new_version ) ?></h4>
    <p class="notice-wpi-update-available__text"><?= __( 'Update now to make your platform fully compatible with the latest version of Wordpress and to stay up to date with the latest security patches.', BPMJ_EDDCM_DOMAIN ) ?></p>

    <div class="notice-wpi-update-available__buttons">
    <a href="<?= wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=wp-idea/wp-idea.php' ), 'upgrade-plugin_wp-idea/wp-idea.php' ) ?>" class="wpi-button notice-wpi-update-available__button notice-wpi-update-available__button--update-now"><?= __( 'Update now', BPMJ_EDDCM_DOMAIN ); ?></a>
    <a href="<?= add_query_arg( [ 'dismiss-new-version-notice' => 1 ], admin_url() ) ?>" class="wpi-button notice-wpi-update-available__button notice-wpi-update-available__button--remind-me-later"><?= __( 'Remind me later', BPMJ_EDDCM_DOMAIN ); ?></a>
    </div>
</div>
