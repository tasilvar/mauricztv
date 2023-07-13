<?php
use bpmj\wpidea\admin\helpers\html\Paragraph;

/** @var Paragraph $model */
?>

<p class="<?= $model->get_classes() ?>" <?= $model->get_data() ?>><?= $model->get_text() ?></p>
