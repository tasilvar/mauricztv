<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\Packages;

class Link_Generator_Field extends Abstract_Setting_Field
{
    private int $product_id;
    private ?string $variable_prices_links_html = null;

    public function set_product_id(int $product_id): self
    {
        $this->product_id = $product_id;
        return $this;
    }

    public function set_variable_prices_links_html(?string $variable_prices_links_html): self
    {
        $this->variable_prices_links_html = $variable_prices_links_html;
        return $this;
    }

    public function render_to_string(): string
    {
        ob_start();

        $single_price_style    = $this->variable_prices_links_html ? 'style="display: none;"' : '';
        $variable_prices_style = $this->variable_prices_links_html ? '' : 'style="display: none;"';
        ?>
        <div class="bpmj-eddcm-add-to-cart-link-creator">

            <?php
            $count = wp_count_posts('edd_discount');

            if ($count->active > 100) : ?>
                <datalist id="bpmj-eddcm-add-to-cart-discount-list"></datalist>
            <?php
            else : ?>

                <datalist id='bpmj-eddcm-add-to-cart-discount-list'>
                    <?php
                    $discounts = edd_get_discounts(['post_status' => ['active'], 'posts_per_page' => -1]);
                    $discounts = !empty($discounts) ? $discounts : [];

                    foreach ($discounts as $discount): ?>
                        <option value="<?php
                        echo esc_attr(edd_get_discount_code($discount->ID)); ?>"></option>
                    <?php
                    endforeach;
                    ?>
                </datalist>

            <?php
            endif; ?>
            <div class="form-group">
                <div class="link-generator-row">
                    <label for="bpmj-eddcm-add-to-cart-link-base"
                           style="display: block;font-size: 16px;margin-bottom: 5px;padding: 0;"><?php
                        _e(
                            'Base URL',
                            BPMJ_EDDCM_DOMAIN
                        ) ?></label>
                    <input type="text" id="bpmj-eddcm-add-to-cart-link-base"
                           name="atc_link_base"
                           value="<?php
                           echo esc_attr(edd_get_checkout_uri()); ?>"/>
                </div>
                <div class='link-generator-row'>
                    <label for="bpmj-eddcm-add-to-cart-link-discount"
                           style="display: block;font-size: 16px;margin-bottom: 5px;padding: 0;"><?php
                        _e(
                            'Discount code',
                            BPMJ_EDDCM_DOMAIN
                        ) ?></label>
                    <input type="text" name="atc_discount" list="bpmj-eddcm-add-to-cart-discount-list"
                           id="bpmj-eddcm-add-to-cart-link-discount" value=""/>
                    <?php
                    if (edd_item_quantities_enabled()):
                        ?>
                        <label for="bpmj-eddcm-add-to-cart-link-quantity"
                               style="display: block;font-size: 16px;margin-bottom: 5px;padding: 0;"><?php
                            _e(
                                'Quantity',
                                BPMJ_EDDCM_DOMAIN
                            ) ?></label>
                        <input type="number" name="atc_quantity" id="bpmj-eddcm-add-to-cart-link-quantity" value="1"/>
                    <?php
                    endif;
                    ?>
                    <?php
                    if (WPI()->packages->has_access_to_feature(Packages::FEAT_BUY_AS_GIFT)): ?>
                        <label>
                            <?php
                            _e('Buy as a gift?', BPMJ_EDDCM_DOMAIN); ?>
                            <input type="checkbox" name="atc_gift" id="bpmj-eddcm-add-to-cart-link-gift" <?= $this->get_disabled_html_attr() ?> />
                        </label>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
            <div class="form-group bpmj-eddcm-single-price" <?= $single_price_style; ?>>
                <div class='link-generator-row'>
                    <label for="bpmj-eddcm-add-to-cart-link"
                           style='display: block;font-size: 16px;margin-bottom: 5px;padding: 0;'><?php
                        _e('Add to cart link', BPMJ_EDDCM_DOMAIN) ?><span
                                class="bpmj-eddcm-add-to-cart-link-copied"><?php
                            _e(
                                'Copied',
                                BPMJ_EDDCM_DOMAIN
                            ) ?></span><br></label>
                    <input type="text" id="bpmj-eddcm-add-to-cart-link" class="select-on-focus bpmj-eddcm-add-to-cart-link"
                           style="width: 100%;"
                           data-product-id="<?php
                           echo esc_attr($this->product_id); ?>"
                           value="<?php
                           echo esc_attr(edd_get_checkout_uri() . '?add-to-cart=' . $this->product_id); ?>"/>
                    <span class="bpmj-eddcm-add-to-cart-link-copy"><?php
                        _e('Copy', BPMJ_EDDCM_DOMAIN) ?></span>
                </div>
            </div>

            <div id="bpmj-eddcm-variable-prices-add-to-cart-links"
                 class="form-group bpmj-eddcm-variable-prices" <?= $variable_prices_style; ?>>
                <?= $this->variable_prices_links_html ?>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }
}