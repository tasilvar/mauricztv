<?php

use bpmj\wpidea\templates_system\admin\blocks\{
    Breadcrumbs_Block,
    Page_Title_Block,
    Search_Results_Block
};

?>
<!-- wp:group -->
<div class="wp-block-group">
    <div class="wp-block-group__inner-container">
        <?= Page_Title_Block::get_gutenberg_block_content() ?>
    </div>
</div>
<!-- /wp:group -->
<!-- wp:group -->
<div class="wp-block-group">
    <div class="wp-block-group__inner-container">
        <?= Breadcrumbs_Block::get_gutenberg_block_content() ?>
    </div>
</div>
<!-- /wp:group -->
<!-- wp:group -->
<div class="wp-block-group">
    <div class="wp-block-group__inner-container">
        <?= Search_Results_Block::get_gutenberg_block_content() ?>
    </div>
</div>
<!-- /wp:group -->