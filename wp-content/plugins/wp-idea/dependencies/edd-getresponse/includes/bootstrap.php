<?php

namespace bpmj\wp\eddres;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

// General defines
defined( 'BPMJ_EDDRES_NAMESPACE' ) OR define( 'BPMJ_EDDRES_NAMESPACE', __NAMESPACE__ );
defined( 'BPMJ_EDDRES_NAMESPACE_LENGTH' ) OR define( 'BPMJ_EDDRES_NAMESPACE_LENGTH', strlen( BPMJ_EDDRES_NAMESPACE ) );
defined( 'BPMJ_EDDRES_PREFIX' ) OR define( 'BPMJ_EDDRES_PREFIX', strtr( BPMJ_EDDRES_NAMESPACE, '\\', '_' ) );

// Directory defines
defined( 'BPMJ_EDDRES_INCLUDES_DIR' ) OR define( 'BPMJ_EDDRES_INCLUDES_DIR', BPMJ_EDDRES_DIR . '/includes' );

// Event defines
defined( 'BPMJ_EDDRES_EVENT_INIT' ) OR define( 'BPMJ_EDDRES_EVENT_INIT', 'bpmj_eddres_event_init' );
defined( 'BPMJ_EDDRES_EVENT_BEFORE_INIT' ) OR define( 'BPMJ_EDDRES_EVENT_BEFORE_INIT', 'bpmj_eddres_event_before_init' );
defined( 'BPMJ_EDDRES_EVENT_ADMIN_INIT' ) OR define( 'BPMJ_EDDRES_EVENT_ADMIN_INIT', 'bpmj_eddres_event_admin_init' );
defined( 'BPMJ_EDDRES_EVENT_BEFORE_ADMIN_INIT' ) OR define( 'BPMJ_EDDRES_EVENT_BEFORE_ADMIN_INIT', 'bpmj_eddres_event_before_admin_init' );

include_once BPMJ_EDDRES_INCLUDES_DIR . '/class-plugin.php';

/*
 * Setup plugin and that's it for now
 */
Plugin::instance()->bootstrap();
