<section class="modules bg">
    <div class="wrapper">
        <h2 class="bg center"><?php the_title(); ?></h2>

        <?php
        if ( have_posts() ) {
            ?>
            <div>
                <?php
                while ( have_posts() ) {
                    the_post();
                    the_content();
                }
                ?>
            </div>
        <?php } ?>

        <?php
        $module = WPI()->courses->get_course_structure_module( get_post() );
        if ( $module ) {
            foreach ( $module->get_children() as $lesson ) {
                ?>
                <div class="box-wrapper">
                    <div class="box">
                        <?php if ( $lesson->should_be_grayed_out() ): ?>
                            <div class="thumb note blurred"
                                 <?php if ( $lesson->get_thumbnail() ) { ?>style="background-image: url(<?php echo $lesson->get_thumbnail(); ?>); background-repeat: no-repeat; background-size: cover; background-position: center center;" <?php } ?>></div>
                            <h3><?php echo $lesson->post_title; ?>
                            </h3>
                            <?php if ( $lesson->get_subtitle() ) { ?>
                                <p><?php echo $lesson->get_subtitle(); ?></p><?php } ?>
                        <?php else: ?>
                            <a href="<?php echo $lesson->get_permalink(); ?>">
                                <div class="thumb note"
                                     <?php if ( $lesson->get_thumbnail() ) { ?>style="background-image: url(<?php echo $lesson->get_thumbnail(); ?>); background-repeat: no-repeat; background-size: cover; background-position: center center;" <?php } ?>></div>
                            </a>
                            <h3>
                                <a href="<?php echo $lesson->get_permalink(); ?>"><?php echo $lesson->post_title; ?></a>
                            </h3>
                            <?php if ( $lesson->get_subtitle() ) { ?><p><?php echo $lesson->get_subtitle(); ?> <a
                                    href="<?php echo $lesson->get_permalink(); ?>"><span
                                        class="arrow">&rsaquo;</span></a>
                                </p><?php } ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
         

<?php if ( comments_open() || get_comments_number() ) : ?>
    <!-- Sekcja z komentarzami -->

    <?php comments_template(); ?>

    <!-- Koniec sekcji z komentarzami -->
<?php endif; ?>
    </div> 
</section>