<?php
use bpmj\wpidea\templates_system\admin\blocks\{
    Breadcrumbs_Block,
    Products_Block,
    Archive_Title_Block
};
?>
        <!-- wp:group -->
        <div class="wp-block-group">
                <div class="wp-block-group__inner-container"> 
                  <?= Archive_Title_Block::get_gutenberg_block_content() ?>
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
                <?= Products_Block::get_gutenberg_block_content() ?>
              </div>
            </div>
         <!-- /wp:group -->