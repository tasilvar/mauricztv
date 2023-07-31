<?php 
use bpmj\wpidea\Course_Progress;
use bpmj\wpidea\Courses;
use bpmj\wpidea\courses\core\entities\Course_Structure;

use bpmj\wpidea\resources\Resource_Type;
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\wolverine\user\User;

use bpmj\wpidea\wolverine\product\Product;
//use bpmj\wpidea\wolverine\product\course\Product;

global $post;
?>

<!-- <?php //WPI()->templates->header(); ?> -->

<?php
//echo "TEST TYPE : ".$post->post_type;

// if(is_archive()) { 
// 	echo "LISTA";
// }
// if(is_single()) { 
// 	echo "PROD";
// }
?>
<?php 
    //echo "POST: ".$post->ID;
    //print_r($post);
    //echo "PAGE: ".get_page_template();
    //exit(); 
	//echo "TYP: ".$post->post_type;
	/**
	 * Dołącz HEAD
	 */
	WPI()->templates->header();
    /**
	 * Jeśli typ widoku to produkt
	 */
	if(($post->post_type == 'download') && (is_single())) {
		include(__DIR__.'/product.php');
	}
	  /**
	   * Jeśli typ widoku to lista produktów
	   */ 
	 else if(($post->ID == 56) || (is_archive())) {

		include(__DIR__.'/products-list.php');
	  } 
	  /**
	   * Dla kazdego innego widoku zwróć szablon publigo
	   */
	  else {
?>
<div id="content" class="<?= apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?> wpi-template">
    <?php if(WPI()->templates->should_show_user_notice()): ?>
        <div class="notice-on-login">
            <div class="notice-content">
                <?php echo WPI()->templates->get_user_notice_content(); ?>
            </div>
        </div>
    <?php endif; ?>

    <?= !empty($content) ? $content : '' ?>
</div>
<?php 
	  } 
?>
<?php 
echo get_footer();

#WPI()->templates->footer(); ?>
