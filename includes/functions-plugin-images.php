<?php
/*
	plugin images functions
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\MaintenanceMode;

/**
 * Custom plugin image path.
 */
function custom_image_path( $path ) {
	return esc_url_raw( plugin_dir_url( PLUGIN_FILE ) . 'assets/images' );
}

/**
 * Custom plugin image url.
 */
function custom_image_url( $url ) {
	return esc_url_raw( plugin_dir_url( PLUGIN_FILE ) . 'assets/images' );
}
