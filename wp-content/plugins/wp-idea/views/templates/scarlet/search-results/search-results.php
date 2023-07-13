<?php

use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\modules\search\core\value_objects\Search_Results_Collection;
use bpmj\wpidea\modules\search\core\value_objects\Search_Result;

/* @var Interface_View_Provider $view */
/* @var Interface_Translator $translator */
/* @var string $query */
/* @var Search_Results_Collection $results */
/* @var int $results_count */
?>

<div class="publigo-search">
    <div class='publigo-search__searchbar'>
        <form action='' method='get'>
            <input type='text' name='s' class='publigo-search__searchbar__input' value="<?= $query ?>"
                   placeholder='<?= $translator->translate('search_results.type_to_search') ?>' required/>
            <button type='submit' class='publigo-search__searchbar__button'>
                <i class='icon-search'></i>
                <?= $translator->translate('search_results.search') ?>
            </button>
        </form>
    </div>

    <div class='publigo-search__results'>
        <?php if(empty($query)): ?>
            <div class="publigo-search__results__placeholder">
                <p><?= $translator->translate('search_results.you_will_see_results_here') ?></p>
            </div>
        <?php endif; ?>

        <?php if(!empty($query) && ($results_count === 0)): ?>
            <div class="publigo-search__results__no-results">
                <p><strong><?= $translator->translate('search_results.no_search_results') ?></strong> <?= $translator->translate('search_results.no_search_results.try_other_phrase')  ?></p>
            </div>
        <?php endif; ?>

        <?php if(!empty($query) && ($results_count !== 0)): ?>
            <div class="publigo-search__results__results">
                <p><?= $translator->translate('search_results.results_count')  ?>: <strong><?= $results_count ?></strong></p>

                <ul>
                    <?php foreach($results as $result): ?>
                        <?php
                        /* @var Search_Result $result */
                        ?>
                        <li><a href="<?= $result->get_url()->get_value() ?>" class="publigo-search__results__results__result_link"><?= $result->get_title() ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


    </div>
</div>
