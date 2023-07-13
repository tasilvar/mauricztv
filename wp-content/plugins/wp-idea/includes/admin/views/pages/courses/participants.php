<?php

use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;

/** @var Interface_Dynamic_Table $table */
?>

<div class='wrap participants-page'>
    <hr class="wp-header-end">

    <h1 class='wp-heading-inline'><?= $page_title ?></h1>
    <?= $table->get_html('users-list-table') ?>
</div>
