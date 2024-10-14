<?php

use bpmj\wpidea\View;

/** @var View $view */
/** @var ?string $image */
/** @var ?string $title */
/** @var string $title_color */
/** @var string $title_alignment_v */
/** @var string $title_alignment_h */

$style_tag_content = !empty($image) ? "background-image: url($image)" : '';
?>


<?php if(!$title && !$image) : ?>

    <?= $view::get('banner-placeholder') ?>

<?php else: ?>

    <?php
    $banner_class_mod = $title ? 'wpi-course-banner--with-title' : 'wpi-course-banner--without-title';
    $banner_class_mod .= $image ? ' wpi-course-banner--with-image' : ' wpi-course-banner--without-image';
    ?>
    <div class="wpi-course-banner <?= $banner_class_mod ?>">

        <?php if($image): ?>
            <img src="<?= $image ?>" alt="" class="wpi-course-banner__image">
        <?php endif; ?>

        <?php if($title): ?>
        <div class="wpi-course-banner__title-wrapper" style="align-items: <?= $title_alignment_v ?>; justify-content: <?= $title_alignment_h ?>;">
            <h1 class="wpi-course-banner__title" style="color: <?= $title_color ?>;"><?= $title ?></h1>
        </div>
        <?php endif; ?>

    </div>

<?php endif; ?>

