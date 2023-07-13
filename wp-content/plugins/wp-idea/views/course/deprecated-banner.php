<?php
/** @var string $bg_image */

$style_tag_content = '';
if (!empty($bg_image)) {
    $style_tag_content .= "background-image: url($bg_image)";
}
?>

<div id="panel_kursu_slider" class="krotki_slider" style="<?= $style_tag_content ?>">
    <div class="contenter">
        <div><?php the_title(); ?></div>
    </div>
</div>