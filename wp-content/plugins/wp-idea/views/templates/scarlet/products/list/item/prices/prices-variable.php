<?php
use bpmj\wpidea\View;
use bpmj\wpidea\wolverine\product\Product;

/** @var Product $product */
?>

<?php if(!$product->hasVariants()) return; ?>

<?php if($product->getIsInCart() && $product->hasPriceModeSingle()) return; ?>

<?php if($product->hasPriceModeMulti()): ?>
    <?= View::get('prices-variable-multiselect', [
        'productId' => $product->getId(),
        'variants' => $product->getVariants(),
        'defaultPriceId' => $product->getDefaultVariantId()
    ]) ?>
<?php else: ?>
    <?= View::get('prices-variable-select', [
        'productId' => $product->getId(),
        'variants' => $product->getVariants(),
        'defaultPriceId' => $product->getDefaultVariantId()
    ]) ?>
<?php endif; ?>