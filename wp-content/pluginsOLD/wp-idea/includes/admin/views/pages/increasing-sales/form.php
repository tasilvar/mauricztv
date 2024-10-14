<?php

use bpmj\wpidea\modules\increasing_sales\core\dto\Offer_Data_DTO;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Increasing_Sales_Offer_Type;
use bpmj\wpidea\nonce\Nonce_Handler;

/** @var string $page_title */
/** @var string $action */
/** @var array $products */
/** @var array $products_not_assigned_to_the_offer */
/** @var Offer_Data_DTO $fields */
/** @var string $url_increasing_sales_page */
/** @var string $currency */
/** @var Interface_Translator $translator */


$product_id = $fields->product_id ?? '';
$offer_type = $fields->offer_type ?? '';
$offered_product_id = $fields->offered_product_id ?? '';
$image_url = $fields->image_url ?? '';
?>

<div class='wrap increasing-sales-page'>
    <hr class='wp-header-end'>

    <h1 class='wp-heading-inline'><?= $page_title ?></h1>
            
    <div class='increasing-sales-form'>
        <form action="<?= $action ?>" id="increasing-sales-send-data" method="post">

            <div class="increasing-sales-form__field">

                <label for="product">
                    <?= $translator->translate('increasing_sales.form.product') ?>
                </label>

                <div class="wrapper">
                <select id="product" name="wpi_increasing_sales_offers[product_id]" required >
                    <option <?php echo $product_id ? '' : 'selected'; ?> value> -- <?= $translator->translate('increasing_sales.form.select_option') ?> -- </option>
                    <?php
                    foreach($products_not_assigned_to_the_offer as $product) {
                        foreach ($product as $key => $value) {
                            $selected_product = ($product_id === (string)$key) ? 'selected' : '';
                            echo "<option value='" . $key . "' " . $selected_product . ">" . $value . "</option>";
                        }
                    }
                    ?>
                </select>
                </div>

                <label for="offer_type">
                    <?= $translator->translate('increasing_sales.form.offer_type') ?>
                </label>

                <div class="wrapper">
                    <select id="offer_type" name="wpi_increasing_sales_offers[offer_type]" required >
                        <option disabled <?php echo $offer_type ? '' : 'selected'; ?> value> -- <?= $translator->translate('increasing_sales.form.select_option') ?> -- </option>
                        <?php
                        foreach (Increasing_Sales_Offer_Type::VALID_OFFER_TYPE as $offer) {
                            $selected_offer_type = ($offer_type === $offer) ? 'selected' : '';
                            echo "<option value='" . $offer . "' ".$selected_offer_type.">" . $translator->translate('increasing_sales.event.' . $offer) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <label for="offered_product">
                    <?= $translator->translate('increasing_sales.form.offered_product') ?>
                </label>

                <div class="wrapper">
                    <select id="offered_product" name="wpi_increasing_sales_offers[offered_product_id]" required >
                        <option disabled <?php echo $offered_product_id ? '' : 'selected'; ?> value> -- <?= $translator->translate('increasing_sales.form.select_option') ?> -- </option>
                        <?php
                        foreach($products as $product) {
                            foreach ($product as $key => $value) {
                                $selected_product = ($offered_product_id === (string)$key) ? 'selected' : '';
                                echo "<option value='" . $key . "' " . $selected_product . ">" . $value . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <label for="title">
                    <?= $translator->translate('increasing_sales.form.title') ?>
                </label>

                <div class="wrapper">
                    <input type="text" id="title" name="wpi_increasing_sales_offers[title]" value="<?= $fields->title ?? '' ?>">
                </div>

                <label for="description">
                    <?= $translator->translate('increasing_sales.form.description') ?>
                </label>

                <div class="wrapper">
                    <textarea id="description" name="wpi_increasing_sales_offers[description]"><?= $fields->description ?? '' ?></textarea>
                </div>

                <label for="picture">
                    <?= $translator->translate('increasing_sales.form.image') ?>
                </label>

                <div class="wrapper">
                    <?php
                    if($image_url){
                        $button_translation = $translator->translate('increasing_sales.form.change_image');
                        $display_image_preview = '';
                        $display_info_no_image = 'display:none;';
                    }else{
                        $button_translation = $translator->translate('increasing_sales.form.choose_image');
                        $display_image_preview = 'display:none;';
                        $display_info_no_image = '';
                    } ?>

                    <div id="info-no-image" style="<?= $display_info_no_image ?>"><?= $translator->translate('increasing_sales.form.no_image') ?></div>
                    <img id="image-preview" class="image-browse" style="cursor:pointer; max-width:390px; <?= $display_image_preview ?>" src="<?= $image_url ?>" alt="">

                    <br>
                    <input class="regular-text image-browse-url" type="hidden" name="wpi_increasing_sales_offers[image_url]" value="<?= $image_url ?>">

                    <input type="button" id="delete-image" class="button delete-image" style="display:inline; <?= $display_image_preview ?>" value="<?= $translator->translate('increasing_sales.form.remove_image') ?>">
                    <input type="button" id="image-browse" class="button image-browse" style="display:inline;" value="<?= $button_translation ?>">
                </div>

                <div style="max-width:390px; padding:5px; background-color: #D9EDF7; border: 1px solid #BCE8f1;color: #31708F; border-radius: 4px;">
                    <?= $translator->translate('increasing_sales.form.discount.warning') ?>
                </div>
                 <br>

                <label for="discount">
                    <?= $translator->translate('increasing_sales.form.discount') ?>
                </label>

                <div class="wrapper">
                    <input type="number" id="discount" min="0.01" step="0.01" max="999999" oninput="if(value.length>6)value=value.slice(0,6)" name="wpi_increasing_sales_offers[discount]" value="<?= $fields->discount ?? '' ?>"> <?= $currency ?>
                </div>

             </div>

            <div class='increasing-sales-form__footer'>
                <input class='wpi-button wpi-button--main offer-save-button' type='submit' name='' value='<?= $translator->translate('increasing_sales.form.save') ?>'>

                <a href='<?= $url_increasing_sales_page ?>' class='offer-cancel-button'><?= $translator->translate('increasing_sales.form.cancel') ?></a>

                <br class='clear' />
            </div>
            <input type="hidden" name="wpi_increasing_sales_offers[id]" value="<?= $fields->id_offer ?? '' ?>">
            <input type="hidden" name="wpi_increasing_sales_offers[redirect_increasing_sales_page]" value="<?= $url_increasing_sales_page ?>">
            <?= Nonce_Handler::get_field() ?>
        </form>

  </div>

</div>

<script>
    jQuery( document ).ready( function ( $ ) {

        $("#product").change(function() {
            let id = $(this).val();
            $("#offered_product option").show();
            $("#offered_product option[value='"+id+"']").hide();
        });

        $( '.delete-image' ).on( 'click', function( e ) {
            e.preventDefault();

            $('.image-browse-url').val('');
            $('#info-no-image').show();
            $('#image-preview').hide();
            $('.delete-image').hide();
        } );

        $( '.image-browse' ).on( 'click', function( e ) {
            e.preventDefault();

            var self = $( this );

            var file_frame = wp.media.frames.file_frame = wp.media( {
                title: self.data( 'uploader_title' ),
                button: {
                    text: self.data( 'uploader_button_text' ),
                },
                multiple: false,
                library: {
                    type: 'image'
                },
            } );

            file_frame.on( 'select', function () {
                var attachment = file_frame.state().get( 'selection' ).first().toJSON();

                $('.image-browse-url').val( attachment.url );

                $('#info-no-image').hide();
                $('.delete-image').show();
                $('#image-preview').show().attr('src',attachment.url);
                self.val('<?= $translator->translate('increasing_sales.form.change_image') ?>')
            } );

            file_frame.open();
        } );

        let form = $('form'),
            original = form.serialize();

        form.submit(function(){
            $('.offer-save-button').prop('disabled', true).val('<?= $translator->translate('increasing_sales.form.while_saving') ?>');
            window.onbeforeunload = null
        })

        window.onbeforeunload = function(){
            if (form.serialize() !== original) {
                return '';
            }
        }

    } );

</script>
