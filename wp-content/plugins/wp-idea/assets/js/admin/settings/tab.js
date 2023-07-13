jQuery(document).ready(function ($) {
    window.wpi_tab_save_data_checker = {
        unsavedData: [],

        init: function () {
            var _this = this;

            window.onbeforeunload = function (e) {
                if (_this.hasUnsavedData()) {
                    e.returnValue = settings_tab.unsaved_data_error;
                    return settings_tab.unsaved_data_error;
                }

                return undefined;
            }
        },

        addUnsavedData: function (name) {
            if (! this.unsavedData.includes(name)) {
                this.unsavedData.push(name)
            }
        },

        subtractUnsavedData: function (name) {
            this.unsavedData = this.unsavedData.filter(function (value) {
                return value !== name
            })
        },

        hasUnsavedData: function () {
            return this.unsavedData.length > 0
        },

        resetUnsavedData: function () {
            this.unsavedData = []
        },

        showAlert: function () {
            return window.confirm(settings_tab.unsaved_data_error)
        },
    }

    window.wpi_tab_save_data_checker.init()

    const settingsContent = $('.settings-content');

    let singleField, singleFieldWrapper, singleFieldSaveButton, singleFieldCancelButton;

    class GeneralInitializer {
        init = () => {
            this.initializeFields()
            this.initializeMediaFields();
        }
        initializeFields = () => {
            settingsContent.on('tab_loaded', function (e, tab) {
                (new SettingsFieldsHandler()).init();
                (new PopupHandler()).init();
            })
        }

        initializeMediaFields = () => {
            // handle media popup
            settingsContent.on('click', '.browse-media', function (event) {
                event.preventDefault();

                var self = $(this);

                // Create the media frame.
                var file_frame = wp.media.frames.file_frame = wp.media({
                    multiple: false
                });

                file_frame.on('select', function () {
                    let attachment = file_frame.state().get('selection').first().toJSON();

                    self.prev('.media-url').val(attachment.url);
                    self.prev('.media-url').trigger('input');
                    self.prev('.media-url').trigger('media-selected');
                });

                // Finally, open the modal
                file_frame.open();
            });
        }
    }

    (new GeneralInitializer()).init();

    class SettingsFieldsHandler {
        savingInProgress = false;
        savingQueue = [];


        init() {
            singleField = $('.single-field');
            singleFieldWrapper = $('.single-field-wrapper');
            singleFieldSaveButton = $('.single-field-save-button');
            singleFieldCancelButton = $('.single-field-cancel-button');

            this.hideOrShowConditionalFields();
            this.bindFieldActions();
            this.handleNavigationLabelSettingField();
            this.showHidePayuAdditionalFields();
        }

        hideOrShowConditionalFields() {
            $('[data-show-only-on-toggle-checked]').each((index, itemNone) => {
                let relatedFieldName = itemNone.dataset.showOnlyOnToggleChecked;
                let relatedFieldValue = this.getFieldValue(relatedFieldName);
                let item = $(itemNone);

                if(relatedFieldValue === 'off') {
                    item.hide();
                } else {
                    item.show();
                }
            });

            $('[data-show-only-on-toggle-not-checked]').each((index, itemNone) => {
                let relatedFieldName = itemNone.dataset.showOnlyOnToggleNotChecked;
                let relatedFieldValue = this.getFieldValue(relatedFieldName);
                let item = $(itemNone);

                if(typeof relatedFieldValue !== "undefined" && relatedFieldValue !== 'off') {
                    item.hide();
                } else {
                    item.show();
                }
            });

            $('[data-show-only-on-select-value]').each((index, itemNone) => {
                let relatedFieldName = itemNone.dataset.showOnlyOnSelectValue;
                let relatedFieldExpectedValue = itemNone.dataset.selectValue;
                let relatedFieldValue = this.getFieldValue(relatedFieldName);
                let item = $(itemNone);

                if(typeof relatedFieldValue !== "undefined" && relatedFieldValue !== relatedFieldExpectedValue) {
                    item.hide();
                } else {
                    item.show();
                }
            });

            $('[data-hide-only-on-select-value]').each((index, itemNone) => {
                let relatedFieldName = itemNone.dataset.hideOnlyOnSelectValue;
                let relatedFieldExpectedValue = itemNone.dataset.selectValue;
                let relatedFieldValue = this.getFieldValue(relatedFieldName);
                let item = $(itemNone);

                if (typeof relatedFieldValue !== "undefined" && relatedFieldValue !== relatedFieldExpectedValue) {
                    item.show();
                } else {
                    item.hide();
                }
            });
        }

        bindFieldActions() {
            let _this = this;

            // on field edit
            singleField.on('input', function (e) {
                let val = $(this).val();
                let initVal = $(this).data('initial-value');

                // skip for inputs in popup
                if ($(this).parents('.wpi-popup').length === 1) {
                    return;
                }

                // handle autosave for toggles
                const fieldName = $(this).attr('name');
                if (_this.fieldHasAutosave($(this))) {
                    _this.saveField(fieldName);
                    return;
                }

                if (val != initVal) {
                    window.wpi_tab_save_data_checker.addUnsavedData(fieldName)

                    _this.showFieldButtons(fieldName)
                } else {
                    window.wpi_tab_save_data_checker.subtractUnsavedData(fieldName)

                    _this.hideFieldButtons(fieldName)

                    _this.hideFieldErrors(fieldName);
                }
            });

            // save
            singleFieldSaveButton.on('click', function (e) {

                let fieldName = $(this).data('related-field')

                window.wpi_tab_save_data_checker.subtractUnsavedData(fieldName)

                _this.saveField(fieldName);
            });

            // cancel
            singleFieldCancelButton.on('click', function (e) {
                let fieldName = $(this).data('related-field')
                const field = $('#' + fieldName);
                let fieldInitVal = field.data('initial-value');

                window.wpi_tab_save_data_checker.subtractUnsavedData(fieldName)

                field.val(fieldInitVal);
                field.trigger('input')
            });
        }

        showHidePayuAdditionalFields() {
            $('select[name="payu_api_type"]').on('change', function () {

                const hiddenFieldsName = [
                    'payu_pos_auth_key',
                    'payu_key1',
                    'payu_return_url_failure',
                    'payu_return_url_success',
                    'payu_return_url_reports'
                ];

                if ('classic' === $(this).val()) {
                    hiddenFieldsName.map((fieldName) => {
                        $('.single-field-wrapper[data-related-field="' + fieldName + '"]').show();
                    });
                } else {
                    hiddenFieldsName.map((fieldName) => {
                        $('.single-field-wrapper[data-related-field="' + fieldName + '"]').hide();
                    });
                }

            }).trigger('change');
        }

        showFieldButtons(fieldName) {
            let fieldWrapper = $('.single-field-wrapper[data-related-field="' + fieldName + '"]');
            const fieldButtonsBox = fieldWrapper.find('.field-buttons');

            let fieldSave = fieldButtonsBox.find('.single-field-save-button')
            let fieldCancel = fieldButtonsBox.find('.single-field-cancel-button')

            fieldSave.show();
            fieldCancel.show();
        }

        hideFieldButtons(fieldName) {
            let fieldWrapper = $('.single-field-wrapper[data-related-field="' + fieldName + '"]');
            const fieldButtonsBox = fieldWrapper.find('.field-buttons');

            let fieldSave = fieldButtonsBox.find('.single-field-save-button')
            let fieldCancel = fieldButtonsBox.find('.single-field-cancel-button')

            fieldSave.hide();
            fieldCancel.hide();
        }

        hideFieldErrors(fieldName) {
            let fieldWrapper = $('.single-field-wrapper[data-related-field="' + fieldName + '"]');

            fieldWrapper.removeClass('has-errors');
            let errorsNode = fieldWrapper.find('.validation-errors')

            errorsNode.text('');
            errorsNode.hide();
        }

        showFieldErrors(fieldName, validationErrors) {
            let fieldWrapper = $('.single-field-wrapper[data-related-field="' + fieldName + '"]');

            fieldWrapper.addClass('has-errors');
            let errorsNode = fieldWrapper.find('.validation-errors')

            errorsNode.text(validationErrors.join(' '));
            errorsNode.show();
        }

        getFieldValue(fieldName) {
            let field = $('#' + fieldName);
            let fieldValue = field.val();
            let isCheckbox = field.is(':checkbox');
            let isChecked = field.is(':checked');

            if (isCheckbox) {
                if (!isChecked) {
                    fieldValue = 'off';
                }
            }

            return fieldValue;
        }

        saveField(fieldName) {
            this.hideFieldButtons(fieldName);
            this.setFieldAsSaving(fieldName);

            if(this.isAnySavingInProgress()) {
                this.addFieldToSavingQueue(fieldName);

                return;
            }

            this.handleAjaxFieldSave(fieldName)
        }

        handleAjaxFieldSave(fieldName) {
            this.handleOnAjaxSavingStartActions(fieldName);

            const fieldValue = this.getFieldValue(fieldName);
            let url = settings_tab.save_single_field_url;

            let _this = this;

            $.ajax({
                method: 'POST',
                url: url,
                data: {
                    name: fieldName,
                    value: fieldValue
                },
                dataType: 'html'
            })
                .success(res => {
                    try {
                        res = JSON.parse(res);
                    } catch (e) {
                        _this.setFieldAsInvalid(fieldName, [settings_tab.an_error_occurred]);
                        _this.handlePostSavingRequestActions(fieldName);
                        return;
                    }

                    let validationErrors = res.validation_errors ?? [];

                    if (validationErrors.length > 0) {
                        _this.setFieldAsInvalid(fieldName, validationErrors);
                        _this.handlePostSavingRequestActions(fieldName);
                        return;
                    }

                    let valueAfterUpdate = res[1] ?? fieldValue;

                    _this.setFieldAsSaved(fieldName, valueAfterUpdate);
                    _this.setNewFieldInitialValue(fieldName, valueAfterUpdate);
                    _this.handlePostSuccessfulSaveActions(fieldName, valueAfterUpdate);
                    _this.handlePostSavingRequestActions(fieldName);

                })
                .error(res => {
                    _this.setFieldAsInvalid(fieldName, [settings_tab.an_error_occurred]);
                    _this.handlePostSavingRequestActions(fieldName);
                });
        }

        setFieldAsSaving(fieldName) {
            let fieldWrapper = $('.single-field-wrapper[data-related-field="' + fieldName + '"]');
            let savingLabel = fieldWrapper.find('.saving');

            fieldWrapper.addClass('saving-in-progress');
            savingLabel.show();
        }

        setFieldAsSaved(fieldName, fieldValue) {
            let fieldWrapper = $('.single-field-wrapper[data-related-field="' + fieldName + '"]');
            let field = $('#' + fieldName);
            const fieldButtonsBox = fieldWrapper.find('.field-buttons');
            let saving = fieldButtonsBox.find('.saving');
            let saved = fieldButtonsBox.find('.saved');

            fieldWrapper.removeClass('saving-in-progress');
            saving.hide();
            saved.show();

            // update field value for non-toggles
            if(!this.fieldHasAutosave(field)) {
                field.val(fieldValue);
            }

            setTimeout(function () {
                saved.hide();
            }, 1500)

            this.hideFieldErrors(fieldName);

            field.trigger('field-successfully-saved');
        }

        setNewFieldInitialValue(fieldName, newValue) {
            $('#' + fieldName).data('initial-value', newValue);
        }

        setFieldAsInvalid(fieldName, validationErrors) {
            let fieldWrapper = $('.single-field-wrapper[data-related-field="' + fieldName + '"]');

            let fieldButtonsBox = fieldWrapper.find('.field-buttons');

            // hide saving indicators
            let saving = fieldButtonsBox.find('.saving');
            let saved = fieldButtonsBox.find('.saved');

            fieldWrapper.removeClass('saving-in-progress');
            saving.hide();
            saved.hide();

            this.showFieldButtons(fieldName);

            this.showFieldErrors(fieldName, validationErrors);
        }

        handleOnAjaxSavingStartActions(fieldName) {
            this.setSavingIsInProgress()
        }

        handlePostSuccessfulSaveActions(fieldName, fieldValue) {
            if (fieldName === 'license_key') {
                $.post(settings_tab.license_key_info_url, (res) => {
                    res = JSON.parse(res);
                    $('#license_status').html(res.html);
                });
            }

            this.hideOrShowConditionalFields();
        }

        handlePostSavingRequestActions(fieldName) {
            this.queueItemHandlingComplete(fieldName)
        }

        handleNavigationLabelSettingField = () => {

            $('.navigation-label-field input[type="radio"]').click(function (event) {
                if (event.target.value === 'other') {
                    event.target.parentElement.parentElement
                        .querySelector('input[type="text"]').removeAttribute('disabled')
                } else {
                    event.target.parentElement.parentElement
                        .querySelector('input[type="text"]').setAttribute('disabled', 'disabled')
                }
            })
        }

        isAnySavingInProgress() {
            return this.savingInProgress === true;
        }

        setSavingIsInProgress() {
            this.savingInProgress = true;
        }

        setSavingIsNotInProgress() {
            this.savingInProgress = false;
        }

        addFieldToSavingQueue(fieldName) {
            if(this.savingQueue.includes(fieldName)) {
                return;
            }

            this.savingQueue.push(fieldName);
        }

        queueItemHandlingComplete(fieldName) {
            this.setSavingIsNotInProgress();

            this.handleNextQueueItem()
        }

        handleNextQueueItem() {
            const nextItem = this.savingQueue.shift()

            if(typeof nextItem === "undefined") {
                return;
            }

            this.handleAjaxFieldSave(nextItem)
        }

        fieldHasAutosave(field) {
            return field.hasClass('autosave');
        }
    }

    class PopupHandler {
        init() {
            // popup
            $('button.wpi-button[data-action="open_popup"]').on('click', function () {
                var popup_id = $(this).data('popup-id');
                var popup = $('#' + popup_id);

                if (popup.length === 0) return;

                popup.trigger('open');
            });

            var wpiPopup = $('.wpi-popup');

            wpiPopup.on('open', function () {
                var _this = $(this);

                _this.find('.wpi-popup__content').html(_this.data('loading'));
                _this.show();
                _this.addClass('open');


                var timeout = _this.data('timeout');

                if (timeout) {
                    setTimeout(function () {
                        $('.wpi-popup.open').trigger('close');
                    }, timeout);
                }

                if (_this.data('type') !== 'ajax-popup') return;

                const data = {
                    action: _this.data('action')
                };

                const params = _this.data('params');

                $.ajax({
                    type: "GET",
                    data: $.extend({}, data, params),
                    url: ajaxurl,
                    success: function (response) {
                        _this.find('.wpi-popup__content').html(response.data);
                        _this.trigger('ajax-content-loaded');
                    }
                });

                this.handleTinymceModeToggle();

            });

            wpiPopup.on('close', function () {
                var _this = $(this);

                _this.hide();
                _this.removeClass('open');
                _this.removeClass('ajax-content-loaded');
            });

            wpiPopup.on('ajax-content-loaded', function () {
                var _this = $(this);

                _this.addClass('ajax-content-loaded');
            });

            wpiPopup.on('click', '[data-close-popup-on-click]', function () {
                $('.wpi-popup.open').trigger('close');
            });

            $('.wpi-popup__back_overlay').on('click', function () {
                $('.wpi-popup.open').trigger('close');
            });

            $(document).keyup(function (e) {
                if (e.key === "Escape") {
                    $('.wpi-popup.open').trigger('close');
                }
            });

            wpiPopup.on('click', '[data-loading]', function () {
                $(this).html($(this).data('loading'));
            });



        }
        
        handleTinymceModeToggle = () => {
            tinymce.editors.forEach((e) => {
                let wrapId = "#wp-" + e.id + "-wrap";
                $(wrapId).find('.switch-tmce').click(() => {
                    let visualActive = $(wrapId).hasClass('tmce-active');
                    if (visualActive) {
                        return;
                    }
                    tinymce.execCommand('mceToggleEditor', false, e.id);
                    $(wrapId).removeClass('html-active').addClass('tmce-active');
                });

                $(wrapId).find('.switch-html').click(() => {
                    let visualActive = $(wrapId).hasClass('tmce-active');
                    if (!visualActive) {
                        return;
                    }
                    tinymce.execCommand('mceToggleEditor', false, e.id);
                    $(wrapId).removeClass('tmce-active').addClass('html-active');
                });
            });
        }
    }
});
