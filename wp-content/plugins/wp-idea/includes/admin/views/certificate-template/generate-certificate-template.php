<h2 style=" text-align: center; font-size: 30px; ">
    <?= __('Generating a certificate', BPMJ_EDDCM_DOMAIN) ?> <br/><br/>  <?= __('Please wait...', BPMJ_EDDCM_DOMAIN) ?>
</h2>
<div style="display: none">
    <div id="generate-pdf-and-close"  style="all:initial; display: flex">
        <?php echo html_entity_decode($certificate->get_page_replacement()); ?>
    </div>
</div>
