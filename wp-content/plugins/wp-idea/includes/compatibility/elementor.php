<?php


/**
 * @param int $course_id
 * @param int $course_page_id
 */
function wpi_course_meta_has_been_cloned_to_course_page($course_id, $course_page_id) {

    $elementor_data = get_post_meta( $course_id, '_elementor_data', true);

    if ($elementor_data){
        update_post_meta( $course_page_id, '_elementor_data', $elementor_data );
    }

}

add_action('wpi_course_meta_has_been_cloned', 'wpi_course_meta_has_been_cloned_to_course_page', 10, 2);

/**
 * @param int $page_id
 * @param string $empty_string
 * @return string
 */
function wpi_add_custom_class_elementor($empty_string, $page_id) {

    return $page_id ? 'elementor-'.$page_id : '';

}

add_filter( 'wpi_add_custom_class', 'wpi_add_custom_class_elementor', 10, 2);

/**
 * @param string $page
 *
 * @return string
 */
function bpmj_eddcm_elementor_check_canvas( $page ) {

	if ( is_singular() ) {
		$page_template   = get_post_meta( get_the_ID(), '_wp_page_template', true );
		$constants       = array(
			'\Elementor\Modules\PageTemplates\Module::TEMPLATE_CANVAS',
			'\Elementor\Core\Settings\Page\Manager::TEMPLATE_CANVAS',
		);
		$template_canvas = '';
		foreach ( $constants as $constant ) {
			if ( defined( $constant ) ) {
				$template_canvas = constant( $constant );
				break;
			}
		}

		if ( $template_canvas && $template_canvas === $page_template && in_array( $page, array(
				'header.php',
				'footer.php'
			) ) ) {
			return str_replace( '.php', '_empty.php', $page );
		}
	}

	return $page;
}

add_filter( 'bpmj_cm_get_template_path_page', 'bpmj_eddcm_elementor_check_canvas' );

/**
 * @param array $prefixes
 *
 * @return array
 */
function bpmj_eddcm_clone_course_elementor_meta_prefixes( $prefixes ) {
	if ( is_array( $prefixes ) ) {
		$prefixes[] = '_elementor_';
	} else {
		$prefixes = array( '_elementor_' );
	}

	return $prefixes;
}

add_filter( 'bpmj_eddcm_clone_course_meta_prefixes', 'bpmj_eddcm_clone_course_elementor_meta_prefixes' );

/**
 * @param int $new_post_id
 * @param int $cloned_from_post_id
 */
function bpmj_eddcm_clone_elementor_files($new_post_id, $cloned_from_post_id) {
	$wp_upload_dir = wp_upload_dir( null, false );
	
	$elementor_upoads_path = defined('\\Elementor\\CSS_File::FILE_BASE_DIR') ? \Elementor\CSS_File::FILE_BASE_DIR . '/' : '';
	if( empty( $elementor_upoads_path ) && (defined('\\Elementor\\CSS_File::UPLOADS_DIR')) && defined('\\Elementor\\CSS_File::DEFAULT_FILES_DIR')) { // Elementor 2.2
		$elementor_upoads_path = '/' . \Elementor\CSS_File::UPLOADS_DIR . \Elementor\CSS_File::DEFAULT_FILES_DIR;
	}
	
	$old_file      = $wp_upload_dir[ 'basedir' ] . $elementor_upoads_path . 'post-' . $cloned_from_post_id . '.css';
	$new_file      = $wp_upload_dir[ 'basedir' ] . $elementor_upoads_path . 'post-' . $new_post_id . '.css';

	if ( file_exists( $old_file ) ) {
		$contents = file_get_contents( $old_file );
		$contents = str_replace( 'elementor-' . $cloned_from_post_id, 'elementor-' . $new_post_id, $contents );
		file_put_contents( $new_file, $contents );
	}
}

add_action('bpmj_eddcm_clone_post', 'bpmj_eddcm_clone_elementor_files', 10, 2);