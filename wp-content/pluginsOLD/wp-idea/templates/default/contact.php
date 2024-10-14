<?php WPI()->templates->header(); ?>

<!-- Sekcja pod paskiem z menu (treść + formularz) -->
<section class="content">
	<div class="wrapper">
                <h2 class="bg center"><?php the_title(); ?></h2>
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				the_content();
			}
		}
		?>
                <p class="e-mail"><a href="mailto:<?php echo WPI()->templates->get_contact_email(); ?>"><?php echo WPI()->templates->get_contact_email(); ?></a></p>

                <!-- Formularz -->
                <div class="form-wrapper">
			<?php
			if ( 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {
				if ( is_email( $_POST[ 'email' ] ) ) {
					$headers = array( 'Content-Type: text/html; charset=UTF-8' );

					$message = __( 'New message from WP Idea', BPMJ_EDDCM_DOMAIN ) . '<br><br>';
					$message .= sprintf( __( 'Name: %s<br>', BPMJ_EDDCM_DOMAIN ), sanitize_text_field( $_POST[ 'author' ] ) );
					$message .= sprintf( __( 'E-mail: %s<br><br>', BPMJ_EDDCM_DOMAIN ), sanitize_email( $_POST[ 'email' ] ) );
					$message .= sprintf( __( 'Message: <br>%s', BPMJ_EDDCM_DOMAIN ), nl2br( esc_textarea( $_POST[ 'message' ] ) ) );

					wp_mail( WPI()->templates->get_contact_email(), __( 'New message from WP Idea', BPMJ_EDDCM_DOMAIN ), $message, $headers );
					_e( 'Your email has been sent', BPMJ_EDDCM_DOMAIN );
				}
			}
			?>
			<form action="" method="post">
				<input type="text" name="author" placeholder="<?php _e( 'Name', BPMJ_EDDCM_DOMAIN ); ?> *" required>
				<input type="email" name="email" placeholder="E-mail *" required>
				<textarea name="message" placeholder="<?php _e( 'Message', BPMJ_EDDCM_DOMAIN ); ?> *"></textarea>
				<button><?php _e( 'Send', BPMJ_EDDCM_DOMAIN ); ?></button>
			</form>
			<p>* <?php _e( 'required fields', BPMJ_EDDCM_DOMAIN ); ?></p>
                </div>
                <!-- Koniec formularza -->
	</div>
</section>
<!-- Koniec sekcji pod paskiem z menu -->

<?php WPI()->templates->footer(); ?>