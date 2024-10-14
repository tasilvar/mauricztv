<?php

use bpmj\wpidea\templates_system\admin\blocks\{
    Comments_Block,
    Course_Content_List,
    Course_Files_Block,
    Course_Lesson_Info_Block,
    Course_Lesson_Title_Section_Block,
    Course_Navigation,
    Course_Progress_Block,
    Page_Content_Block
};

?>
<!-- wp:group {"align":"full"} -->
<div class="wp-block-group alignfull">
    <div class="wp-block-group__inner-container"><?= Course_Lesson_Title_Section_Block::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->

<!-- wp:group -->
<div class="wp-block-group">
    <div class="wp-block-group__inner-container"><?= Course_Navigation::get_gutenberg_block_content() ?></div>
</div>
<!-- /wp:group -->

<!-- wp:columns -->
<div class="wp-block-columns">
    <!-- wp:column {"width":75.00} -->
    <div class="wp-block-column" style="flex-basis:75%">
        <!-- wp:group -->
        <div class="wp-block-group">
            <div class="wp-block-group__inner-container"><?= Page_Content_Block::get_gutenberg_block_content() ?></div>
        </div>
        <!-- /wp:group -->

        <!-- wp:group -->
        <div class="wp-block-group">
            <div class="wp-block-group__inner-container"><?= Comments_Block::get_gutenberg_block_content() ?></div>
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:column -->
    <!-- wp:column {"width":25.00} -->
    <div class="wp-block-column" style="flex-basis:25%">
        <!-- wp:group -->
        <div class="wp-block-group">
            <div class="wp-block-group__inner-container"><?= Course_Progress_Block::get_gutenberg_block_content() ?></div>
        </div>
        <!-- /wp:group -->

        <!-- wp:group -->
        <div class="wp-block-group">
            <div class="wp-block-group__inner-container"><?= Course_Lesson_Info_Block::get_gutenberg_block_content() ?></div>
        </div>
        <!-- /wp:group -->

        <!-- wp:group -->
        <div class="wp-block-group">
            <div class="wp-block-group__inner-container"><?= Course_Files_Block::get_gutenberg_block_content() ?></div>
        </div>
        <!-- /wp:group -->

        <!-- wp:group -->
        <div class="wp-block-group">
            <div class="wp-block-group__inner-container"><?= Course_Content_List::get_gutenberg_block_content() ?></div>
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:column -->
</div>
<!-- /wp:columns -->
