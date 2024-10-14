<div class="contenter">
    <?php the_title( '<h1>', '</h1>' ); ?>
    
    <?php WPI()->templates->breadcrumbs(); ?>
    
    <div class="single-post-featured-image">
        <?php the_post_thumbnail( 'full' ); ?>
    </div>

    <?php
    while ( have_posts() ) {
        the_post();
        the_content();
    }
    ?>
</div>
<!-- Koniec sekcji pod paskiem z menu -->

<div class="contenter">
<?php if ( comments_open() || get_comments_number() ) : ?>
    <!-- Sekcja z komentarzami -->

    <?php comments_template(); ?>

    <!-- Koniec sekcji z komentarzami -->
<?php endif; ?>
</div>