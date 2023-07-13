<?php
use bpmj\wpidea\helpers\Translator_Static_Helper;

/** @var array $categories */
?>

<div class="box_glowna_kategorie">
    <p><?= count($categories) === 1 ? Translator_Static_Helper::translate('product.item.category') : Translator_Static_Helper::translate('product.item.categories') ?></p>

    <ul>
        <?php foreach ($categories as $category ): ?>
            <li><a href="<?= $category->getLink() ?>">
                <?= $category->getName() ?>
            </a></li>
        <?php endforeach; ?>
    </ul>
</div>