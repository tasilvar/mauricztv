<?php
/**
 * Server-side rendering of the `core/button` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/button` block on the server,
 *
 * @since 6.6.0
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block object.
 *
 * @return string The block content.
 */
function gutenberg_render_block_core_button( $attributes, $content ) {
	/*
	 * The current Gutenberg plugin supports WordPress 6.4, but the next_token()
	 * method does not exist in WordPress 6.4. Therefore, if Gutenberg is used
	 * as a plugin, use the Gutenberg class that has the `next_token()` method.
	 *
	 * TODO: After the Gutenberg plugin drops support for WordPress 6.4, this
	 * conditional statement will be removed and the core class `WP_HTML_Tag_Processor`
	 * should be used.
	 */
	if ( defined( 'IS_GUTENBERG_PLUGIN' ) && IS_GUTENBERG_PLUGIN && class_exists( 'Gutenberg_HTML_Tag_Processor_6_7' ) ) {
		$p = new Gutenberg_HTML_Tag_Processor_6_7( $content );
	} else {
		$p = new WP_HTML_Tag_Processor( $content );
	}

	/*
	 * The button block can render an `<a>` or `<button>` and also has a
	 * `<div>` wrapper. Find the a or button tag.
	 */
	$tag = null;
	while ( $p->next_tag() ) {
		$tag = $p->get_tag();
		if ( 'A' === $tag || 'BUTTON' === $tag ) {
			break;
		}
	}

	/*
	 * If this happens, the likelihood is there's no block content,
	 * or the block has been modified by a plugin.
	 */
	if ( null === $tag ) {
		return $content;
	}

	// If the next token is the closing tag, the button is empty.
	$is_empty = true;
	while ( $p->next_token() && $tag !== $p->get_token_name() && $is_empty ) {
		if ( '#comment' !== $p->get_token_type() ) {
			/**
			 * Anything else implies this is not empty.
			 * This might include any text content (including a space),
			 * inline images or other HTML.
			 */
			$is_empty = false;
		}
	}

	/*
	 * When there's no text, render nothing for the block.
	 * See https://github.com/WordPress/gutenberg/issues/17221 for the
	 * reasoning behind this.
	 */
	if ( $is_empty ) {
		return '';
	}

	return $content;
}

/**
 * Registers the `core/button` block on server.
 *
 * @since 6.6.0
 */
function gutenberg_register_block_core_button() {
	register_block_type_from_metadata(
		__DIR__ . '/button',
		array(
			'render_callback' => 'gutenberg_render_block_core_button',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_button', 20 );
