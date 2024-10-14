<?php
use bpmj\wpidea\translator\Interface_Translator;

/** @var Interface_Translator $translator */
/** @var string $text */
/** @var string $link */
?>

<div class="top-bar">
    <a href="<?= $link ?>" class="top-bar__link"><?php if($link): ?><i class="fas fa-caret-left"></i>&nbsp;<?php endif; ?>
        <?= $text ?>
    </a>
</div>
