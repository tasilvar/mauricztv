<?php

use bpmj\wpidea\API_V2;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Packages_API_Static_Helper;

if ( !Packages_API_Static_Helper::has_access_to_feature(Packages::FEAT_API_V2)) { ?>
    <h3><span><?php _e('API Key', BPMJ_EDDCM_DOMAIN); ?></span></h3>

    <?= Packages_API_Static_Helper::render_no_access_to_feature_info(Packages::FEAT_API_V2) ?>

<?php } else { ?>
    <h3><span><?php _e('API Key', BPMJ_EDDCM_DOMAIN); ?></span></h3>

    <p><?php _e('API key v1 (Shoper, Woocommerce, Presta)', 'wp-idea'); ?></p>

    <p>
        <input type="text" name="api_key" class="regular-text" disabled value="<?php
        global $wpidea_settings;
        echo $wpidea_settings['license_key'];
        ?>">
    </p>
    <hr>

    <form method="post" action="<?php echo admin_url('admin.php?page=wp-idea-tools&tab_api_key'); ?>">
        <?php wp_nonce_field('regenerate_api_key_nonce'); ?>
        <div class="inside">
            <p><?php _e('API key v2 (Zapier and others)', 'wp-idea'); ?></p>

            <p>
                <input type="text" name="api_key" class="regular-text" disabled value="<?= API_V2::get_api_key() ?>">
                <input type="hidden" name="regenerate" value="1"/>
                <?php wp_nonce_field('regenerate_api_key_nonce', 'regenerate_api_key_nonce'); ?>
                <?php submit_button(__('Delete current and generate new', 'wp-idea'), 'secondary', 'submit', false); ?>

            </p>
            <p>
            </p>
        </div>
    </form>


<?php } ?>
