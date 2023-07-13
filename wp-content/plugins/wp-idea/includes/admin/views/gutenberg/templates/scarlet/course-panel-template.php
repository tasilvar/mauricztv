<?php

use bpmj\wpidea\templates_system\admin\blocks\{Breadcrumbs_Block,
    Course_Banner_Block,
    Course_Panel_Lessons_List_Block,
    Course_Panel_Lessons_Modules_Block,
    Course_Top_Bar_Block,
    Page_Content_Block};

?>
<!-- wp:group {"align":"full"} -->
<div class="wp-block-group alignfull">
    <div class="wp-block-group__inner-container"><?= Course_Top_Bar_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full"} -->
<div class="wp-block-group alignfull">
    <div class="wp-block-group__inner-container"><?= Course_Banner_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->

<!-- wp:group -->
<div class="wp-block-group">
    <div class="wp-block-group__inner-container"><?= Breadcrumbs_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->

<!-- wp:group -->
<div class="wp-block-group">
    <div class="wp-block-group__inner-container"><?= Page_Content_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->

<!-- wp:group -->
<div class="wp-block-group">
    <div class="wp-block-group__inner-container"><?= Course_Panel_Lessons_Modules_Block::get_gutenberg_block_content() ?> </div>
</div>
<!-- /wp:group -->

<!-- wp:group -->
<div class="wp-block-group">
    <div class="wp-block-group__inner-container"><?= Course_Panel_Lessons_List_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->
