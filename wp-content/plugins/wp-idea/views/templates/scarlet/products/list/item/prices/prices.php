<?php

use bpmj\wpidea\resources\Resource_Type;
use bpmj\wpidea\View;
use bpmj\wpidea\wolverine\product\Product;
use bpmj\wpidea\wolverine\user\User;
use bpmj\wpidea\infrastructure\system\System;

/** @var Product $product */
/** @var bool $userHasAccess */
/** @var bool $showBuyButton */
/** @var bool $showPrice */
?>

<div class="col-sm-3
    <?php if (!$product->getIsInCart() && !$userHasAccess && $product->hasVariants()): echo 'warianty'; endif; ?>
    <?php if (!$product->getIsInCart() && !$userHasAccess && $product->hasPromotionalPrice()): echo 'promocja'; endif; ?>">

    <form class="edd_download_purchase_form edd_purchase_<?= $product->getId() ?> 
        <?php if ($product->salesDisabled() && !$userHasAccess): ?>edd-sales-disabled<?php endif; ?>"
            <?php if ($product->salesDisabled()): ?>
                data-eddcm-sales-disabled-reason="<?= $product->getSalesDisabledReason() ?>" data-eddcm-sales-disabled-reason-long="<?= $product->getSalesDisabledReasonDescription() ?>"
            <?php endif; ?>
                method="post">

            <?php
            $linked_resource_type = $product->getLinkedResourceType();

            if ($userHasAccess && ($linked_resource_type !== Resource_Type::DIGITAL_PRODUCT) && ($linked_resource_type !== Resource_Type::SERVICE)): ?>

                <?= View::get('price-unlocked', [
                    'link' => $product->getPanelOrProductLinkForUser(User::getCurrentUserId())
                ]) ?>
            <?php else: ?>
                <?php if ($showBuyButton && $product->hasVariants()): ?>
                    <?= View::get('prices-variable', [
                        'product' => $product
                    ]) ?>
                <?php endif; ?>

                <?php bpmj_render_lowest_price_information($product->getId()); ?>

                <?php if($showPrice): ?>
                    <?= View::get('price', [
                        'product' => $product
                    ]) ?>
                <?php endif; ?>

                <?php if($showBuyButton): ?>
                    <?= View::get('add-to-cart-button', [
                        'product' => $product,
                        'isAjaxPurchaseEnabled' => System::is_ajax_purchase_enabled()
                    ]) ?>
                <?php endif; ?>
            <?php endif; ?>
    </form>
</div>