<?php
use bpmj\wpidea\View;
/** @var string $custom_class */
?>

<div class="wpi-block wpi-block-page-content <?= $custom_class ?>">
    <?php the_content() ?>
</div>

<?=  View::get('/scripts/check-lesson-as-undone'); ?>
