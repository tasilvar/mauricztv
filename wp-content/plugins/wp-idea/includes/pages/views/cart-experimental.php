<?php
WPI()->templates->experimental_cart_header(); ?>
<div id="content" class="<?= apply_filters('bpmj_eddcm_template_section_css_class', 'content'); ?> wpi-template experimental-cart">
    <?= !empty($content) ? $content : '' ?>
</div>

<footer id="experimental-cart-footer">
    <?php WPI()->templates->output_footer_html(true); ?>
</footer>
