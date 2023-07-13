<?php
use bpmj\wpidea\templates_system\admin\blocks\Course_Module_Lessons_List_Block;
?>

<!-- wp:group {"align":"full"} -->
<div class="wp-block-group alignfull">
    <div class="wp-block-group__inner-container"><?= Course_Module_Lessons_List_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->
