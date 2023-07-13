<?php
/** @var int $product_id */
?>
<div class="contenter_tresci">
<?php
    the_title('<h2>', '</h2>');

    remove_action( 'edd_after_download_content', 'edd_append_purchase_link' );

    while ( have_posts() ) {
        the_post();
        the_content();
    }
?>
</div>