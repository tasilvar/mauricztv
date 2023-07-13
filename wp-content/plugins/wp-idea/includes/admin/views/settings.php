<?php
use bpmj\wpidea\admin\Menu;
?>
<div class="wrap eddcm-settings-wrap">
	<div class="eddcm-dashboard-head">
		<h2><span class="dashicons dashicons-admin-generic"></span><?php

            _e( 'Settings', BPMJ_EDDCM_DOMAIN ); ?></h2>
		<?php
		if ( ! get_option( 'bmpj_wpidea_vkey' ) && isset( $this ) && $this instanceof Menu ):
			?>
			<div class="error">
				<p><?php echo $this->get_first_time_message(); ?></p>
			</div>
			<?php
		endif;
		?>
	</div>

	<?php $settings->show_navigation(); ?>
	<?php $settings->show_forms(); ?>

</div>