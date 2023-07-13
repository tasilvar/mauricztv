<?php
use \bpmj\wpidea\wolverine\course\progress\events\CheckPreviousLessonAsDone;
?>
<?php if (is_null( CheckPreviousLessonAsDone::$finished ) ) : ?>
    <?php return; ?>
<?php endif; ?>

<?php
$toast_text = __(
        'The previous lesson has been automatically marked as completed. If you want to undo this mark, click <a href="#" class="check-previous-lesson-as-undone"><span class="text">here</span></a>&nbsp;',
        BPMJ_EDDCM_DOMAIN
    ) .
    '<span class="check-previous-lesson-as-undone-loader" style="display: none;">(' . __(
        'Wait...',
        BPMJ_EDDCM_DOMAIN
    ) . ')</span>' .
    '<span class="check-previous-lesson-as-undone-done" style="display: none;">(' . __(
        'Done',
        BPMJ_EDDCM_DOMAIN
    ) . ')</span>' .
    '<span class="check-previous-lesson-as-undone-fail" style="display: none;">(' . __(
        'Something went wrong, please try again',
        BPMJ_EDDCM_DOMAIN
    ) . ')</span>.';
?>

<script>
    window.showToast('<?= $toast_text ?>');

    jQuery('.check-previous-lesson-as-undone').on('click', function (e) {
        e.preventDefault();

        jQuery('.check-previous-lesson-as-undone-loader').css('display', 'inline');

        var ajaxurl = '<?= admin_url('admin-ajax.php'); ?>';
        var lesson_finished = '<?php echo CheckPreviousLessonAsDone::$finished; ?>';

        jQuery.ajax({
            type: "POST",
            data: {
                action: 'bpmj_eddcm_check_previous_lesson_as_undone',
                lesson: lesson_finished,
            },
            url: ajaxurl,
            complete: function (response) {
                if (response.responseJSON.data.hasOwnProperty('status') && 'ok' === response.responseJSON.data.status) {
                    jQuery('.check-previous-lesson-as-undone-loader').css('display', 'none');
                    jQuery('.check-previous-lesson-as-undone-done').css('display', 'inline');
                } else {
                    jQuery('.check-previous-lesson-as-undone-loader').css('display', 'none');
                    jQuery('.check-previous-lesson-as-undone-fail').css('display', 'inline');
                }

                jQuery('#course-progress input[type="checkbox"]').trigger('change');
            }
        });
    });
</script>