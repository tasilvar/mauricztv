<?php

use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;
use bpmj\wpidea\translator\Interface_Translator;

/** @var string $id */
/** @var string $title */
/** @var string $save_configuration_group_fields_url */
/** @var Interface_Translator $translator */
/** @var Additional_Fields_Collection $additional_settings_fields */
?>

<div class="configuration-settings-content wpi-popup__core">
        <h2><?= $title ?></h2>

        <form id="form-<?= $id ?>">
            <?php
            foreach($additional_settings_fields as $field){
                echo $field->render_to_string();
            }
            ?>
        </form>

  <br><br><br>
</div>
<div class="wpi-popup__footer">
    <button type="button" id="close-<?= $id ?>" class="wpi-button wpi-button--secondary" data-close-popup-on-click><?= $translator->translate('settings.popup.button.cancel') ?></button>
    <button type="button" id="save-<?= $id ?>" class="wpi-button wpi-button--main"><?= $translator->translate('settings.popup.button.save') ?></button>
</div>

<script>
    jQuery( document ).ready( function ( $ ) {
        const groupFieldsSaveButton = $( '#save-<?= $id ?>' );
        const groupFieldsCloseButton = $( '#close-<?= $id ?>' );

        const getFormData = () => {
            let data_form = $('#form-<?= $id ?>').serializeArray();

            /* Because serializeArray() ignores unset checkboxes and radio buttons: */
            data_form = data_form.concat(
                jQuery('#form-<?= $id ?>' + ' input[type=checkbox]:not(:checked)').map(
                    function () {
                        return {'name': this.name, 'value': 'off'}
                    }).get()
            );

            data_form.forEach((item, index) => {
                if(item.name.indexOf('[]') === -1) {
                    return;
                }

                let nameWithoutBrackets = item.name.replace('[]', '');
                let itemWithNameWithoutBrackets = data_form.find(item => item.name === nameWithoutBrackets);

                if(typeof itemWithNameWithoutBrackets === 'undefined') {
                    data_form.push({
                        name: nameWithoutBrackets,
                        value: [item.value]
                    });
                    return;
                }

                let indexOfItemWithNameWithoutBrackets = data_form.findIndex(item => item.name === nameWithoutBrackets);

                data_form[indexOfItemWithNameWithoutBrackets].value.push(item.value);
            })

            return data_form;
        }
        let initial_data_form = getFormData();

        const hideAllFieldErrors = () => {
            let fieldWrapper = $('.configuration-settings-content .single-field-wrapper');

            fieldWrapper.removeClass('has-errors');
            let errorsNode = fieldWrapper.find('.validation-errors')

            errorsNode.text('');
            errorsNode.hide();
        }

        const showFieldErrors = (fieldName, validationErrors) => {
            let fieldWrapper = $('.single-field-wrapper[data-related-field="' + fieldName + '"]');

            fieldWrapper.addClass('has-errors');
            let errorsNode = fieldWrapper.find('.validation-errors')

            errorsNode.text(validationErrors.join(' '));
            errorsNode.show();
        }

        const ajaxQuery = (buttonSave, cbAfterSave = false) =>{
            let url = settings_tab.save_configuration_group_fields_url;

            if(typeof tinymce !== 'undefined') {
                tinymce.editors.forEach((e) => {
                    e.save();
                });
            }

            $.ajax({
                method: "POST",
                url: url,
                data: {
                    fields_value: getFormData()
                },
                dataType: "html"
            })
                .success(res => {
                    try {
                        res = JSON.parse(res);
                    } catch (e) {
                        // @todo: pokazac snackbar?
                        return;
                    }

                    // restore button label
                    $(buttonSave).prop('disabled', false).html('<?= $translator->translate('settings.popup.button.save') ?>');

                    // show field errors
                    const validationErrors = res.validation_errors ?? [];
                    const validationErrorsFieldNames = Object.keys(validationErrors);
                    if (validationErrorsFieldNames.length > 0) {
                        validationErrorsFieldNames.forEach(function (fieldName) {
                            showFieldErrors(fieldName, validationErrors[fieldName]);
                        })
                        return;
                    }

                    hideAllFieldErrors();
                    const successMessage = res.message ?? '';
                    if(successMessage.length > 0) {
                        window.snackbar.show(successMessage)
                    }

                    if(cbAfterSave !== false){
                    	cbAfterSave();
                    }
                });
        }

		const closePopup = () => {
            const wpiPopup = $('.wpi-popup');
            wpiPopup.hide();
            wpiPopup.removeClass('open');
            wpiPopup.removeClass('ajax-content-loaded');
		}

        groupFieldsSaveButton.on( 'click', function(e) {
            $(this).prop('disabled', true).html('<?= $translator->translate('settings.popup.button.saving') ?>');
            ajaxQuery(this, closePopup);
        });

        groupFieldsCloseButton.on( 'click', function(e) {
            initial_data_form.forEach((fieldForm) => {
                const field = $('#' + fieldForm.name);

                let fieldInitVal = field.data('initial-value') ;
                let wpEditor = (typeof tinymce !== 'undefined') ? tinymce.get(""+fieldForm.name+"") : null;
                let isCheckbox = field.is(':checkbox');

                if(wpEditor) {
                    wpEditor.render();
                } else if(isCheckbox) {
                    if(fieldInitVal) {
                        field.prop('checked', true)
                    } else {
                        field.prop('checked', false)
                    }
                } else {
                    field.val(fieldInitVal);
                }

                hideAllFieldErrors();
            });
        });
        
        <?php if('integration_wp-wfirma-additional-settings-popup' == $id): ?>
        
        const wFirmaDoRedirection = () => {
			var url = $('#wfirma_wf_oauth2_button_redir').attr('data-data');
			url = url.replaceAll('{client_id}', $('#wfirma_wf_oauth2_client_id').val().trim());
			window.location = url;        
        }
        
        $('#wfirma_wf_oauth2_button_redir').on('click', function () {
			ajaxQuery(groupFieldsSaveButton, wFirmaDoRedirection);
			return false;
		});
        
		<?php endif; ?>
        
    });
</script>