<?php

/**
 * Declare the Namespace.
 */
namespace azurecurve\MaintenanceMode;

// Check that code was called from ClassicPress with uninstallation constant declared
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Options to remove
$options = array(
	PLUGIN_HYPHEN,
);

foreach ( $options as $option ) {
	delete_option( $option );
}
