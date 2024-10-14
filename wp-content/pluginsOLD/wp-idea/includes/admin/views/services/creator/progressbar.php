<?php
use bpmj\wpidea\translator\Interface_Translator;


/** @var Interface_Translator $translator */
/** @var bool $has_integrations */

$total_steps_number = $has_integrations ? 2 : 1;
?>

<section class="edd-courses-manager-creator-steps">
    <div class='container'>
        <ul class='progressbar progressbar--<?= $total_steps_number ?>-steps'>
            <li class='active' data-step='one'><?= $translator->translate('services.creator.step_name.details') ?></li>
            
            <?php if($has_integrations): ?>
            <li data-step="two"><?= $translator->translate('services.creator.step_name.integrations') ?></li>
            <?php endif ?>
        </ul>
    </div>
</section>