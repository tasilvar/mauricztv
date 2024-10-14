<?php

use bpmj\wpidea\admin\helpers\html\Button;

/** @var ?string $image_url */
/** @var ?string $title */
/** @var string[] $paragraphs */
/** @var Button[] $buttons */
/** @var string $size */
/** @var string $classes */
/** @var string $data */

?>
<div class="wpi-info-box wpi-info-box--<?= $image_url ? 'with-image' : 'no-image' ?> wpi-info-box--<?= $size ?> <?= $classes ?>" <?= $data ?>>
    <?php if($image_url): ?>
    <div class="wpi-info-box__image">
        <img src="<?= $image_url ?>" alt="">
    </div>
    <?php endif; ?>

    <div class="wpi-info-box__content wpi-info-box__content--<?= !$image_url ? 'full-width' : 'default' ?>">
        <h3 class="wpi-info-box__title wpi-info-box__title--<?= $size ?>"><?= $title ?></h3>

        <?php foreach($paragraphs as $index => $paragraph): ?>
            <p class="wpi-info-box__paragraph wpi-info-box__paragraph--<?= $size ?>"><?= $paragraph ?></p>
        <?php endforeach; ?>

        <?php if(!empty($buttons)): ?>
        <div class="wpi-info-box__content__buttons">
            <?php foreach($buttons as $index => $button): ?>
                <?= $button->get_html() ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>