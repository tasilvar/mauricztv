<div style=" position: fixed; background: white; z-index: 99; top: 0; bottom: 0; left: 0; right: 0; padding: 10% 0; text-align: center; font-size: 30px; ">
    <?= __('Generating a certificate', BPMJ_EDDCM_DOMAIN) ?> <br/>  <?= __('Please wait...', BPMJ_EDDCM_DOMAIN) ?>
</div>
<div style="display: none">
    <div id="generate-pdf-and-close"  style="all:initial; display: flex">
        <?php echo html_entity_decode($certificate->get_page_replacement()); ?>
    </div>
</div>
