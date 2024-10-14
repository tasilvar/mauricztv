<form method="post" action="<?php echo admin_url('admin.php?page=wp-idea-tools&tab_banned_emails'); ?>">
    <?php wp_nonce_field('edd_banned_emails_nonce'); ?>
    <h3><span><?php _e( 'Banned emails', BPMJ_EDDCM_DOMAIN); ?></span></h3>
    <div class="inside">
        <p><?php _e( 'Emails placed in the box below will not be allowed to make purchases. To ban an entire domain, enter the domain starting with "@".', 'easy-digital-downloads' ); ?></p>
        <form method="post" action="<?php echo admin_url( 'edit.php?post_type=download&page=edd-tools&tab=general' ); ?>">
            <p>
                <textarea name="banned_emails" rows="10" class="large-text"><?php echo implode( "\n", edd_get_banned_emails() ); ?></textarea>
                <span class="description"><?php _e( 'Enter emails and/or domains (starting with @) to disallow, one per line.', BPMJ_EDDCM_DOMAIN ); ?></span>
            </p>
            <p>
                <input type="hidden" name="edd_action" value="save_banned_emails" />
                <?php wp_nonce_field( 'edd_banned_emails_nonce', 'edd_banned_emails_nonce' ); ?>
                <?php submit_button( __( 'Save', 'easy-digital-downloads' ), 'secondary', 'submit', false ); ?>
            </p>
        </form>
    </div><!-- .inside -->
</form>

