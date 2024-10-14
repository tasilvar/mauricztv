<div class="bpmj-eddcm-quizzess-table-advanced-filters">
    <select id="bpmj-eddcm-quizz-resolved-by" name="by">
        <option value=""></option>
        <option value="course_name" <?php isset( $_GET['by'] ) ? selected( $_GET['by'], 'course_name', true ) : ''; ?>><?php _e( 'Course name', BPMJ_EDDCM_DOMAIN ); ?></option>
        <option value="quiz_name" <?php isset( $_GET['by'] ) ? selected( $_GET['by'], 'quiz_name', true ) : ''; ?>><?php _e( 'Quiz name', BPMJ_EDDCM_DOMAIN ); ?></option>
        <option value="user_email" <?php isset( $_GET['by'] ) ? selected( $_GET['by'], 'user_email', true ) : ''; ?>><?php _e( 'User email', BPMJ_EDDCM_DOMAIN ); ?></option>
        <option value="user_first_name" <?php isset( $_GET['by'] ) ? selected( $_GET['by'], 'user_first_name', true ) : ''; ?>><?php _e( 'User first name', BPMJ_EDDCM_DOMAIN ); ?></option>
        <option value="user_last_name" <?php isset( $_GET['by'] ) ? selected( $_GET['by'], 'user_last_name', true ) : ''; ?>><?php _e( 'User last name', BPMJ_EDDCM_DOMAIN ); ?></option>
    </select>
    <input id="bpmj-eddcm-quizz-resolved-by-text" type="text" name="by-text" value="<?php echo isset( $_GET[ 'by-text' ] ) ? sanitize_text_field( $_GET['by-text'] ) : '' ; ?>">
    <button class="button"><?php _e( 'Filter', BPMJ_EDDCM_DOMAIN ); ?></button>
    <button id="bpmj-eddcm-clear-quizes-filters" class="button">
        <span class="text">
            <?php _e( 'Clear filters', BPMJ_EDDCM_DOMAIN ); ?>
        </span>
    </button>
</div>
