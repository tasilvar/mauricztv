<?php

namespace bpmj\wp\eddpayu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// General defines
defined( 'BPMJ_EDDPAYU_NAMESPACE' ) OR define( 'BPMJ_EDDPAYU_NAMESPACE', __NAMESPACE__ );
defined( 'BPMJ_EDDPAYU_NAMESPACE_LENGTH' ) OR define( 'BPMJ_EDDPAYU_NAMESPACE_LENGTH', strlen( BPMJ_EDDPAYU_NAMESPACE ) );
defined( 'BPMJ_EDDPAYU_PREFIX' ) OR define( 'BPMJ_EDDPAYU_PREFIX', strtr( BPMJ_EDDPAYU_NAMESPACE, '\\', '_' ) );

// Directory defines
defined( 'BPMJ_EDDPAYU_INCLUDES_DIR' ) OR define( 'BPMJ_EDDPAYU_INCLUDES_DIR', BPMJ_EDDPAYU_DIR . '/includes' );
defined( 'BPMJ_EDDPAYU_INCLUDES_VENDOR_DIR' ) OR define( 'BPMJ_EDDPAYU_INCLUDES_VENDOR_DIR', BPMJ_EDDPAYU_INCLUDES_DIR . '/vendor' );
defined( 'BPMJ_EDDPAYU_INCLUDES_ADMIN_DIR' ) OR define( 'BPMJ_EDDPAYU_INCLUDES_ADMIN_DIR', BPMJ_EDDPAYU_INCLUDES_DIR . '/admin' );

// Event defines
defined( 'BPMJ_EDDPAYU_EVENT_INIT' ) OR define( 'BPMJ_EDDPAYU_EVENT_INIT', 'bpmj_eddpayu_event_init' );
defined( 'BPMJ_EDDPAYU_EVENT_BEFORE_INIT' ) OR define( 'BPMJ_EDDPAYU_EVENT_BEFORE_INIT', 'bpmj_eddpayu_event_before_init' );
defined( 'BPMJ_EDDPAYU_EVENT_ADMIN_INIT' ) OR define( 'BPMJ_EDDPAYU_EVENT_ADMIN_INIT', 'bpmj_eddpayu_event_admin_init' );
defined( 'BPMJ_EDDPAYU_EVENT_BEFORE_ADMIN_INIT' ) OR define( 'BPMJ_EDDPAYU_EVENT_BEFORE_ADMIN_INIT', 'bpmj_eddpayu_event_before_admin_init' );

// Development defines
if ( file_exists( BPMJ_EDDPAYU_DIR . '/_dev-constants.php' ) ) {
	include_once BPMJ_EDDPAYU_DIR . '/_dev-constants.php';
}
defined( 'BPMJ_EDDPAYU_DEV' ) OR define( 'BPMJ_EDDPAYU_DEV', false );

include_once BPMJ_EDDPAYU_INCLUDES_DIR . '/class-plugin.php';

/*
 * Setup plugin and that's it for now
 */
Plugin::instance()->bootstrap();
