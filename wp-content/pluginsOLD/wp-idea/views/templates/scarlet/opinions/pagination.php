<?php
/** @var int $previous_page */
/** @var int $total_pages */
/** @var int $page */
/** @var int $next_page */
/** @var string $opinions_page_param_name */

if($total_pages > 1){
?>
<div class="paginacja_boxy">
    <div class="contenter">
        <ul>
            <?php if($previous_page): ?>
                <li><a class="prev page-numbers" href="?<?= $opinions_page_param_name ?>=<?= $previous_page ?>#opinions"><i class="fa fa-angle-left"></i></a></li>
            <?php endif; ?>

            <?php for ( $i = 1; $i<=$total_pages; $i++ ): ?>
                <?php $page_link = '?'.$opinions_page_param_name.'='.$i.'#opinions'; ?>
                <?php if ( $i != $page ): ?>
                    <li>
                        <a href="<?= $page_link ?>" class="page-numbers">
                            <?= $i ?>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="active">
                        <a href="#opinions"><span aria-current="page" class="page-numbers current"><?= $i ?></span></a>
                    </li>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if($next_page): ?>
                <li><a class="prev page-numbers" href="?<?= $opinions_page_param_name ?>=<?= $next_page ?>#opinions"><i class="fa fa-angle-right"></i></a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php
}
?>