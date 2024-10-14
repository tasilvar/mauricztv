<?php
/** @var \bpmj\wpidea\admin\tables\Enhanced_Table $table */
/** @var string $page_title */
/** @var \bpmj\wpidea\admin\helpers\html\Info_Box $info_box */
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?= $page_title ?></h1>

    <hr class="wp-header-end">

    <?= $info_box->get_html() ?>

    {no_active_group_warning}

    <?= $table->render() ?>
</div>