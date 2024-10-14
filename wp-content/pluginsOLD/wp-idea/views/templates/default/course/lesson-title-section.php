<?php
    $show_subtitle = WPI()->templates->get_meta( 'subtitle_mode' ) !== 'off';
?>

<section class="description">
    <div class="wrapper">

        <h2 class="bg center"><?php the_title(); ?></h2>

        <?php
        if ($show_subtitle) {
            echo '<p>' . WPI()->templates->get_meta( 'subtitle' ) . '</p>';
        }
        ?>
        <!-- Breadcrumbs -->
        <?php WPI()->templates->breadcrumbs(); ?>
        <!-- Koniec breadcrumbs -->
    </div>
</section>