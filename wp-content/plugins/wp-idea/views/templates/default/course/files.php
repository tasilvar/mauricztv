<?php
use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;
/** @var array $files */
/** @var int $lesson_page_id */
?>

<?php if (is_array($files)): ?>
    <div class="box">
        <h2 class="bg center"><?php _e( 'Files for download', BPMJ_EDDCM_DOMAIN ); ?></h2>
        <?php
        foreach ( $files as $fileID => $file ):
            ?>
            <div class="download <?php WPI()->templates->the_file_icon( $fileID ); ?>">
                <h3>
                    <a href="<?php echo bpmj_eddpc_encrypt_link( wp_get_attachment_url( $fileID ), $lesson_page_id ); ?>"
                       <?php if(!App_View_API_Static_Helper::is_active()){ ?> target="_blank" <?php } ?>><?php echo get_the_title( $fileID ); ?></a></h3>
                <p><?php echo $file[ 'desc' ]; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>