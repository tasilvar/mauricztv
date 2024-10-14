<?php
/** @var \bpmj\wpidea\view\Interface_View_Provider $view */

$title = __('Files', BPMJ_EDDCM_DOMAIN);
$hint = __('Add files in the desired order.', BPMJ_EDDCM_DOMAIN);
$next_step_button_label = __('Save product', BPMJ_EDDCM_DOMAIN);
$add_file_button_label = __('Add file', BPMJ_EDDCM_DOMAIN);
$select_file_button_label = __('Choose file', BPMJ_EDDCM_DOMAIN);
$selected_file_info_text = __('Selected file', BPMJ_EDDCM_DOMAIN);
$filename_placeholder_text = __('Enter a display name for the file (optional)', BPMJ_EDDCM_DOMAIN);
?>

<template class="digital-product-creator__files-list__item-template">
    <section class="digital-product-creator__files-list__item">
        <input class='regular-text digital-product-file-url' type='hidden'
               name='digital_product_file_urls[]'>
        <input class='regular-text digital-product-file-id' type='hidden'
               name='digital_product_file_ids[]'>
        <input class='regular-text digital-product-file-name' type='text' placeholder="<?= $filename_placeholder_text ?>"
               name='digital_product_file_names[]'>
        <input type='button' class='button add-file'
               value="<?= $select_file_button_label ?>">
        <div class="digital-product-file-url-info">
            <?= $selected_file_info_text ?>: <span></span>
        </div>

        <span class='dashicons dashicons-no-alt remove-file' data-action='remove'></span>
    </section>
</template>

<section class='step-two' style='display: none;'
         data-next-step-label="<?= $next_step_button_label ?>">
    <div class='row'>
        <div class='container'>
            <div class='panel'>
                <div class='panel-body'>
                    <h3><?= $title ?></h3>

                    <section class="digital-product-creator__files-list">

                    </section>

                    <p><?= $hint ?></p>
                    <hr>
                    <button type='button' class='btn-eddcm btn-eddcm-primary' data-action='add-digital-product-file'>
                        <?= $add_file_button_label ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<script type='text/javascript'>
    jQuery(document).ready(function ($) {
        $('button[data-action="add-digital-product-file"]').on('click', function (e) {
            const filesList = $('.digital-product-creator__files-list');
            const fileItemTemplate = $('.digital-product-creator__files-list__item-template').html();

            filesList.append(fileItemTemplate);
        });

        $('.digital-product-creator__files-list').on('click', '.remove-file', function (e) {
            $(this).parent('.digital-product-creator__files-list__item').remove();
        });

        $('.digital-product-creator__files-list').on('click', '.add-file', function (e) {
            e.preventDefault();

            const self = $(this);

            const file_frame = wp.media.frames.file_frame = wp.media({
                multiple: false
            });

            file_frame.on('select', function () {
                const attachment = file_frame.state().get('selection').first().toJSON();
                const selectedFileInfo = self.next('.digital-product-file-url-info');

                selectedFileInfo.find('span').text(attachment.url);
                selectedFileInfo.show();

                self.parent().find('.digital-product-file-url').val(attachment.url);
                self.parent().find('.digital-product-file-id').val(attachment.id);
            });

            file_frame.open();
        });
    });
</script>