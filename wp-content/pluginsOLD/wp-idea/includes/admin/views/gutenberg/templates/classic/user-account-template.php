<?php

use bpmj\wpidea\templates_system\admin\blocks\Page_Title_Block;
use bpmj\wpidea\templates_system\admin\blocks\User_Account_Form_Block;
?>
<!-- wp:group -->
<div class="wp-block-group">
    <div class="wp-block-group__inner-container"><?= Page_Title_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->
<!-- wp:group -->
<div class="wp-block-group">
    <div class="wp-block-group__inner-container"><?= User_Account_Form_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->