<?php
use bpmj\wpidea\templates_system\admin\blocks\Cart_Content_Block;
?>
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":100} -->
    <div class="wp-block-column" style="flex-basis:100%"><?= Cart_Content_Block::get_gutenberg_block_content() ?></div>
<!-- /wp:column -->
<!-- /wp:columns -->
