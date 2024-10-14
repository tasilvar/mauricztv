<?php
use bpmj\wpidea\Software_Variant;
?>

<div class="wrap">
	<h2></h2>

	<div class="edd-courses-manager">

		<div class="row">
			<div class="heading animated fadeInDown">
				<?php _e( Software_Variant::get_name(), BPMJ_EDDCM_DOMAIN ); ?>
				<span class="settings-page"><?php _e( 'Diagnostic', BPMJ_EDDCM_DOMAIN ); ?></span>
			</div>
		</div>

		<section class="edd-courses-manager-diagnostic">
			<div class="row">

				<div class="full-column">
					<div class="panel required-plugins animated fadeInUp">
						<div class="panel-heading">
							<?php _e( 'Required Plugins', BPMJ_EDDCM_DOMAIN ); ?>
						</div>
						<div class="panel-body no-padding">
							<?php
							/**
							 * Required Plugins
							 */
							$plugins = WPI()->diagnostic->get_required_plugins();
							if ( is_array( $plugins ) ):
								foreach ( $plugins as $slug => $plugin ):

									// All plugins without Payment Gates
									if ( $slug != 'payment_gates' ) {
										$class		 = $plugin[ 'enabled' ] ? 'enabled' : 'disabled';
										$dashicon	 = $plugin[ 'enabled' ] ? 'yes' : 'no';

										echo '<div class="item ' . $class . ' animated fadeInUp">';
										echo '<div class="icon"><span class="dashicons dashicons-' . $dashicon . '"></span></div>';
										echo '<div class="content">' . $plugin[ 'name' ];

										if ( !$plugin[ 'enabled' ] ) {
											echo '<span class="warning">';

											if ( isset( $plugin[ 'install-url' ] ) ) {
												printf( __( 'Please <a target="_blank" href="%s">install</a> or <a href="%s">activate</a> this plugin!', BPMJ_EDDCM_DOMAIN ), $plugin[ 'install-url' ], admin_url( 'plugins.php' ) );
											} else {
												printf( __( 'Please install or <a href="%s">activate</a> this plugin!', BPMJ_EDDCM_DOMAIN ), admin_url( 'plugins.php' ) );
											}

											echo '</span>';
										}

										echo '</div></div>';

										// Payment Gates [must be activated at least one]
									} else {

										echo '<div class="item payment-gates">';
										echo '<div class="subheading animated fadeInLeft">' . __( 'Payment Gates', BPMJ_EDDCM_DOMAIN ) . ' <span class="tooltip"><div class="tooltiptext">' . __( 'At least one is required', BPMJ_EDDCM_DOMAIN ) . '</div></span></div>';

										foreach ( $plugin as $gate ) {
											$class		 = $gate[ 'enabled' ] ? 'enabled' : 'disabled';
											$dashicon	 = $gate[ 'enabled' ] ? 'yes' : 'no';

											echo '<div class="subitem ' . $class . ' animated fadeInUp">';
											echo '<div class="icon"><span class="dashicons dashicons-' . $dashicon . '"></span></div>';
											echo '<div class="content">' . $gate[ 'name' ] . '</div>';
											echo '</div>';
										}

										echo '</div>';
									}
								endforeach;
							endif;
							?>
						</div>
					</div>
				</div>

			</div>
		</section>

	</div>
</div>