<?php
/** @var int $previous_page */
/** @var int $total_pages */
/** @var int $page */
/** @var int $next_page */
?>
<div class="paginacja_boxy">
    <div class="contenter">
        <ul>
            <?php
                //@todo: zamienic paginacje na liste obiektow (linków)
            ?>
            <?php if($previous_page): ?>
                <li><a class="prev page-numbers" href="<?= get_pagenum_link($previous_page) ?>"><i class="fa fa-angle-left"></i></a></li>
            <?php endif; ?>

            <?php for ( $i = 1; $i<=$total_pages; $i++ ): ?>
                <?php $page_link = get_pagenum_link($i); ?>
                <?php if ( $i != $page ): // not current page ?>
                    <li>
                        <a href="<?= $page_link ?>" class="page-numbers">
                            <?= $i ?>
                        </a>
                    </li>
                <?php else: // current page ?>
                    <li class="active">
                        <a href="#"><span aria-current="page" class="page-numbers current"><?= $i ?></span></a>
                    </li>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if($next_page): ?>
                <li><a class="prev page-numbers" href="<?= get_pagenum_link($next_page) ?>"><i class="fa fa-angle-right"></i></a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>