<?php
// Exit if accessed directly
use bpmj\wpidea\admin\Edit_Course;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// clean up
function bpmj_eddcm_admin_init() {
	remove_action( 'edd_payments_page_bottom', 'edd_payment_history_mobile_link' );
	remove_filter( 'admin_footer_text', 'edd_admin_rate_us' );
}

add_action( 'admin_init', 'bpmj_eddcm_admin_init' );

function bpmj_eddcm_admin_download_certificate() {
    if (isset( $_GET['certificate'] ) && isset( $_GET['action'] ) && $_GET['action'] === 'download' ) {
        $post_id = WPI()->certificates->get_certificate_post_id( $_GET['certificate'] );

        if ( ! empty( $post_id ) ) {
            $pdf_content = WPI()->certificates->get_pdf_content( $post_id );
            $orientation = WPI()->certificates->get_pdf_orientation(); 

            $pdf = bpmj_eddcm_render_pdf( $pdf_content, $orientation );
            $pdf->stream();
        }
    }
}

add_action( 'admin_init', 'bpmj_eddcm_admin_download_certificate' );

function bpmj_eddcm_get_users_stats_for_course() {
    $course_id = sanitize_text_field( $_POST['course'] );
    $participants = WPI()->courses->get_course_participants( $course_id );
    if ( $participants[ 'all' ] ) {

        ?>
        <a title="<?php esc_attr_e( 'Active (current) / Inactive / All', BPMJ_EDDCM_DOMAIN ) ?>"
           href="<?php echo admin_url( 'users.php?course_participants_of=' . $course_id ); ?>"><strong><?php echo $participants[ 'active' ]; ?></strong>
            / <?php echo $participants[ 'inactive' ]; ?>
            / <?php echo $participants[ 'all' ]; ?></a>
        <?php
    } else {
        echo '0';
    }
    wp_die();
}

add_action( 'wp_ajax_get_users_stats_for_course', 'bpmj_eddcm_get_users_stats_for_course' );

function bpmj_eddcm_get_users_stats_for_course_lessons() {
    $course_id = sanitize_text_field( $_POST['course'] );
    echo Edit_Course::get_show_stats_popup_html( $course_id, true );
    wp_die();
}

add_action( 'wp_ajax_get_users_stats_for_course_lessons', 'bpmj_eddcm_get_users_stats_for_course_lessons' );

function bpmj_eddcm_flush_rewrite_rules( $post_id, $post, $update ) {
    if ( $post->post_type === 'courses' && $update )
        flush_rewrite_rules();
}
add_action( 'save_post', 'bpmj_eddcm_flush_rewrite_rules', 10, 3 );
