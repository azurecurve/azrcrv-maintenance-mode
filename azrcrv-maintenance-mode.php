<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name: Maintenance Mode
 * Description: Enable maintenance mode to disable the front-end of your ClassicPress site.
 * Version: 1.3.1
 * Author: azurecurve
 * Author URI: https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI: https://development.azurecurve.co.uk/classicpress-plugins/maintenance-mode/
 * Text Domain: azrcrv-mm
 * Domain Path: /languages
 * ------------------------------------------------------------------------------
 * This is free sottware released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.html.
 * ------------------------------------------------------------------------------
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// include plugin menu
require_once dirname( __FILE__ ) . '/pluginmenu/menu.php';
add_action( 'admin_init', 'azrcrv_create_plugin_menu_mm' );

// include update client
require_once dirname( __FILE__ ) . '/libraries/updateclient/UpdateClient.class.php';

/**
 * Setup registration activation hook, actions, filters and shortcodes.
 *
 * @since 1.0.0
 */
// add actions
add_action( 'admin_menu', 'azrcrv_mm_create_admin_menu' );
add_action( 'admin_post_azrcrv_mm_save_options', 'azrcrv_mm_save_options' );
add_action( 'plugins_loaded', 'azrcrv_mm_load_languages' );
add_action( 'wp_head', 'azrcrv_mm_maintenance_mode' );
add_action( 'admin_init', 'azrcrv_mm_register_admin_styles' );
add_action( 'admin_enqueue_scripts', 'azrcrv_mm_enqueue_admin_styles' );
add_action( 'admin_init', 'azrcrv_mm_register_admin_scripts' );
add_action( 'admin_enqueue_scripts', 'azrcrv_mm_enqueue_admin_scripts' );
add_action( 'wp_enqueue_scripts', 'azrcrv_mm_load_css' );

// add filters
add_filter( 'plugin_action_links', 'azrcrv_mm_add_plugin_action_link', 10, 2 );
add_filter( 'codepotent_update_manager_image_path', 'azrcrv_mm_custom_image_path' );
add_filter( 'codepotent_update_manager_image_url', 'azrcrv_mm_custom_image_url' );

/**
 * Load language files.
 *
 * @since 1.0.0
 */
function azrcrv_mm_load_languages() {
	$plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages';
	load_plugin_textdomain( 'azrcrv-mm', false, $plugin_rel_path );
}

/**
 * Custom plugin image path.
 *
 * @since 1.0.0
 */
function azrcrv_mm_custom_image_path( $path ) {
	if ( strpos( $path, 'azrcrv-azrcrv-mm' ) !== false ) {
		$path = plugin_dir_path( __FILE__ ) . 'assets/pluginimages';
	}
	return $path;
}

/**
 * Custom plugin image url.
 *
 * @since 1.0.0
 */
function azrcrv_mm_custom_image_url( $url ) {
	if ( strpos( $url, 'azrcrv-azrcrv-mm' ) !== false ) {
		$url = plugin_dir_url( __FILE__ ) . 'assets/pluginimages';
	}
	return $url;
}

/**
 * Register admin styles.
 *
 * @since 1.3.0
 */
function azrcrv_mm_register_admin_styles() {
	wp_register_style( 'azrcrv-mm-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), '1.0.0' );
	wp_register_style( 'azrcrv-mm-pluginmenu-admin-styles', plugins_url( 'pluginmenu/css/style.css', __FILE__ ), array(), '1.0.0' );
}

/**
 * Enqueue admin styles.
 *
 * @since 1.3.0
 */
function azrcrv_mm_enqueue_admin_styles() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'azrcrv-mm' ) ) {
		wp_enqueue_style( 'azrcrv-mm-admin-styles' );
		wp_enqueue_style( 'azrcrv-mm-pluginmenu-admin-styles' );
	}
}

/**
 * Register admin scripts.
 *
 * @since 1.3.0
 */
function azrcrv_mm_register_admin_scripts() {
	wp_register_script( 'azrcrv-mm-admin-jquery', plugins_url( 'assets/jquery/admin.js', __FILE__ ), array(), '1.0.0', true );
}

/**
 * Enqueue admin styles.
 *
 * @since 1.3.0
 */
function azrcrv_mm_enqueue_admin_scripts() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'azrcrv-mm' ) ) {
		wp_enqueue_script( 'azrcrv-mm-admin-jquery' );
	}
}

/**
 * Load plugin css.
 *
 * @since 1.0.0
 */
function azrcrv_mm_load_css() {
	wp_enqueue_style( 'azrcrv-mm', plugins_url( 'assets/css/style.css', __FILE__ ) );
}

/**
 * Get options including defaults.
 *
 * @since 1.0.0
 */
