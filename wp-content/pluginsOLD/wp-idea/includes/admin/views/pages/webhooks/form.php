<?php
use bpmj\wpidea\nonce\Nonce_Handler;

/** @var string $page_title */
/** @var string $action */
/** @var array $fields */
/** @var string $url_webhook_page */
/** @var array $webhook_event_types */
/** @var Interface_Translator $translator */

$status = $fields['status'] ?? '';
$type_of_event = $fields['type_of_event'] ?? '';
?>

<div class='wrap webhooks-page'>
    <hr class='wp-header-end'>

    <h1 class='wp-heading-inline'><?= $page_title ?></h1>
            
    <div class='webhooks-form'>
        <form action="<?= $action ?>" id="webhook-send-data" method="post">

            <div class="webhooks-form__field">

                <label for="type_of_event">
                    <?= $translator->translate('webhooks.column.type_of_event') ?>
                </label>

                <div class="wrapper">
                <select id="type_of_event" name="wpi_webhook[type_of_event]" required >
                    <option disabled <?php echo $type_of_event ? '' : 'selected'; ?> value> -- <?= $translator->translate('webhooks.form.select_option') ?> -- </option>
                    <?php
                    foreach($webhook_event_types as $event_types) {
                        foreach ($event_types as $key => $value) {
                            $selected_type_of_event = ($type_of_event === $key) ? 'selected' : '';
                            echo "<option value='" . $key . "' ".$selected_type_of_event.">" . $value . "</option>";
                        }
                    }
                    ?>
                </select>
                </div>

                <label for="url">
                    <?= $translator->translate('webhooks.column.url') ?>
                </label>

                <div class="wrapper">
                    <input type="url" id="url" name="wpi_webhook[url]" value="<?= $fields['url'] ?? '' ?>" placeholder="https://example.com" required >
                </div>

                <label>
                    <?= $translator->translate('webhooks.column.status') ?>
                </label>

                <div class="wrapper">
                    <input type="radio" id="status1" name="wpi_webhook[status]" value="1" <?php if($status === 1) echo 'checked'; ?> required > <label for="status1"><?= $translator->translate('webhooks.status.active') ?></label>

                    <input type="radio" id="status2" name="wpi_webhook[status]" value="0" <?php if($status === 0) echo 'checked'; ?> required > <label for="status2"><?= $translator->translate('webhooks.status.suspended') ?></label>
                </div>

             </div>

            <div class='webhooks-form__footer'>
                <input class='wpi-button wpi-button--main webhook-save-button' type='submit' name='' value='<?= $translator->translate('webhooks.form.save') ?>'>

                <a href='<?= $url_webhook_page ?>' class='webhook-cancel-button'><?= $translator->translate('webhooks.form.cancel') ?></a>

                <br class='clear' />
            </div>
            <input type="hidden" name="wpi_webhook[id]" value="<?= $fields['id_webhook'] ?? '' ?>">
            <input type="hidden" name="wpi_webhook[redirect_webhook_page]" value="<?= $url_webhook_page ?>">
            <?= Nonce_Handler::get_field() ?>
        </form>

  </div>

</div>
