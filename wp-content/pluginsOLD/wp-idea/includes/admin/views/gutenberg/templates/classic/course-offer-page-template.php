<?php

use bpmj\wpidea\templates_system\admin\blocks\{Course_Offer_Page_Content_Block, Opinions_Block};
use bpmj\wpidea\templates_system\admin\blocks\Page_Title_Block;

?>
<!-- wp:group -->
<div class="wp-block-group">
    <div class="wp-block-group__inner-container"><?= Page_Title_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->

<!-- wp:group -->
<div class="wp-block-group"><div class="wp-block-group__inner-container">
    <?= Course_Offer_Page_Content_Block::get_gutenberg_block_content() ?>
</div></div>
<!-- /wp:group -->

<!-- wp:group -->
<div class="wp-block-group"><div class="wp-block-group__inner-container">
        <?= Opinions_Block::get_gutenberg_block_content() ?>
    </div></div>
<!-- /wp:group -->