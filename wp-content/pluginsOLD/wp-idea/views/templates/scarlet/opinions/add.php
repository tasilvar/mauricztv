<?php
/* @var Interface_Translator $translator */
/* @var Product_To_Rate_Collection $products_user_can_rate */
/* @var string $user_name */
/* @var string $opinions_rules_url */

use bpmj\wpidea\Info_Message;
use bpmj\wpidea\modules\opinions\core\collections\Product_To_Rate_Collection;
use bpmj\wpidea\translator\Interface_Translator;

?>

<div class="add-opinion-wrapper">
    <?php if (!$products_user_can_rate->is_empty()): ?>

    <template id='spinner-template'>
        <div class='loader'></div>
    </template>

    <p class="error" style="display: none;"><?= $translator->translate('user_account.opinions.add.saving_error') ?></p>
    <p class="success" style="display: none;"></p>

    <form class='add-opinion'>
        <label for='reviewed-product'><?= $translator->translate('user_account.opinions.add.label.reviewed_product') ?></label>
        <select id="reviewed-product" class="reviewed-product" name="product" required>
            <option value=""><?= $translator->translate('user_account.opinions.add.select.select_product') ?></option>
            <?php
            foreach ($products_user_can_rate as $product) {
                echo "<option value='{$product->get_product_id()}'>{$product->get_product_name()}</option>";
            }
            ?>
        </select>

        <label for="reviewer-name"><?= sprintf($translator->translate('user_account.opinions.add.label.reviewer_name'), "<button role='link' data-target-tab-id='account-settings'>", '</button>') ?></label>
        <input id="reviewer-name" class="reviewer-name" value="<?= $user_name ?>" disabled/>

        <label><?= $translator->translate('user_account.opinions.add.label.rating') ?></label>
        <div class="product-rating-stars">
            <input id='product-rating-1' type='radio' name='product-rating' value='1' required>
            <input id='product-rating-2' type='radio' name='product-rating' value='2'>
            <input id='product-rating-3' type='radio' name='product-rating' value='3'>
            <input id='product-rating-4' type='radio' name='product-rating' value='4'>
            <input id='product-rating-5' type='radio' name='product-rating' value='5'>

            <div class="stars-labels">
                <label for='product-rating-1'>
                    <span class='publigo-opinion-star dashicons dashicons-star-empty star-default'></span>
                    <span class='publigo-opinion-star dashicons dashicons-star-filled star-checked'></span>
                </label>
                <label for='product-rating-2'>
                    <span class='publigo-opinion-star dashicons dashicons-star-empty star-default'></span>
                    <span class='publigo-opinion-star dashicons dashicons-star-filled star-checked'></span>
                </label>
                <label for='product-rating-3'>
                    <span class='publigo-opinion-star dashicons dashicons-star-empty star-default'></span>
                    <span class='publigo-opinion-star dashicons dashicons-star-filled star-checked'></span>
                </label>

                <label for='product-rating-4'>
                    <span class='publigo-opinion-star dashicons dashicons-star-empty star-default'></span>
                    <span class='publigo-opinion-star dashicons dashicons-star-filled star-checked'></span>
                </label>

                <label for='product-rating-5'>
                    <span class='publigo-opinion-star dashicons dashicons-star-empty star-default'></span>
                    <span class='publigo-opinion-star dashicons dashicons-star-filled star-checked'></span>
                </label>
            </div>
            <br class="clear">
        </div>
        <label for='opinion-content'><?= $translator->translate('user_account.opinions.add.label.opinion_content') ?></label>
        <textarea id="opinion-content" name="opinion-content" required></textarea>
        <p class="add-opinion-info">
            <?= sprintf($translator->translate('user_account.opinions.add.add_opinion_info'), $opinions_rules_url) ?>
        </p>
        <button class="add-opinion-button"
                type='submit'>
            <span class="dashicons dashicons-text-page"></span>
            <?= $translator->translate('user_account.opinions.add.save') ?>
        </button>
    </form>
    <?php endif; ?>

    <div class="no-products-to-rate" style="display: <?= $products_user_can_rate->is_empty() ? 'block' : 'none' ?>;">
        <?php
        $message = new Info_Message($translator->translate('user_account.opinions.add.no_product_to_review'), null, 'media-text');
        $message->render();
        ?>
    </div>
</div>