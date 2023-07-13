<?php

use bpmj\wpidea\View_Hooks;

/** @var string $link */
/** @var string $title */
/** @var int $product_id */

?>
<div class="box_glowna_tytul">
    <a href="<?= $link ?>" <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_HYPERLINK_PRODUCT, $product_id) ?> class="box_glowna_tytul_link">
      <h2 itemprop="name">
          <?= $title ?>
      </h2>
    </a>
    <?php
    bpmj_render_available_quantities_information($product_id);
    ?>
</div>
