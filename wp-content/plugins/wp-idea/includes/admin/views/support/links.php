<?php
use bpmj\wpidea\admin\support\Links;
?>

<h4 class="support__title support__title--small"><?php

_e( 'Helpful links', BPMJ_EDDCM_DOMAIN ); ?></h4>

<a href="<?= Links::DOCS ?>" target="_blank" class="support__docs-link">
    <div class="support__docs-link-wrap">
        <div class="dashicons dashicons-media-document support__docs-link__icon"></div>
        <span><?php _e( 'Browse LMS Idea wiki', BPMJ_EDDCM_DOMAIN ); ?></span>
    </div>
</a>