function azrcrv_mm_get_option( $option_name ) {

	$defaults = array(
		'enabled'          => 0,
		'maintenance-mode' => array(
			'user'  => array(
				'header'  => 'Site Undergoing Maintenance',
				'message' => 'This site is currently undergoing maintenance. Please check back later.',
			),
			'admin' => array(
				'header'  => 'Maintenance Mode Enabled',
				'message' => 'This site is currently undergoing maintenance and is only available to admin users.',
			),
		),
	);

	$options = get_option( $option_name, $defaults );

	$options = azrcrv_mm_recursive_parse_args( $options, $defaults );

	return $options;

}

/**
 * Recursively parse options to merge with defaults.
 *
 * @since 1.14.0
 */
function azrcrv_mm_recursive_parse_args( $args, $defaults ) {
	$new_args = (array) $defaults;

	foreach ( $args as $key => $value ) {
		if ( is_array( $value ) && isset( $new_args[ $key ] ) ) {
			$new_args[ $key ] = azrcrv_mm_recursive_parse_args( $value, $new_args[ $key ] );
		} else {
			$new_args[ $key ] = $value;
		}
	}

	return $new_args;
}

/**
 * Add action link on plugins page.
 *
 * @since 1.0.0
 */
function azrcrv_mm_add_plugin_action_link( $links, $file ) {
	static $this_plugin;

	if ( ! $this_plugin ) {
		$this_plugin = plugin_basename( __FILE__ );
	}

	if ( $file == $this_plugin ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=azrcrv-mm' ) . '"><img src="' . plugins_url( '/pluginmenu/images/logo.svg', __FILE__ ) . '" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />' . esc_html__( 'Settings', 'azrcrv-mm' ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 */
function azrcrv_mm_create_admin_menu() {

	// add settings to from twitter submenu
	$options = azrcrv_mm_get_option( 'azrcrv-mm' );

	add_submenu_page(
		'azrcrv-plugin-menu',
		esc_html__( 'Maintenance Mode Settings', 'azrcrv-mm' ),
		esc_html__( 'Maintenance Mode', 'azrcrv-mm' ),
		'manage_options',
		'azrcrv-mm',
		'azrcrv_mm_display_options'
	);
}

/*
 * Display admin page for this plugin
 *
 * @since 1.0.0
 *
 */
function azrcrv_mm_display_options() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'azrcrv-mm' ) );
	}

	$options = azrcrv_mm_get_option( 'azrcrv-mm' );

	global $menu;

	echo '<div id="azrcrv-mm-general" class="wrap azrcrv-mm">
			<h1>
				<a href="https://development.azurecurve.co.uk/classicpress-plugins/"><img src="' . esc_attr(
		plugins_url( '/pluginmenu/images/logo.svg', __FILE__ ) ) . '" style="padding-right: 6px; height: 20px; width: 20px;" alt="azurecurve" /></a>
				' . esc_html( get_admin_page_title() )
	. '
			</h1>';

	if ( isset( $_GET['settings-updated'] ) ) {
		echo '<div class="notice notice-success is-dismissible">
				<p><strong>' . esc_html__( 'Settings have been saved.', 'azrcrv-mm' ) . '</strong></p>
			</div>';
	}

		$tab_1_label = esc_html__( 'Plugin Settings', 'azrcrv-mm' );
		$tab_1       = '<table class="form-table azrcrv-mm">
						
					<tr>
						<th scope="row"><label for="widget-width">
							' . esc_html__( 'Enable maintenance mode', 'azrcrv-mm' ) . '
						</th>
						<td>
							<input name="enabled" type="checkbox" id="enabled" value="1" ' . checked( '1', $options['enabled'], false ) . ' />
							<label for="enabled"><span class="description">
								' . esc_html__( 'Enable maintenance mode. Only a logged in administrator will be able to access the site.', 'azrcrv-mm' ) . '
							</span></label
						</td>
					</tr>
					
					<tr>
						<th scope="row" colspan=2 class="section-heading">
							<h3>' . esc_html__( 'Admin', 'azrcrv-mm' ) . '</h3>
						</th>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="maintenance-mode-admin-header">
								' . esc_html__( 'Header', 'azrcrv-mm' ) . '
							</label>
						</th>
						<td>
							<input name="maintenance-mode-admin-header" type="text" id="maintenance-admin-mode-header" value="' . esc_html( wp_unslash( $options['maintenance-mode']['admin']['header'] ) ) . '" class="regular-text" />
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="maintenance-mode-admin-message">
								' . esc_html__( 'Message', 'azrcrv-mm' ) . '
							</label>
						</th>
						<td>
							<input name="maintenance-mode-admin-message" type="text" id="maintenance-mode-admin-message" value="' . esc_html( wp_unslash( $options['maintenance-mode']['admin']['message'] ) ) . '" class="large-text" />
						</td>
					</tr>
					
					<tr>
						<th scope="row" colspan=2 class="section-heading">
							<h3>' . esc_html__( 'User', 'azrcrv-mm' ) . '</h3>
						</th>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="maintenance-mode-user-header">
								' . esc_html__( 'Header', 'azrcrv-mm' ) . '
							</label>
						</th>
						<td>
							<input name="maintenance-mode-user-header" type="text" id="maintenance-user-mode-header" value="' . esc_html( wp_unslash( $options['maintenance-mode']['user']['header'] ) ) . '" class="regular-text" />
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="maintenance-mode-user-message">
								' . esc_html__( 'Message', 'azrcrv-mm' ) . '
							</label>
						</th>
						<td>
							<input name="maintenance-mode-user-message" type="text" id="maintenance-mode-user-message" value="' . esc_html( wp_unslash( $options['maintenance-mode']['user']['message'] ) ) . '" class="large-text" />
						</td>
					</tr>
					
				</table>';

		$tab_3_label = esc_html__( 'Instructions', 'azrcrv-mm' );
		$tab_3       = '<table class="form-table azrcrv-mm">
		
					<tr>
					
						<th scope="row" colspan=2 class="section-heading">
							
								<h2 class="azrcrv-mm">' . esc_html__( 'Maintenance Mode Usage', 'azrcrv-mm' ) . '</h2>
							
						</th>
	
					</tr>
		
					<tr>
					
						<td scope="row" colspan=2>
						
							<p>' .
								esc_html__( 'Mark the enable checkbox on the Settings tab and the site will be put into maintenance mode displaying the configured messages.', 'azrcrv-mm' )
							. '</p>
							<p>' .
								esc_html__( 'Unmark the enable checkbox to disable maintenance mode.', 'azrcrv-mm' )
							. '</p>
						
						</td>
					
					</tr>
					
				</table>';

		$plugin_array = get_option( 'azrcrv-plugin-menu' );

		$tab_4_plugins = '';
	foreach ( $plugin_array as $plugin_name => $plugin_details ) {
		if ( $plugin_details['retired'] == 0 ) {
			$alternative_color = '';
			if ( isset( $plugin_details['bright'] ) and $plugin_details['bright'] == 1 ) {
				$alternative_color = 'bright-';
			}
			if ( isset( $plugin_details['premium'] ) and $plugin_details['premium'] == 1 ) {
				$alternative_color = 'premium-';
			}
			if ( is_plugin_active( $plugin_details['plugin_link'] ) ) {
				$tab_4_plugins .= "<a href='{$plugin_details['admin_URL']}' class='azrcrv-{$alternative_color}plugin-index'>{$plugin_name}</a>";
			} else {
				$tab_4_plugins .= "<a href='{$plugin_details['dev_URL']}' class='azrcrv-{$alternative_color}plugin-index'>{$plugin_name}</a>";
			}
		}
	}

		$tab_4_label = esc_html__( 'Other Plugins', 'azrcrv-mm' );
		$tab_4       = '<table class="form-table azrcrv-mm">
		
					<tr>
					
						<td scope="row" colspan=2>
						
							<p>' .
								sprintf( esc_html__( '%1$s was one of the first plugin developers to start developing for ClassicPress; all plugins are available from %2$s and are integrated with the %3$s plugin for fully integrated, no hassle, updates.', 'azrcrv-mm' ), '<strong>azurecurve | Development</strong>', '<a href="https://development.azurecurve.co.uk/classicpress-plugins/">azurecurve | Development</a>', '<a href="https://directory.classicpress.net/plugins/update-manager/">Update Manager</a>' )
							. '</p>
							<p>' .
								sprintf( esc_html__( 'Other plugins available from %s are:', 'azrcrv-mm' ), '<strong>azurecurve | Development</strong>' )
							. '</p>
						
						</td>
					
					</tr>
					
					<tr>
					
						<td scope="row" colspan=2>
						
							' . $tab_4_plugins . '
							
						</td>
	
					</tr>
					
				</table>';

	?>
		<form method="post" action="admin-post.php">

				<input type="hidden" name="action" value="azrcrv_mm_save_options" />

				<?php
					// <!-- Adding security through hidden referer field -->.
					wp_nonce_field( 'azrcrv-mm', 'azrcrv-mm-nonce' );
				?>
				
				
				<div id="tabs" class="azrcrv-ui-tabs">
					<ul class="azrcrv-ui-tabs-nav azrcrv-ui-widget-header" role="tablist">
						<li class="azrcrv-ui-state-default azrcrv-ui-state-active" aria-controls="tab-panel-1" aria-labelledby="tab-1" aria-selected="true" aria-expanded="true" role="tab">
							<a id="tab-1" class="azrcrv-ui-tabs-anchor" href="#tab-panel-1"><?php echo $tab_1_label; ?></a>
						</li>
						<li class="azrcrv-ui-state-default" aria-controls="tab-panel-3" aria-labelledby="tab-3" aria-selected="false" aria-expanded="false" role="tab">
							<a id="tab-3" class="azrcrv-ui-tabs-anchor" href="#tab-panel-3"><?php echo $tab_3_label; ?></a>
						</li>
						<li class="azrcrv-ui-state-default" aria-controls="tab-panel-4" aria-labelledby="tab-4" aria-selected="false" aria-expanded="false" role="tab">
							<a id="tab-4" class="azrcrv-ui-tabs-anchor" href="#tab-panel-4"><?php echo $tab_4_label; ?></a>
						</li>
					</ul>
					<div id="tab-panel-1" class="azrcrv-ui-tabs-scroll" role="tabpanel" aria-hidden="false">
						<fieldset>
							<legend class='screen-reader-text'>
								<?php echo $tab_1_label; ?>
							</legend>
							<?php echo $tab_1; ?>
						</fieldset>
					</div>
					<div id="tab-panel-3" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
						<fieldset>
							<legend class='screen-reader-text'>
								<?php echo $tab_3_label; ?>
							</legend>
							<?php echo $tab_3; ?>
						</fieldset>
					</div>
					<div id="tab-panel-4" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
						<fieldset>
							<legend class='screen-reader-text'>
								<?php echo $tab_4_label; ?>
							</legend>
							<?php echo $tab_4; ?>
						</fieldset>
					</div>
				</div>

			<input type="submit" name="btn_save" value="<?php esc_html_e( 'Save Settings', 'azrcrv-mm' ); ?>" class="button-primary"/>
		</form>
		<div class='azrcrv-mm-donate'>
			<?php
				esc_html_e( 'Support', 'azrcrv-mm' );
			?>
			azurecurve | Development
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="MCJQN9SJZYLWJ">
				<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
			</form>
			<span>
				<?php
				esc_html_e( 'You can help support the development of our free plugins by donating a small amount of money.', 'azrcrv-mm' );
				?>
			</span>
		</div>
	</div>
	<?php

}

