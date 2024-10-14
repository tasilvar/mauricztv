<?php
/**
 * Product Metabox HTML Markup
 */
/* @var $this BPMJ_EDD_Sell_Discount_Product_Metabox */
?>

<div class="edd-sell-discount">
    <?php wp_nonce_field(basename(BPMJ_EDD_SELL_DISCOUNT_DIR), 'edd-sell-discount-nonce'); ?>

    <div class="form">
        <div class="form-group">
            <p class="howto"> <?php _e('Razem z tym produktem możesz sprzedać kod rabatowy który zostanie wygenerowany na podstawie już wcześniej stworzonego. Możesz ustalić jego termin ważności.', BPMJ_EDD_SELL_DISCOUNT_DOMAIN); ?> </p>
        </div>
    </div>


    <div class="form">
        <div class="form-group">
            <label for="edd-sell-discount-code"><strong><?php _e('Wybierz kod wzorcowy', BPMJ_EDD_SELL_DISCOUNT_DOMAIN); ?></strong></label>
            <p><?php $this->discount_codes(); ?></p>
            <p class="howto"><?php _e('Na jego podstawie wygenerujemy <strong>nowy</strong> kod po opłaceniu zamówienia.', BPMJ_EDD_SELL_DISCOUNT_DOMAIN); ?></p>
        </div>

        <div class="form-group">
            <label for="edd-sell-discount-time"><strong><?php _e('Okres ważności', BPMJ_EDD_SELL_DISCOUNT_DOMAIN); ?></strong></label>
            <p>
                <input type="number" step="1" id="edd-sell-discount-time" name="edd-sell-discount-time"
                       value="<?php echo $this->get_time(); ?>">
                <?php $this->discount_time_types(); ?>
            </p>
            <p class="howto"><?php _e('Ten parametr jest opcjonalny. Domyślnie kod rabatowy nigdy nie wygasa.', BPMJ_EDD_SELL_DISCOUNT_DOMAIN); ?></p>
        </div>
    </div>
</div>


<style>
    .edd-sell-discount p {
        margin: 0.5em 0;
    }

    .edd-sell-discount .heading {
        margin: 1em 0;
    }
</style>