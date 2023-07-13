<?php

use bpmj\wpidea\View;
use bpmj\wpidea\wolverine\product\Product;
use bpmj\wpidea\wolverine\user\User;

/** @var Product $product */
/** @var bool $displayCategories */
/** @var bool $displayTags */
/** @var bool $displayExcerpt */
/** @var bool $showReadMoreButton */


$has_categories = $displayCategories && $product->hasCategories();
$has_tags = $displayTags && $product->hasTags();
?>

<div class="col-sm-5 glowna_box_product_details">
    <?= View::get('title', [
        'title' => $product->getName(),
        'link' => $product->getPanelOrProductLinkForUser(User::getCurrentUserId()),
        'product_id' => $product->getId(),
    ]) ?>

    <?php if($has_categories || $has_tags): ?>
    <div class="box_glowna_kategorie_wrapper">
        <?php if($has_categories): ?>
            <?= View::get('categories', ['categories' => $product->getCategories()]) ?>
        <?php endif; ?>

        <?php if($has_tags): ?>
            <?= View::get('tags', ['tags' => $product->getTags()]) ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if($displayExcerpt): ?>
        <?= View::get('description', [
             'excerpt' => $product->getExcerpt(),
             'product_link' => $product->getProductLink(),
             'show_read_more_button' => $showReadMoreButton
         ]) ?>
    <?php endif; ?>
</div>
