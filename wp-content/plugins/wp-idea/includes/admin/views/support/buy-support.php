<?php

use bpmj\wpidea\admin\support\Links;
use bpmj\wpidea\admin\support\Support;

?>

<?php if( !Support::current_user_has_active_support() ): ?>
    <div class="buy-support">
        <span class="dashicons dashicons-lightbulb buy-support__icon"></span> 
        <h3 class="buy-support__description"><?= __('Only active LMS Idea software key provides access to full technical support. <strong>Renew your license now to enjoy all the benefits!</strong>', BPMJ_EDDCM_DOMAIN ) ?></strong></h3>
        <a href="<?= Links::get_purchase_link() ?>" class="buy-support__button" target="_BLANK"><?= __('Buy support', BPMJ_EDDCM_DOMAIN ) ?></a>
        <a href="<?= Links::SUPPORT_PROFITS ?>" class="buy-support__see-profits" target="_BLANK"><?= __('See the benefits of active support', BPMJ_EDDCM_DOMAIN ) ?></a>

        <div class="buy-support__table" style="display:none;">
            <iframe data-src="<?= Links::get_localized_comparison_table_link() ?>" frameborder="0"></iframe>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($){
        showTable();

        function showTable(){
            var button              = $('.buy-support__see-profits');
            var table               = $('.buy-support__table');
            var iframe              = table.find('iframe');
            
            button.on('click', function(e){
                e.preventDefault();
                table.slideToggle();

                iframe.attr( 'src', iframe.data('src') );
            })
        }
    })
    </script>
<?php endif; ?>