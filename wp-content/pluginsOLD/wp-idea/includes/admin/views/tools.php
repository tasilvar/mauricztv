<?php
use bpmj\wpidea\admin\Menu;
?>
<div class="wrap eddcm-settings-wrap">

	<div class="eddcm-dashboard-head">
		<h2><span class="dashicons dashicons-admin-tools"></span><?php

            _e( 'Tools', BPMJ_EDDCM_DOMAIN ); ?></h2>
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

    <h2 class="nav-tab-wrapper wp_idea-nav-tab-wrapper">
        <?php foreach ( WPI()->tools->get_sections() as $key => $section ) : ?>
            <a href="#wp-idea-<?php echo $section['id']; ?>-tab" class="nav-tab <?php echo ( $key === 0 ) ? 'nav-tab-active' : ''; ?>" id="wp-idea-<?= $section['id'] ?>-tab-link"><?php echo $section['title']; ?></a>
        <?php endforeach; ?>
    </h2>

    <?php foreach ( WPI()->tools->get_sections() as $key => $section ) : ?>
        <div id="wp-idea-<?php echo $section['id']; ?>-tab" class="wp_idea-group wp_idea-tools-group wp_idea-tools-group-<?= $section['id'] ?> <?= ($key === 0) ? 'wp_idea-tools-group-default' : '' ?>">
            <?php call_user_func( $section['view_callback'], $section ); ?>
        </div>
    <?php endforeach; ?>

    <script type="text/javascript">
        jQuery( document ).ready( function ( $ ) {
            $( '.wp_idea-nav-tab-wrapper a' ).on( 'click', function( e ) {
                e.preventDefault();

                $( this ).addClass( 'nav-tab-active' )
                    .siblings( 'a' ).removeClass( 'nav-tab-active' );
                $( '.wp_idea-group' ).hide();
                $( $( this ).attr( 'href' ) ).fadeIn();
            } );

            if ( window.location.href.search( 'tab_import_students' ) !== -1 ) {
                $( '#wp-idea-import_students-tab-link' ).trigger( 'click' );
            } else if ( window.location.href.search( 'tab_banned_emails' ) !== -1 ) {
                $( '#wp-idea-banned_emails-tab-link' ).trigger( 'click' );
            } else if ( window.location.href.search( 'tab_api_key' ) !== -1 ) {
                $( '#wp-idea-api-tab-link' ).trigger( 'click' );
            }
        } );
    </script>

	<?php // $settings->show_navigation(); ?>
	<?php // $settings->show_forms(); ?>

</div>
