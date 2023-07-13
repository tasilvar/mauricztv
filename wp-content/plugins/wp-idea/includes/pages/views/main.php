<?php WPI()->templates->header(); ?>
<div id="content" class="<?= apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?> wpi-template">
    <?php if(WPI()->templates->should_show_user_notice()): ?>
        <div class="notice-on-login">
            <div class="notice-content">
                <?php echo WPI()->templates->get_user_notice_content(); ?>
            </div>
        </div>
    <?php endif; ?>

    <?= !empty($content) ? $content : '' ?>
</div>
<?php WPI()->templates->footer(); ?>
