<?php

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\settings\LMS_Settings;
// menu (backend)

add_action( 'admin_head-nav-menus.php', 'bpmj_courses_add_nav_menu_metabox' );

function bpmj_courses_add_nav_menu_metabox() {
	add_meta_box( 'bpmj_courses', __( 'WP Idea courses', BPMJ_EDDCM_DOMAIN ), 'bpmj_courses_nav_menu_metabox', 'nav-menus', 'side', 'default' );
}

function bpmj_courses_nav_menu_metabox() {
	global $nav_menu_selected_id;

    $courses_functionality_enabled = LMS_Settings::get_option(Settings_Const::COURSES_ENABLED) ?? true;

    $elems = [
        (object) [
            'db_id'            => 0,
            'object'           => 'bpmj-eddcm-login',
            'object_id'        => 'bpmj-eddcm-login',
            'menu_item_parent' => 0,
            'type'             => 'custom',
            'title'            => __( 'Login', BPMJ_EDDCM_DOMAIN ) . '|' . __( 'Log Out', BPMJ_EDDCM_DOMAIN ),
            'url'              => '#bpmj-eddcm-login#',
            'target'           => '',
            'attr_title'       => '',
            'classes'          => array(),
            'xfn'              => '',
        ]
    ];

    if($courses_functionality_enabled){

        $elems = array_merge([
            (object) [
                'db_id'            => 0,
                'object'           => 'bpmj-eddcm-panel',
                'object_id'        => 'bpmj-eddcm-panel',
                'menu_item_parent' => 0,
                'type'             => 'custom',
                'title'            => __( 'Course Panel', BPMJ_EDDCM_DOMAIN ),
                'url'              => '#bpmj-eddcm-panel#',
                'target'           => '',
                'attr_title'       => '',
                'classes'          => array(),
                'xfn'              => '',
            ],
            (object)[
                'db_id'            => 0,
                'object'           => 'bpmj-eddcm-my-courses',
                'object_id'        => 'bpmj-eddcm-my-courses',
                'menu_item_parent' => 0,
                'type'             => 'custom',
                'title'            => __( 'My courses', BPMJ_EDDCM_DOMAIN ),
                'url'              => '#bpmj-eddcm-my-courses#',
                'target'           => '',
                'attr_title'       => '',
                'classes'          => array(),
                'xfn'              => '',
            ]
        ], $elems);

    }

	$walker = new Walker_Nav_Menu_Checklist( array() );
	?>
    <div id="course-menu-links" class="course-menu-links-div">

        <div id="tabs-panel-course-menu-links-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
            <ul id="course-menu-linkschecklist" class="list:course-menu-links categorychecklist form-no-clear">
				<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $elems ), 0, ( object ) array( 'walker' => $walker ) ); ?>
            </ul>
        </div>

        <p class="button-controls">
			<span class="add-to-menu">
				<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?>
                       class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>"
                       name="add-course-menu-links-menu-item" id="submit-course-menu-links"/>
				<span class="spinner"></span>
			</span>
        </p>

    </div>
	<?php
}
