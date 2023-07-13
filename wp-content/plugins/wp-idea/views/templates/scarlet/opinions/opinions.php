<?php

use bpmj\wpidea\modules\opinions\core\collections\Opinion_Collection;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Rating;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

/* @var Interface_Translator $translator */
/* @var Interface_View_Provider $view */
/** @var string $template_path_base */
/* @var Opinion_Collection $results */
/** @var int $total_pages */
/** @var int $page */
/** @var string $opinions_page_param_name */
/** @var int $show_opinions_in_column */

$star_filled = '<span class="publigo-opinion-star dashicons dashicons-star-filled"></span>';
$star_empty = '<span class="publigo-opinion-star dashicons dashicons-star-empty"></span>';

$class_flex_container = 'flex-opinions-container-column';
$class_flex_column = 'flex-opinions-item-one-column';

if($show_opinions_in_column === 2){
    $class_flex_container = 'flex-opinions-container-row';
    $class_flex_column = 'flex-opinions-item-two-columns';
}
?>

<div class="publigo-opinions">
    <a name="opinions"></a>
    <h3>Opinie</h3>

    <?php
    if($results->is_empty()){
        echo $translator->translate('blocks.opinions.empty');
    }

    echo'<ul class="flex-opinions-container '.$class_flex_container.'">';
    foreach($results as $result){

        $user_full_name = trim($result->get_user_full_name());

        $user = !empty($user_full_name) ? explode(' ', $user_full_name) : [$translator->translate('blocks.opinions.empty.user')];

        echo'
            <li class="'.$class_flex_column.'">
             <div class="header">';

             for ($i = 1; $i <= $result->get_rating()->get_value(); $i++) {
                 echo $star_filled;
              }

              for ($i = 1; $i <= (Opinion_Rating::MAX - $result->get_rating()->get_value()); $i++) {
                 echo $star_empty;
              }

        echo '<p class="signature">' . $user[0] . ' <span class="date">' . $result->get_date_of_issue()->format('d.m.Y') . '</span></p>';

        echo'</div>
             <div class="content">
                ' .$result->get_opinion_content()->get_value(). '
             </div>
           </li>
          ';
    }
    echo'</ul>';
    ?>

        <?= $view->get($template_path_base . '/opinions/pagination', [
            'total_pages' => $total_pages,
            'page' => $page,
            'previous_page' => ($page - 1 > 0) ? $page - 1 : null,
            'next_page' => ($page + 1 <= $total_pages) ? $page + 1 : null,
            'opinions_page_param_name' => $opinions_page_param_name
        ]) ?>
</div>
