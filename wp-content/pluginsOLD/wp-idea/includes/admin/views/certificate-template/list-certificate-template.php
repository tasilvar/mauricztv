<?php

use bpmj\wpidea\certificates\Certificate_Template;

?>
<table id="certificates" class="wp-list-table widefat fixed posts">
    <thead>
    <tr>
        <th class="name"><?= __('Template name', BPMJ_EDDCM_DOMAIN) ?></th>
        <th style=" text-align: right; padding-right: 75px; " class="action"><?= __('Actions', BPMJ_EDDCM_DOMAIN) ?></th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var Certificate_Template $certificate */
    foreach ($certificates as $certificate) { ?>
        <tr>
            <td><?= $certificate->get_name(); ?></td>
            <td style="text-align: right">
                <a class="btn-eddcm btn-eddcm-primary" target="_blank" href="<?= $certificate->get_preview_url() ?>" title="<?= __('Download a sample PDF', BPMJ_EDDCM_DOMAIN) ?>"><span class="dashicons dashicons-pdf"></span></a>
                <?php if( $certificate->get_is_default()){ ?>
                    <a class="btn-eddcm btn-eddcm-primary" href="#" title="<?= __('Default', BPMJ_EDDCM_DOMAIN) ?>"><span class="dashicons dashicons-saved"></span></a>
               <?php } else { ?>
                <a class="btn-eddcm btn-eddcm-primary set-default-cert-template" href="<?= WPI()->certificates_template_actions->get_set_default_url($certificate->get_id()) ?>" title="<?= __('Set as default', BPMJ_EDDCM_DOMAIN) ?>"><span class="dashicons dashicons-saved"></span></a>
                <?php } ?>

                <a class="btn-eddcm btn-eddcm-primary" href="<?= $certificate->get_edit_url() ?>" title="<?= __('Edit', BPMJ_EDDCM_DOMAIN) ?>"><span class="dashicons dashicons-edit"></span></a>
                <a class="btn-eddcm btn-eddcm-danger delete-certificate-template"
                   data-id="<?= $certificate->get_id() ?>"
                   title="<?= __('Delete', BPMJ_EDDCM_DOMAIN) ?>"
                   href="#"><span class="dashicons dashicons-trash"></span></a>
            </td>
        </tr>
    <?php } ?>
    </tbody>

</table>

<p>
    <a href="<?= Certificate_Template::get_add_url() ?>" class="button-secondary"><?= __('Add new template', BPMJ_EDDCM_DOMAIN) ?></a>
</p>
