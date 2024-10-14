<?php

use bpmj\wpidea\View;
use bpmj\wpidea\View_Hooks;

/** @var array $product */
/** @var bool $show_only_my_courses */
/** @var string $default_view */
/** @var bool $show_pagination */
/** @var int $total_pages */
/** @var int $page */
/** @var string $description_category_page */
/** @var bool $show_only_my_courses_is_checked */
?>

<?php
if($description_category_page){
    echo '<div class="category-page-description">'.$description_category_page.'</div>';
}

?>

<?= View::get('list-view-switcher', [
    'show_only_my_courses' => $show_only_my_courses,
    'show_only_my_courses_is_checked' => $show_only_my_courses_is_checked,
]) ?>
<div class="glowna_boxy" data-default-view="<?= $default_view ?>"

    <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_PRODUCT_LIST_BY_PRODUCTS_ARRAY, $products); ?>>
    <div class="row">
        <?php foreach ($products as $key => $product): ?>
            <?= View::get('item/item', [
                'product' => $product
            ]) ?>
        <?php endforeach; ?>
    </div>
</div>

<?php if($show_pagination): ?>
    <?= View::get('pagination', [
        'total_pages' => $total_pages,
        'page' => $page,
        'previous_page' => ($page - 1 > 0) ? $page - 1 : null,
        'next_page' => ($page + 1 <= $total_pages) ? $page + 1 : null
    ]); ?>
<?php endif; ?>
