<?php
/** @var string $action */
/** @var string $data */
/** @var string $type */
/** @var string $classes */
/** @var string $text */
/** @var bool $has_popup */
/** @var string $popup_html */
/** @var string $dashicon */
/** @var bool $disabled */
?>

    <button type="button" data-action="<?= $action ?>" <?= $data ?> class="wpi-button wpi-button--<?= $type ?> <?= $classes ?> <?= $disabled ? 'wpi-button--disabled' : '' ?>">
        <?= $dashicon ?>
        <?= $text ?>
    </button>

<?php if($has_popup): ?>
    <?= $popup_html ?>
<?php endif; ?>
