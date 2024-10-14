<?php
use bpmj\wpidea\translator\Interface_Translator;

/** @var Interface_Translator $translator */
/** @var string $second_step_label */

?>

<section class='navigation text-center animated fadeInUp'>

    <h4 class='question'><?php _e('Are you ready for next step?', BPMJ_EDDCM_DOMAIN); ?></h4>
    <button type="button" class="btn-eddcm btn-eddcm-default" data-action="previous-step"
            style="display: none;"><?php _e('Back', BPMJ_EDDCM_DOMAIN); ?></button>
    <button type="button" class="btn-eddcm btn-eddcm-primary btn-eddcm-big"
            data-action="next-step"><?= $second_step_label ?></button>

</section>