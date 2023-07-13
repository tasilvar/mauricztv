<div class="col-md-4 posts-loop-item">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <?php if( has_post_thumbnail() ): ?>
        <div class="single-post-featured-image">
            <?php the_post_thumbnail( 'large' ); ?>
        </div>
        <?php endif; ?>

        <header class="entry-header">
            <a href="<?= get_post_permalink() ?>" title="<?= get_the_title(); ?>" class="single-post-title-link">
                <?php the_title( '<h2>', '</h2>' ); ?>
            </a>
        </header>

        <div class="entry-content">
            <?= get_the_excerpt(); ?>
            <a class="button button-read-more" href="<?= get_the_permalink(); ?>"><?php _e( 'Read more', BPMJ_EDDCM_DOMAIN ) ?></a>
        </div>
    </article>
</div>