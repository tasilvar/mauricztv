<?php

use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\View;
use bpmj\wpidea\View_Hooks;
use bpmj\wpidea\wolverine\product\Product;
use bpmj\wpidea\wolverine\user\User;

/** @var Product $product */

?>
<?php View_Hooks::run(View_Hooks::BEFORE_PRODUCT_LIST_ITEM) ?>
<div itemscope itemtype="http://schema.org/Product" class="col-sm-12" id="edd_download_<?= $product->getId() ?>">
    <div class="box">

        <?= View::get('thumbnail', [
            'product' => $product,
        ]) ?>
        <?= View::get('details', [
            'product' => $product,
            'displayCategories' => LMS_Settings::get_option('display_categories'),
            'displayTags' => LMS_Settings::get_option('display_tags'),
            'displayExcerpt' => 'yes' === LMS_Settings::get_option('list_excerpt'),
            'showReadMoreButton' => ('true' === LMS_Settings::get_option('list_details_button') && !App_View_API_Static_Helper::is_active()),
        ]) ?>
        <?= View::get('prices/prices', [
            'product' => $product,
            'showBuyButton' => 'yes' === LMS_Settings::get_option('list_buy_button'),
            'showPrice' => 'yes' === LMS_Settings::get_option('list_price'),
            'userHasAccess' => $product->userHasAccess(User::getCurrentUserId()),
        ]) ?>
    </div>
</div>

