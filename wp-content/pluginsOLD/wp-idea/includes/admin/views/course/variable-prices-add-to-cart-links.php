<?php
use bpmj\wpidea\translator\Interface_Translator;

/** @var int $product_id */
/** @var Interface_Translator $translator */
/** @var array $variable_prices */


    foreach ( $variable_prices as $price_id => $variable_price ):
    ?>
    <div class='link-generator-row'>
        <label for="bpmj-eddcm-add-to-cart-link"
               style='display: block;font-size: 16px;margin-bottom: 5px;padding: 0;'>
            <?php
            echo $translator->translate('service_editor.sections.link_generator.variable_prices.price') . ' ' . esc_html( $variable_price[ 'name' ] );
            ?>
            <span class="bpmj-eddcm-add-to-cart-link-copied"><?= $translator->translate('service_editor.sections.link_generator.variable_prices.copied') ?></span><br></label>
        <input type="text" id="bpmj-eddcm-add-to-cart-link" class="select-on-focus bpmj-eddcm-add-to-cart-link"
               style="width: 100%;"
               data-product-id="<?php echo $product_id; ?>"
               data-price-id="<?php echo $price_id; ?>"
               value="<?php echo esc_attr( edd_get_checkout_uri( array(
                   'add-to-cart' => $product_id,
                   'price-id'    => $price_id,
               ) ) ); ?>"/>
        <span class="bpmj-eddcm-add-to-cart-link-copy"><?= $translator->translate('service_editor.sections.link_generator.variable_prices.copy') ?></span>
    </div>
  <?php
  endforeach;

