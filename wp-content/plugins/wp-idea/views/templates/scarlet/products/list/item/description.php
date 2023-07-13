<?php
/** @var string $excerpt */
?>

<div itemprop="description" class="box_glowna_opis edd_download_excerpt">
    <?= $excerpt ?>
    <?php if ($show_read_more_button): ?>
        <p class="text-right">
            <a href="<?= $product_link ?>"><?= __('Read more', BPMJ_EDDCM_DOMAIN) ?></a>
        </p>
    <?php endif; ?>
</div>
