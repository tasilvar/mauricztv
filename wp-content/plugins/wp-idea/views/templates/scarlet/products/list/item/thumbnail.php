<?php

use bpmj\wpidea\View_Hooks;
use bpmj\wpidea\wolverine\product\Product;
use bpmj\wpidea\wolverine\user\User;

/** @var Product $product */
?>
<div class="glowna_box_zdjecie col-sm-4">
    <a <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_HYPERLINK_PRODUCT, $product->getId()); ?>
            href="<?= $product->getPanelOrProductLinkForUser(User::getCurrentUserId()) ?>" title="<?= $product->getName() ?>" class="thumb glowna_box_zdjecie_link">
        <?php if ($product->getThumbnail()): ?>
            <img itemprop="image" src="<?= $product->getThumbnail() ?>" alt="<?= $product->getName() ?>">
        <?php else: ?>
            <span class="course-thumbnail-default">
                <span><i class="icon-hat"></i></span>
            </span>
        <?php endif; ?>
    </a>
</div>
