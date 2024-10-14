<div class="course-offer-page-main-content">
    <?php
        while ( have_posts() ) {
            the_post();
            the_content();
        }
        remove_action( 'edd_after_download_content', 'edd_append_purchase_link' );
    ?>
</div>