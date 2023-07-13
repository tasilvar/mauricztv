<?php

use \bpmj\wpidea\admin\helpers\html\Popup;

/** @var string $upload_url */
/** @var string $go_back_url */
/** @var string $placeholder_text */
/** @var string $invalid_file_type_text */
/** @var string $file_too_big_text */
/** @var string $upload_still_in_progress_text */
/** @var string $go_back_text */
/** @var string $header_text */
/** @var string $accepted_files */
/** @var int $max_file_size_in_mb */
/** @var string $storage_not_enough_space_popup_content */
/** @var string $response_error_text */

?>

<script src='https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js'></script>
<link href='https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css' rel='stylesheet' type='text/css'/>

<div class='wrap video-uploader-page'>
    <hr class='wp-header-end'>

    <h1 class='wp-heading-inline'><?= $header_text ?></h1>

    <div id='video-uploader-drop-zone' class='dropzone video-uploader-drop-zone'>
    </div>

    <a href="<?= $go_back_url ?>" class="go-back-to-videos-page">
        <img src="<?= BPMJ_EDDCM_URL . 'assets/imgs/go-back-icon.svg' ?>" alt='' /> <?= $go_back_text ?>
    </a>
</div>

<?php
echo Popup::create('bunnynet-video-storage-not-enough-space-popup', $storage_not_enough_space_popup_content)->get_html();
?>

<script>
    let myDropzone = new Dropzone('.video-uploader-drop-zone', {
        url: '<?= $upload_url ?>',
        maxFilesize: '<?= $max_file_size_in_mb ?>',
        parallelUploads: 1,
        clickable: true,
        dictDefaultMessage: '<?= $placeholder_text ?>',
        dictInvalidFileType: '<?= $invalid_file_type_text ?>',
        dictFileTooBig: '<?= $file_too_big_text ?>',
        acceptedFiles: '<?= $accepted_files ?>',
        dictResponseError: '<?= $response_error_text ?>',
    });

    myDropzone.on('error', (file, error) => {
        let msg = '';
        try {
            msg = JSON.parse(error).error;
        } catch (e) {
            msg = error;
        }

        const popup = jQuery('#bunnynet-video-storage-not-enough-space-popup');
        popup.find('.media-warning-content-replace').text(msg);
        popup.trigger('open');
        jQuery('.dz-error-message').remove();
    });

    jQuery(window).on('beforeunload', function ($) {
        if (myDropzone.getUploadingFiles().length > 0) {
            return '<?= $upload_still_in_progress_text ?>';
        }
    });
</script>


<style>
    .video-uploader-drop-zone {
        background: #FFFFFF;
        border: 2px dashed #C4C4C4;
        border-radius: 4px;
        text-align: center;
        padding: 7rem 1rem;
        margin-top: 15px;
    }

    .dz-default.dz-message {
        font-size: 1rem;
        font-weight: bold;
        color: #37484F;
    }

    .dropzone.dz-drag-hover {
        border-color: #1ABC9C;
    }

    .video-uploader-drop-zone span {
        display: block;
    }

    a.go-back-to-videos-page {
        float: right;
        font-size: 16px;
        background: #fff;
        border: 2px solid #E7EAEA;
        padding: 6px 30px 8px;
        display: flex;
        align-items: center;
        border-radius: 4px;
        font-weight: 700;
        color: #37484F;
        line-height: 28px;
        cursor: pointer;
        text-decoration: none;
        margin-top: 20px;
    }

    a.go-back-to-videos-page img {
        margin-right: 15px;
        margin-bottom: -2px;
    }
</style>