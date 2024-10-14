<?php
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;

/** @var Interface_Dynamic_Table $table */
/** @var string $page_title */
?>

<div class='wrap affiliate-program-page'>
    <hr class="wp-header-end">

    <h1 class='wp-heading-inline'><?= $page_title ?></h1>

    <?= $table->get_html('affiliate-program-list-table') ?>
</div>
