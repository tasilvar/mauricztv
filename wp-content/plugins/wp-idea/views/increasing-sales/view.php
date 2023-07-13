<?php
/** @var string $url_action */
/** @var null|string $product_image_url */
/** @var string $offered_product_lowest_price */
?>
<tr>
    <td colspan='1000' style='padding: 0; display:table-cell;'>
        <div class='publigo-cart-product-frame'>
            <div class='publigo-cart-product-frame__header'>
                <label for='special-offer-checkbox'>

                        <input type='checkbox' name='publigo_special_offer_{offer_type}' id='special-offer-checkbox'>
                        <input type="hidden" name="publigo_special_offer_id" value='{offer_id}'>

                    <span class='publigo-cart-product-frame__product_name'>{product_name}</span>
                    <span class='publigo-cart-product-frame__product_price'>{product_price}</span>
                </label>
            </div>
            <div class='publigo-cart-product-frame__body'>
                <?php if($product_image_url): ?>
                <div class='publigo-cart-product-frame__image'>
                    <img src="{product_image_url}" alt="">
                </div>
                <?php endif; ?>
                <div class='publigo-cart-product-frame__description'>
                    <label class="lowest_price_information">
                        <?php
                        echo $offered_product_lowest_price;
                        ?>
                    </label>
                    <?= $product_description ?? '' ?>
                </div>
            </div>
        </div>
    </td>
</tr>

<script>
    jQuery('document').ready(function ($){
        $('#special-offer-checkbox').on('change', function (e) {
            e.target.disabled = true;

            let data_form = $('#edd_checkout_cart_form').serializeArray();

            $.ajax({
                method: "POST",
                url: '<?= $url_action ?>',
                data: {
                    fields_value: data_form
                },
                dataType: "html"
            })
                .success(res => {
                    try {
                        res = JSON.parse(res);
                    } catch (e) {
                        return;
                    }

                    const successMessage = res.message ?? '';
                    if(successMessage.length > 0) {
                        $('#edd_checkout_cart_form').submit();
                    }

                });
        })
    })
</script>

<style>
    .publigo-cart-product-frame {
        border: 3px dashed var(--main-color);
        margin: 10px 0;
    }

    .publigo-cart-product-frame__header label {
        cursor:pointer;
        display: flex;
        align-items: center;
        padding: 20px 0px;
        margin: 0;
    }

    .publigo-cart-product-frame__header label input[type='checkbox'] {
        width: 20px;
        margin: 0 20px;
        height: 20px;
    }

    span.publigo-cart-product-frame__product_name {
        font-size: 16px;
        font-weight: bold;
    }

    span.publigo-cart-product-frame__product_price{
        margin: 0 0 0 auto;
        padding: 0 20px;
        white-space: nowrap;
        text-align: center;
    }

    span.publigo-cart-product-frame__product_price div{
        font-size: 18px;
        color:#6c7f90;
    }

    span.publigo-cart-product-frame__product_price .special-offer-price-crossed-out{
        text-decoration: line-through;
        padding: 0 5px;
        display: inline;
    }

    span.publigo-cart-product-frame__product_price .special-offer-price{
        display: inline;
    }

    @media (max-width: 768px){
        .publigo-cart-product-frame__header label {
            padding: 10px 0;
        }
        span.publigo-cart-product-frame__product_name {
            font-size: 14px;
        }
        span.publigo-cart-product-frame__product_price div{
            font-size:14px;
        }

        span.publigo-cart-product-frame__product_price .special-offer-sale-price{
            display:block;
        }
        span.publigo-cart-product-frame__product_price .special-offer-price{
            display:block;
        }
    }

    .publigo-cart-product-frame__header {
        border-bottom: 1px solid var(--cart-border-color);
        background: rgb(255 209 0 / 11%);
    }

    .publigo-cart-product-frame__body {
        padding: 20px;
        display: flex;
        flex-wrap: wrap;
    }

    .publigo-cart-product-frame__image {
        flex: 1;
        flex-grow: 3;
        margin-right: 20px;
    }

    .publigo-cart-product-frame__image img {
        margin: 0 !important;
    }

    .publigo-cart-product-frame__description {
        flex: 1;
        flex-grow: 7;
    }

    .publigo-cart-product-frame__description p {
        margin-top: 0;
    }

    .publigo-cart-product-frame .lowest_price_information {
        padding: 10px 10px;
        min-height: 40px;
    }
</style>