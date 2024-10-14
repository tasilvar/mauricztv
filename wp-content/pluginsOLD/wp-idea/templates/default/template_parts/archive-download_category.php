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
        $post_ids .= ($id-1) . ',';
        continue;
    }

    if ( 'courses' !== get_post_type( $id ) ) {
        continue;
    }

    $tmp_id = get_post_meta( $id, 'product_id', true );
    $post_ids .= ($tmp_id-1) . ',';
}

if( ! empty( $post_ids ) ) {
    $post_ids = substr($post_ids, 0, -1);
}
?>

<section class="<?php echo apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?>">
    <div class="wrapper">
        <?php
        echo '<h2 class="bg center">' . $term_name . '</h2>';
        echo do_shortcode( '[courses ids='.$post_ids.']' );
        ?>
    </div>
</section>
<?php
WPI()->templates->footer();