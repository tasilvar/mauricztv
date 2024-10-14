<?php

namespace bpmj\wp\eddfm;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

// General defines
defined( 'BPMJ_EDDFM_NAMESPACE' ) OR define( 'BPMJ_EDDFM_NAMESPACE', __NAMESPACE__ );
defined( 'BPMJ_EDDFM_NAMESPACE_LENGTH' ) OR define( 'BPMJ_EDDFM_NAMESPACE_LENGTH', strlen( BPMJ_EDDFM_NAMESPACE ) );
defined( 'BPMJ_EDDFM_PREFIX' ) OR define( 'BPMJ_EDDFM_PREFIX', strtr( BPMJ_EDDFM_NAMESPACE, '\\', '_' ) );

// Directory defines
defined( 'BPMJ_EDDFM_INCLUDES_DIR' ) OR define( 'BPMJ_EDDFM_INCLUDES_DIR', BPMJ_EDDFM_DIR . '/includes' );

// Event defines
defined( 'BPMJ_EDDFM_EVENT_INIT' ) OR define( 'BPMJ_EDDFM_EVENT_INIT', 'bpmj_eddfm_event_init' );
defined( 'BPMJ_EDDFM_EVENT_BEFORE_INIT' ) OR define( 'BPMJ_EDDFM_EVENT_BEFORE_INIT', 'bpmj_eddfm_event_before_init' );
defined( 'BPMJ_EDDFM_EVENT_ADMIN_INIT' ) OR define( 'BPMJ_EDDFM_EVENT_ADMIN_INIT', 'bpmj_eddfm_event_admin_init' );
defined( 'BPMJ_EDDFM_EVENT_BEFORE_ADMIN_INIT' ) OR define( 'BPMJ_EDDFM_EVENT_BEFORE_ADMIN_INIT', 'bpmj_eddfm_event_before_admin_init' );

include_once BPMJ_EDDFM_INCLUDES_DIR . '/class-plugin.php';

/*
 * Setup plugin and that's it for now
 */
Plugin::instance()->bootstrap();
