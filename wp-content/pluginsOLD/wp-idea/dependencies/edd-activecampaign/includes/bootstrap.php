<?php

namespace bpmj\wp\eddact;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

// General defines
defined( 'BPMJ_EDDACT_NAMESPACE' ) OR define( 'BPMJ_EDDACT_NAMESPACE', __NAMESPACE__ );
defined( 'BPMJ_EDDACT_NAMESPACE_LENGTH' ) OR define( 'BPMJ_EDDACT_NAMESPACE_LENGTH', strlen( BPMJ_EDDACT_NAMESPACE ) );
defined( 'BPMJ_EDDACT_PREFIX' ) OR define( 'BPMJ_EDDACT_PREFIX', strtr( BPMJ_EDDACT_NAMESPACE, '\\', '_' ) );

// Directory defines
defined( 'BPMJ_EDDACT_INCLUDES_DIR' ) OR define( 'BPMJ_EDDACT_INCLUDES_DIR', BPMJ_EDDACT_DIR . '/includes' );

// Event defines
defined( 'BPMJ_EDDACT_EVENT_INIT' ) OR define( 'BPMJ_EDDACT_EVENT_INIT', 'bpmj_eddact_event_init' );
defined( 'BPMJ_EDDACT_EVENT_BEFORE_INIT' ) OR define( 'BPMJ_EDDACT_EVENT_BEFORE_INIT', 'bpmj_eddact_event_before_init' );
defined( 'BPMJ_EDDACT_EVENT_ADMIN_INIT' ) OR define( 'BPMJ_EDDACT_EVENT_ADMIN_INIT', 'bpmj_eddact_event_admin_init' );
defined( 'BPMJ_EDDACT_EVENT_BEFORE_ADMIN_INIT' ) OR define( 'BPMJ_EDDACT_EVENT_BEFORE_ADMIN_INIT', 'bpmj_eddact_event_before_admin_init' );

include_once BPMJ_EDDACT_INCLUDES_DIR . '/class-plugin.php';

/*
 * Setup plugin and that's it for now
 */
Plugin::instance()->bootstrap();
