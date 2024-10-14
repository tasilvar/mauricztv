<?php
use bpmj\wpidea\nonce\Nonce_Handler;

/** @var string $page_title */
/** @var string $go_back_url */
/** @var string $action_url */
/** @var array $products */
/** @var \bpmj\wpidea\translator\Interface_Translator $translator */

?>

<div class='wrap affiliate-program-redirection-list-page'>
    <hr class="wp-header-end">

    <h1 class='wp-heading-inline'><?= $page_title ?></h1>

    <div class='add-redirection-link-form-wrapper'>
        <form action="<?= $action_url ?>" id='add-redirection-link-form' method='post'>

            <div class='add-redirection-link-form__field'>

                <label for='type_of_event'>
                    <?= $translator->translate('affiliate_program_redirections.column.product') ?>
                </label>

                <div class="wrapper">
                    <select id="type_of_event" name="redirection_link[product]" required>
                        <option disabled selected value> -- <?= $translator->translate(
                                'affiliate_program_redirections.actions.add.select_product'
                            ) ?> --
                        </option>
                        <?php
                        foreach ($products as $product_id => $product_name) {
                            echo "<option value='" . $product_id . "'>" . $product_name . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <label for="url">
                    <?= $translator->translate('affiliate_program_redirections.column.url') ?>
                </label>

                <div class="wrapper">
                    <input type="url" id="url" name="redirection_link[url]" value=""
                           placeholder="https://example.com" required>
                </div>

            </div>

            <div class='add-redirection-link-form__footer'>
                <input class='wpi-button wpi-button--main save-button' type='submit' name=''
                       value='<?= $translator->translate('affiliate_program_redirections.actions.add.save') ?>'>

                <a href='<?= $go_back_url ?>' class='cancel-button'><?= $translator->translate(
                        'affiliate_program_redirections.actions.add.cancel'
                    ) ?></a>

                <br class='clear'/>
            </div>
            <input type="hidden" name="redirection_link[go_back_url]" value="<?= $go_back_url ?>">
            <?= Nonce_Handler::get_field() ?>
        </form>


    </div>
