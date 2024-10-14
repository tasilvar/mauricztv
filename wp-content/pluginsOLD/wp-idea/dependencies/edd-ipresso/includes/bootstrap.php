<?php

namespace bpmj\wp\eddip;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

// General defines
defined( 'BPMJ_EDDIP_NAMESPACE' ) OR define( 'BPMJ_EDDIP_NAMESPACE', __NAMESPACE__ );
defined( 'BPMJ_EDDIP_NAMESPACE_LENGTH' ) OR define( 'BPMJ_EDDIP_NAMESPACE_LENGTH', strlen( BPMJ_EDDIP_NAMESPACE ) );
defined( 'BPMJ_EDDIP_PREFIX' ) OR define( 'BPMJ_EDDIP_PREFIX', strtr( BPMJ_EDDIP_NAMESPACE, '\\', '_' ) );

// Directory defines
defined( 'BPMJ_EDDIP_INCLUDES_DIR' ) OR define( 'BPMJ_EDDIP_INCLUDES_DIR', BPMJ_EDDIP_DIR . '/includes' );

// Event defines
defined( 'BPMJ_EDDIP_EVENT_INIT' ) OR define( 'BPMJ_EDDIP_EVENT_INIT', 'bpmj_eddip_event_init' );
defined( 'BPMJ_EDDIP_EVENT_BEFORE_INIT' ) OR define( 'BPMJ_EDDIP_EVENT_BEFORE_INIT', 'bpmj_eddip_event_before_init' );
defined( 'BPMJ_EDDIP_EVENT_ADMIN_INIT' ) OR define( 'BPMJ_EDDIP_EVENT_ADMIN_INIT', 'bpmj_eddip_event_admin_init' );
defined( 'BPMJ_EDDIP_EVENT_BEFORE_ADMIN_INIT' ) OR define( 'BPMJ_EDDIP_EVENT_BEFORE_ADMIN_INIT', 'bpmj_eddip_event_before_admin_init' );

include_once BPMJ_EDDIP_INCLUDES_DIR . '/class-plugin.php';

/*
 * Setup plugin and that's it for now
 */
Plugin::instance()->bootstrap();
