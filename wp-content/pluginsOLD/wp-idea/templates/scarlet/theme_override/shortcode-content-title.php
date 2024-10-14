
<?php

use bpmj\wpidea\View_Hooks;
?>
<div class="box_glowna_tytul">
    <a href="<?php the_permalink(); ?>" <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_HYPERLINK_PRODUCT, get_the_ID()); ?>>
      <h2>
         <?php the_title(); ?>
      </h2>
    </a>
</div>
