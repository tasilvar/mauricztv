<?php
/* @var string $video_page_url */
/* @var string $docs_url */
?>
<div class='notice notice-error bpmj-wpi-notice video-changes-info-notice'>
    <span class='dashicons dashicons-video-alt2'></span>
    <p>
        <strong>Uwaga! Jeśli chcesz przesłać pliki wideo do platformy, skorzystaj z zakładki <a href="<?= $video_page_url ?>">Wideo</a>.</strong>
        Możesz także zapoznać się z wpisem w naszej bazie wiedzy, który objaśnia wdrożone zmiany dotyczące obsługi wideo - <a href="<?= $docs_url ?>">Zmiany w przesyłaniu wideo</a>.
    </p>
</div>

<style>
    .video-changes-info-notice {
        display: flex;
        align-items: center;
        padding: 0;
        color: #842029;
        background-color: #f8d7da;
        border-color: #f5c2c7;
        border-left-color: #842029;
    }

    .video-changes-info-notice p strong:first-child {
        font-size: 1.2rem;
        display: block;
    }

    .video-changes-info-notice span.dashicons {
        margin: 0 20px;
    }
</style>