/*
 * Display admin page for this plugin
 *
 * @since 1.0.0
 *
 */
function azrcrv_mm_save_options() {

	// Check that user has proper security level
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action', 'azrcrv-mm' ) );
	}

	// Check that nonce field created in configuration form is present
	if ( ! empty( $_POST ) && check_admin_referer( 'azrcrv-mm', 'azrcrv-mm-nonce' ) ) {

		$option_name = 'enabled';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options[ $option_name ] = 1;
		} else {
			$options[ $option_name ] = 0;
		}

		$option_name                                    = 'maintenance-mode-admin-header';
		$options['maintenance-mode']['admin']['header'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );

		$option_name                                     = 'maintenance-mode-admin-message';
		$options['maintenance-mode']['admin']['message'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );

		$option_name                                   = 'maintenance-mode-user-header';
		$options['maintenance-mode']['user']['header'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );

		$option_name                                    = 'maintenance-mode-user-message';
		$options['maintenance-mode']['user']['message'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );

		/*
		* Update options
		*/
		update_option( 'azrcrv-mm', $options );

		// Redirect the page to the configuration form that was processed
		wp_safe_redirect( add_query_arg( 'page', 'azrcrv-mm&settings-updated', admin_url( 'admin.php' ) ) );
		exit;
	}
}

/*
 * Maintenance Mode
 *
 * @since 1.0.0
 *
 */
function azrcrv_mm_maintenance_mode() {

	$options = azrcrv_mm_get_option( 'azrcrv-mm' );

	if ( $options['enabled'] == 1 ) {
		if ( current_user_can( 'manage_options' ) ) {
			echo '<h1 class="azrcrv-mm-admin">' . esc_html( wp_unslash( $options['maintenance-mode']['admin']['header'] ) ) . '</h1><p class="azrcrv-mm-admin">' . esc_html( wp_unslash( $options['maintenance-mode']['admin']['message'] ) ) . '</p>';
		} else {
			wp_die( '<h1 class="azrcrv-mm-user">' . esc_html( wp_unslash( $options['maintenance-mode']['user']['header'] ) ) . '</h1><p class="azrcrv-mm-user">' . esc_html( wp_unslash( $options['maintenance-mode']['user']['message'] ) ) . '</p>' );
		}
	}

}
