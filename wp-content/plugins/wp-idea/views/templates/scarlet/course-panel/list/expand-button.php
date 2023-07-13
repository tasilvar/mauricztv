<?php
/** @var int $module_id */
/** @var boolean $is_expanded */
?>
<button role="button" class="button-expand-module <?= $is_expanded ? 'expanded' : '' ?>" data-module-id="<?= $module_id ?>">
    <i class='fas fa-chevron-down'></i>
</button>