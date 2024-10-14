<?php
/*
 * Template for displaying courses category page
 */

global $wp_query;

$term_name = $wp_query->queried_object->name;
$post_ids = '';

foreach( $wp_query->posts as $post ) {
    $id = $post->ID;

    if ( 'download' === get_post_type( $id ) ) {
		if( 'on' !== get_post_meta( $id, 'hide_from_lists', true ) ) {
			$post_ids .= $id . ',';
		}
        continue;
    }

    if ( 'courses' !== get_post_type( $id ) ) {
        continue;
    }

	if( 'on' !== get_post_meta( $id, 'hide_from_lists', true ) ) {
		$post_ids .= $id . ',';
	}
}

if( ! empty( $post_ids ) ) {
    $post_ids = substr($post_ids, 0, -1);
}
?>

<div id="content" class="<?php echo apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?>">
    <div class="contenter">
        <?php
        echo '<h1>' . $term_name . '</h1>';
		WPI()->templates->breadcrumbs();
        echo do_shortcode( '[courses ids='.$post_ids.']' );
        ?>
    </div>
</div>
<?php
WPI()->templates->footer();
