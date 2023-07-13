<?php
use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;
/** @var array $files */
/** @var int $lesson_page_id */
/** @var bool $visible_files_block */
/** @var bool $compact_mode_on */
/** @var \bpmj\wpidea\View $view */

if ($visible_files_block === true) {
    $app_view_is_active = App_View_API_Static_Helper::is_active();
?>
<!-- Pole z materiałami do pobrania -->
<?php if ( is_array( $files ) && !empty($files) ): ?>
    <a name="files"></a>
    <h3><?php _e( 'Files for download', BPMJ_EDDCM_DOMAIN ); ?></h3>
    <div class="pliki_do_pobrania">
        <?php if($compact_mode_on): ?>
            <?= $view::get('files-list-one-col', [
                'files' => $files,
                'lesson_page_id' => $lesson_page_id,
                'app_view_is_active' => $app_view_is_active
            ]) ?>
        <?php else: ?>
            <?= $view::get('files-list-two-cols', [
                'files' => $files,
                'lesson_page_id' => $lesson_page_id,
                'app_view_is_active' => $app_view_is_active
            ]) ?>
        <?php endif; ?>
    </div>
<?php 
      endif;
 }
?>
<!-- Koniec pola z materiałami do pobrania -->
