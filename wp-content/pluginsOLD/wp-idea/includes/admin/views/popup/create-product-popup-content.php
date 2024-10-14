<?php
use bpmj\wpidea\translator\Interface_Translator;

/** @var string $title */
/** @var string $form_id */
/** @var string $create_product_url */
/** @var Interface_Translator $translator */

?>
    <div class="create-editor-popup-content">
    <h2><?= $title ?></h2>

        <form id="form-<?= $form_id ?>">
        <div class="single-field-wrapper" data-related-field="name" >
            <label for="name" class="field-label">
                <?= $translator->translate($form_id.'.popup.field.name') ?>
            </label>

            <div class="single-input-wrapper">
                <input type="text" name="name" id="name" value="" placeholder="<?= $translator->translate($form_id.'.popup.field.name.placeholder') ?>" class="single-field" maxlength="200">
                <span class="validation-errors" style="display: none;"></span>
            </div>
        </div>

       <div class="single-field-wrapper" data-related-field="price" >
           <label for="price" class="field-label">
               <?= $translator->translate($form_id.'.popup.field.price') ?>
               <span class='field-tooltip'>
                <img src='<?= BPMJ_EDDCM_URL ?>assets/imgs/settings/tooltip-icon.svg' alt=''/>
                <span class='field-tooltip-text'><?= $translator->translate($form_id.'.popup.field.price.tooltip') ?></span>
            </span>
           </label>

           <div class="single-input-wrapper">
               <input style="width:55%;" type="number" name="price" id="price" value="" placeholder="<?= $translator->translate($form_id.'.popup.field.price.placeholder') ?>" class="single-field" step="0.01" min="1">
               <span class="validation-errors" style="display: none;"></span>
           </div>
       </div>
        </form>
    </div>

    <div class="wpi-popup__footer">
          <button type="button" id="close-<?= $form_id ?>" class="service-popup-editor wpi-button wpi-button--secondary" data-close-popup-on-click><?= $translator->translate('product_editor.popup.button.cancel') ?></button>
          <button type="button" id="save-<?= $form_id ?>" class="wpi-button wpi-button--main"><?= $translator->translate('product_editor.popup.button.save') ?></button>
    </div>

<script>
    jQuery(document).ready(function($) {

        let popupFooter = $('.wpi-popup').find('.wpi-popup__footer');
        $(popupFooter[2]).css("display", "none");

        const closeButton = $( '#close-<?= $form_id ?>' );
        const saveButton = $( '#save-<?= $form_id ?>' );

        closeButton.on( 'click', function(e) {
            const wpiPopup = $(this).closest('.wpi-popup');
            if(wpiPopup.length === 0){
                return;
            }
            wpiPopup.remove()
        });

        saveButton.on( 'click', function(e) {
            $(this).prop('disabled', true).html('<?= $translator->translate('product_editor.popup.button.saving') ?>');
            ajaxQuery(this);
        });

        const ajaxQuery = (buttonSave) =>{
            let url = "<?= $create_product_url ?>";

            let name = $('#name').val();
            let price = $('#price').val();

            $.ajax({
                method: "POST",
                url: url,
                data: {
                    name: name,
                    price: price
                },
                dataType: "html"
            })
                .success(res => {
                    try {
                        res = JSON.parse(res);
                    } catch (e) {
                        return;
                    }

                    hideAllFieldErrors();

                    const validationErrors = res.validation_errors ?? [];
                    const validationErrorsFieldNames = Object.keys(validationErrors);

                    if (validationErrorsFieldNames.length > 0) {
                        enabledButtonSave(buttonSave);
                        validationErrorsFieldNames.forEach(function (fieldName) {
                            showFieldErrors(fieldName, validationErrors[fieldName]);
                        })
                        return;
                    }

                    const errorMessage = res.error ?? '';
                    if(errorMessage.length > 0) {
                        enabledButtonSave(buttonSave);
                        window.snackbar.show(errorMessage)
                    }

                    const successMessage = res.message ?? '';
                    if(successMessage.length > 0) {
                        window.location.href = successMessage
                    }
                });
        }

        const showFieldErrors = (fieldName, validationErrors) => {
            let fieldWrapper = $('.single-field-wrapper[data-related-field="' + fieldName + '"]');

            fieldWrapper.addClass('has-errors');
            let errorsNode = fieldWrapper.find('.validation-errors')

            errorsNode.text(validationErrors);
            errorsNode.show();
        }

        const hideAllFieldErrors = () => {
            let fieldWrapper = $('.create-editor-popup-content .single-field-wrapper');

            fieldWrapper.removeClass('has-errors');
            let errorsNode = fieldWrapper.find('.validation-errors')

            errorsNode.text('');
            errorsNode.hide();
        }
        const enabledButtonSave = (buttonSave) => {
           $(buttonSave).prop('disabled', false).html('<?= $translator->translate('product_editor.popup.button.save') ?>');
        }
    });
</script>