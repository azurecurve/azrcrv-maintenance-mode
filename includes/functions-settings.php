<?php
/*
	tab output on settings page
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\MaintenanceMode;

/**
 * Get options including defaults.
 */
function get_option_with_defaults( $option_name ) {

	$defaults = array(
		'enabled'          => 0,
		'maintenance-mode' => array(
			'user'  => array(
				'header'  => 'Site Undergoing Maintenance',
				'message' => 'This site is currently undergoing maintenance. Please check back later.',
			),
			'admin' => array(
				'disabled'  => 0,
				'header'  => 'Maintenance Mode Enabled',
				'message' => 'This site is currently undergoing maintenance and is only available to admin users.',
			),
		),
	);

	$options = get_option( $option_name, $defaults );

	$options = recursive_parse_args( $options, $defaults );

	return $options;

}

/**
 * Recursively parse options to merge with defaults.
 */
function recursive_parse_args( $args, $defaults ) {
	$new_args = (array) $defaults;

	foreach ( $args as $key => $value ) {
		if ( is_array( $value ) && isset( $new_args[ $key ] ) ) {
			$new_args[ $key ] = recursive_parse_args( $value, $new_args[ $key ] );
		} else {
			$new_args[ $key ] = $value;
		}
	}

	return $new_args;
}

/**
 * Display Settings page.
 */
function display_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'azrcrv-mm' ) );
	}

	// Retrieve plugin configuration options from database.
	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	echo '<div id="' . esc_attr( PLUGIN_HYPHEN ) . '-general" class="wrap">';

		echo '<h1>';
			echo '<a href="' . esc_url_raw( DEVELOPER_RAW_LINK ) . esc_attr( PLUGIN_SHORT_SLUG ) . '/"><img src="' . esc_url_raw( plugins_url( '../assets/images/logo.svg', __FILE__ ) ) . '" style="padding-right: 6px; height: 20px; width: 20px;" alt="azurecurve" /></a>';
			echo esc_html( get_admin_page_title() );
		echo '</h1>';

	// phpcs:ignore.
	if ( isset( $_GET['settings-updated'] ) ) {
		echo '<div class="notice notice-success is-dismissible">
					<p><strong>' . esc_html__( 'Settings have been saved.', 'azrcrv-mm' ) . '</strong></p>
				</div>';
	}

		require_once 'tab-settings.php';
		require_once 'tab-instructions.php';
		require_once 'tab-other-plugins.php';
		require_once 'tabs-output.php';
	?>
		
	</div>
	<?php
}

/**
 * Save settings.
 */
function save_options() {
	// Check that user has proper security level.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action', 'azrcrv-mm' ) );
	}
	// Check that nonce field created in configuration form is present.
	if ( ! empty( $_POST ) && check_admin_referer( PLUGIN_HYPHEN, PLUGIN_HYPHEN . '-nonce' ) ) {

		// Retrieve original plugin options array.
		$options = get_option_with_defaults( PLUGIN_HYPHEN );

		$option_name = 'enabled';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = 1;
		} else {
			$options[ $option_name ] = 0;
		}

		$option_name = 'maintenance-mode-admin-disabled';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ 'maintenance-mode']['admin']['disabled' ] = 1;
		} else {
			$options[ 'maintenance-mode']['admin']['disabled' ] = 0;
		}

		$option_name                                    = 'maintenance-mode-admin-header';
		$options['maintenance-mode']['admin']['header'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );

		$option_name                                     = 'maintenance-mode-admin-message';
		$options['maintenance-mode']['admin']['message'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );

		$option_name                                   = 'maintenance-mode-user-header';
		$options['maintenance-mode']['user']['header'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );

		$option_name                                    = 'maintenance-mode-user-message';
		$options['maintenance-mode']['user']['message'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );

		// Store updated options array to database.
		update_option( PLUGIN_HYPHEN, $options );

		// Redirect the page to the configuration form that was processed.
		wp_safe_redirect( add_query_arg( 'page', PLUGIN_HYPHEN . '&settings-updated', admin_url( 'admin.php' ) ) );
		exit;
	}
}
