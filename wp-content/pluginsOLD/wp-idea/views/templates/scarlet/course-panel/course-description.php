<?php if ( WPI()->templates->get_meta( 'first_section' ) !== 'off' ) { ?>
    <div>
        <?php
        if ( have_posts() ) {
            while ( have_posts() ) {
                the_post();
                the_content();
            }
        }
        ?>
    </div>
<?php } ?>