<?php

use bpmj\wpidea\admin\helpers\html\Link;

/** @var Link $model */
?>

<a <?= $model->get_href() ?> class='wpi-link <?= $model->get_classes() ?>' <?= $model->get_data() ?> <?= $model->get_title() ?> target="<?= $model->get_target() ?>">
    <?= $model->has_dashicon() ? $model->get_dashicon_html() . ' ' : '' ?>
    <?= $model->get_text() ?>
</a>
