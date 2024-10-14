<?php
/** @var int $module_id */
/** @var boolean $is_module_currently_viewed */
/** @var boolean $should_link */
/** @var boolean $show_expand_button */
/** @var \bpmj\wpidea\View $view */

$module_class = $is_module_currently_viewed ? 'active expanded' : 'other';
$module_title = get_the_title($module_id);
if($should_link) {
    $module_url = get_permalink($module_id);
    $module_title = "<a href='$module_url'>$module_title</a>";
}
?>

<p class="module-title module-title--<?= $module_class ?>">
    <i class="icon-module"></i><?= $module_title ?>

    <?php if($show_expand_button): ?>
        <?= $view::get('expand-button', ['module_id' => $module_id, 'is_expanded' => $is_module_currently_viewed]) ?>
    <?php endif; ?>
</p>
