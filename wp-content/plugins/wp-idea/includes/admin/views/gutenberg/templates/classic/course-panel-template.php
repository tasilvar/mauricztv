<?php
use bpmj\wpidea\templates_system\admin\blocks\Course_Panel_Description_Block;
use bpmj\wpidea\templates_system\admin\blocks\Course_Panel_Lessons_List_Block;
use bpmj\wpidea\templates_system\admin\blocks\Course_Panel_Lessons_Modules_Block;

?>
<!-- wp:group {"align":"full"} -->
<div class="wp-block-group alignfull">
    <div class="wp-block-group__inner-container"><?= Course_Panel_Description_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full"} -->
<div class="wp-block-group alignfull">
    <div class="wp-block-group__inner-container"><?= Course_Panel_Lessons_Modules_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full"} -->
<div class="wp-block-group alignfull">
    <div class="wp-block-group__inner-container"><?= Course_Panel_Lessons_List_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->