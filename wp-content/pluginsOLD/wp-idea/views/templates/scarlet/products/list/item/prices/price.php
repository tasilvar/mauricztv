<?php

use bpmj\wpidea\wolverine\product\Product;
use bpmj\wpidea\infrastructure\system\System;

/** @var Product $product */
?>

<?php if ($product->hasPromotionalPrice() && !$product->hasVariants()) : ?>
    <div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="glowna_box_cena glowna_box_cena_promo">
        <p class="glowna_box_cena_cena"><?= $product->getFormattedPromotionalPrice() ?></p>
        <meta itemprop="price" content="<?= $product->getPromotionalPrice() ?>"></meta>
        <p itemprop="priceCurrency" class="glowna_box_cena_waluta"><?= System::get_currency() ?></p>
    </div>
    <div class="glowna_box_cena glowna_box_cena_promocyjna">
        <p class="glowna_box_cena_cena"><?= $product->getFormattedPrice() ?></p>
        <p class="glowna_box_cena_waluta"><?= System::get_currency() ?></p>
    </div>
<?php else: ?>
    <?php if ($product->isFree() && !$product->hasVariants()) : ?>
        <div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="glowna_box_cena glowna_box_cena_dostepny" >
            <p class="glowna_box_cena_dostepny_opis gratis"><?php _e( 'Free', BPMJ_EDDCM_DOMAIN ); ?></p>
            <meta itemprop="price" content="0"></meta>
            <meta itemprop="priceCurrency" content="<?= System::get_currency() ?>"></meta>
        </div>
    <?php else: ?>
        <div itemprop="offers" itemscope itemtype="<?= $product->hasVariants() ? 'https://schema.org/AggregateOffer' : 'https://schema.org/Offer' ?>" class="glowna_box_cena">
            <?php if($product->hasVariants()): ?>
                <p class="glowna_box_cena_od"><?= __( 'from', BPMJ_EDDCM_DOMAIN ) ?></p>
                <p class="glowna_box_cena_cena"><?= $product->getFormattedLowestVariantPrice() ?></p>
                <meta itemprop="lowPrice" content="<?= $product->getLowestVariantPrice() ?>"></meta>
            <?php else: ?>
                <p class="glowna_box_cena_cena"><?= $product->getFormattedPrice() ?></p>
                <meta itemprop="price" content="<?= $product->getPrice() ?>"></meta>
            <?php endif; ?>

            <p itemprop="priceCurrency" class="glowna_box_cena_waluta"><?= System::get_currency() ?></p>
        </div>
    <?php endif; ?>
<?php endif; ?>
