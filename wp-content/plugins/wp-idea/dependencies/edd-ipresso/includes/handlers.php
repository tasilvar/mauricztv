<?php

/**
 * Tags widget
 *
 * @param array $args
 */
function edd_ipresso_tags_callback( $args ) {
	$value = isset( $args[ 'value' ] ) ? $args[ 'value' ] : edd_get_option( $args[ 'id' ], '' );
	$name  = isset( $args[ 'name' ] ) ? $args[ 'name' ] : 'edd_settings[' . $args[ 'id' ] . ']';
	?>
    <input id="edd_settings-<?php echo $args[ 'id' ]; ?>" name="<?php echo $name; ?>"
           class="regular-text ipresso-tags"
           value="<?php echo $value; ?>"
           type="text"/>
	<?php if ( $args[ 'desc' ] ): ?>
        <label for="edd_settings-<?php echo $args[ 'id' ]; ?>"><?php echo $args[ 'desc' ]; ?></label>
		<?php
	endif;
}