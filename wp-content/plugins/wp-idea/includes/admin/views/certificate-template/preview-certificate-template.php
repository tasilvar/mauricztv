<p style="text-align: center; margin-left: 40px; font-size: 40px"><?= __('PDF generation is in progress', BPMJ_EDDCM_DOMAIN) ?><br /> <?= __('Please wait...', BPMJ_EDDCM_DOMAIN) ?></p>
<div style="display: none">
    <div id="generate-pdf-and-close"  style="all:initial; display: flex">
        <?php echo html_entity_decode($certificate->get_page_replacement()); ?>
    </div>
</div>

