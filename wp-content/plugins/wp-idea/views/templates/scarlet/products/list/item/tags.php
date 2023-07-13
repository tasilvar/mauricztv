<?php
use bpmj\wpidea\helpers\Translator_Static_Helper;

/** @var array $tags */
?>

<div class="box_glowna_kategorie box_glowna_kategorie--tagi">
    <div class="box_glowna_tagi">
        <p><?= count($tags) === 1 ? Translator_Static_Helper::translate('product.item.tag') : Translator_Static_Helper::translate('product.item.tags') ?></p>


        <ul>
            <?php foreach ($tags as $tag): ?>
                <li><a href="<?= $tag->getLink() ?>">
                    #<?= $tag->getName() ?>
                </a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>