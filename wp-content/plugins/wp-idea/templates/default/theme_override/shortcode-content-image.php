<?php if ( !in_array( get_post_thumbnail_id( get_the_ID() ), array( '', -1 ) ) ) { ?>
    <div class="edd_download_image">
        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php echo get_the_post_thumbnail( get_the_ID(), 'post-thumbnail' ); ?>
        </a>
    </div>
<?php } else { ?>
    <div class="edd_download_image">
        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
            <span class="course-thumbnail-default"></span>
        </a>
    </div>
<?php } ?>