<?php
/** 
 * ------------------------------------------------------------------------------
 * Plugin Name: Maintenance Mode
 * Description: Enable maintenance mode to disable the front-end of your ClassicPress site.
 * Version: 1.1.0
 * Author: azurecurve
 * Author URI: https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI: https://development.azurecurve.co.uk/classicpress-plugins/maintenance-mode/
 * Text Domain: maintenance-mode
 * Domain Path: /languages
 * ------------------------------------------------------------------------------
 * This is free sottware released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.html.
 * ------------------------------------------------------------------------------
 */

// Prevent direct access.
if (!defined('ABSPATH')){
	die();
}

// include plugin menu
require_once(dirname(__FILE__).'/pluginmenu/menu.php');
add_action('admin_init', 'azrcrv_create_plugin_menu_mm');

// include update client
require_once(dirname(__FILE__).'/libraries/updateclient/UpdateClient.class.php');

/**
 * Setup registration activation hook, actions, filters and shortcodes.
 *
 * @since 1.0.0
 *
 */
// add actions
add_action('admin_menu', 'azrcrv_mm_create_admin_menu');
add_action('admin_post_azrcrv_mm_save_options', 'azrcrv_mm_save_options');
add_action('plugins_loaded', 'azrcrv_mm_load_languages');
add_action('wp_head', 'azrcrv_mm_maintenance_mode');
add_action('wp_enqueue_scripts', 'azrcrv_mm_load_css');

// add filters
add_filter('plugin_action_links', 'azrcrv_mm_add_plugin_action_link', 10, 2);
add_filter('codepotent_update_manager_image_path', 'azrcrv_mm_custom_image_path');
add_filter('codepotent_update_manager_image_url', 'azrcrv_mm_custom_image_url');

/**
 * Load language files.
 *
 * @since 1.0.0
 *
 */
function azrcrv_mm_load_languages() {
    $plugin_rel_path = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain('maintenance-mode', false, $plugin_rel_path);
}

/**
 * Custom plugin image path.
 *
 * @since 1.12.0
 *
 */
function azrcrv_mm_custom_image_path($path){
    if (strpos($path, 'azrcrv-maintenance-mode') !== false){
        $path = plugin_dir_path(__FILE__).'assets/pluginimages';
    }
    return $path;
}

/**
 * Custom plugin image url.
 *
 * @since 1.12.0
 *
 */
function azrcrv_mm_custom_image_url($url){
    if (strpos($url, 'azrcrv-maintenance-mode') !== false){
        $url = plugin_dir_url(__FILE__).'assets/pluginimages';
    }
    return $url;
}

/**
 * Load plugin css.
 *
 * @since 1.0.0
 *
 */
function azrcrv_mm_load_css(){
	wp_enqueue_style('azrcrv-mm', plugins_url('assets/css/style.css', __FILE__));
}

/**
 * Get options including defaults.
 *
 * @since 1.12.0
 *
 */
function azrcrv_mm_get_option($option_name){
 
	$defaults = array(
						'enabled' => 0,
						'maintenance-mode' => array(
													'user' => array(
																		'header' => 'Site Undergoing Maintenance',
																		'message' => 'This site is currently undergoing maintenance. Please check back later.',
																	),
													'admin' => array(
																		'header' => 'Maintenance Mode Enabled',
																		'message' => 'This site is currently undergoing maintenance and is only available to admin users.',
																	),
												),
					);

	$options = get_option($option_name, $defaults);

	$options = azrcrv_mm_recursive_parse_args($options, $defaults);

	return $options;

}

/**
 * Recursively parse options to merge with defaults.
 *
 * @since 1.14.0
 *
 */
function azrcrv_mm_recursive_parse_args( $args, $defaults ) {
	$new_args = (array) $defaults;

	foreach ($args as $key => $value ) {
		if (is_array($value) && isset($new_args[$key])){
			$new_args[$key] = azrcrv_mm_recursive_parse_args($value, $new_args[$key]);
		}else{
			$new_args[$key] = $value;
		}
	}

	return $new_args;
}

/**
 * Add action link on plugins page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_mm_add_plugin_action_link($links, $file){
	static $this_plugin;

	if (!$this_plugin){
		$this_plugin = plugin_basename(__FILE__);
	}

	if ($file == $this_plugin){
		$settings_link = '<a href="'.admin_url('admin.php?page=azrcrv-mm').'"><img src="'.plugins_url('/pluginmenu/images/logo.svg', __FILE__).'" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />'.esc_html__('Settings' ,'maintenance-mode').'</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 *
 */
function azrcrv_mm_create_admin_menu(){
	
	// add settings to from twitter submenu
	$options = azrcrv_mm_get_option('azrcrv-mm');
	
	add_submenu_page("azrcrv-plugin-menu"
						,__("Maintenance Mode Settings", "maintenance-mode")
						,__("Maintenance Mode", "maintenance-mode")
						,'manage_options'
						,'azrcrv-mm'
						,'azrcrv_mm_display_options');
}

/*
 * Display admin page for this plugin
 *
 * @since 1.0.0
 *
 */
function azrcrv_mm_display_options(){

	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'maintenance-mode'));
	}
	
	$options = azrcrv_mm_get_option('azrcrv-mm');
	
	global $menu;
	
	echo '<div id="azrcrv-mm-general" class="wrap azrcrv-mm">
		<fieldset>
			<h1>'.esc_html(get_admin_page_title()).'</h1>';
			
			if(isset($_GET['settings-updated'])){
				echo '<div class="notice notice-success is-dismissible">
					<p><strong>'.esc_html('Settings have been saved.', 'maintenance-mode').'</strong></p>
				</div>';
			}
			
			echo '<form method="post" action="admin-post.php">
				<input type="hidden" name="action" value="azrcrv_mm_save_options" />';
				
				wp_nonce_field('azrcrv-mm', 'azrcrv-mm-nonce');
				
				echo '<table class="form-table">
					
					<tr>
						<th scope="row"><label for="widget-width">
							'.__('Enable maintenance mode', 'maintenance-mode').'
						</th>
						<td>
							<input name="enabled" type="checkbox" id="enabled" value="1" '.checked('1', $options['enabled'], false).' />
							<label for="enabled"><span class="description">
								'.__('Enable maintenance mode. Only a logged in administrator will be able to access the site.', 'maintenance-mode').'
							</span></label
						</td>
					</tr>
					
					<tr>
						<th scope="row" colspan="2">
							<h3>'.__('Admin', 'maintenance-mode').'</h3>
						</th>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="maintenance-mode-admin-header">
								'.__('Header', 'maintenance-mode').'
							</label>
						</th>
						<td>
							<input name="maintenance-mode-admin-header" type="text" id="maintenance-admin-mode-header" value="'.$options['maintenance-mode']['admin']['header'].'" class="regular-text" />
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="maintenance-mode-admin-message">
								'.__('Message', 'maintenance-mode').'
							</label>
						</th>
						<td>
							<input name="maintenance-mode-admin-message" type="text" id="maintenance-mode-admin-message" value="'.$options['maintenance-mode']['admin']['message'].'" class="large-text" />
						</td>
					</tr>
					
					<tr>
						<th scope="row" colspan="2">
							<h3>'.__('User', 'maintenance-mode').'</h3>
						</th>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="maintenance-mode-user-header">
								'.__('Header', 'maintenance-mode').'
							</label>
						</th>
						<td>
							<input name="maintenance-mode-user-header" type="text" id="maintenance-user-mode-header" value="'.$options['maintenance-mode']['user']['header'].'" class="regular-text" />
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="maintenance-mode-user-message">
								'.__('Message', 'maintenance-mode').'
							</label>
						</th>
						<td>
							<input name="maintenance-mode-user-message" type="text" id="maintenance-mode-user-message" value="'.$options['maintenance-mode']['user']['message'].'" class="large-text" />
						</td>
					</tr>
					
				</table>
				
				<input type="submit" value="'.__('Save Changes', 'maintenance-mode').'" class="button-primary"/>
				
			</form>
		</fieldset>
	</div>';
}

/*
 * Display admin page for this plugin
 *
 * @since 1.0.0
 *
 */
function azrcrv_mm_save_options(){

	// Check that user has proper security level
	if (!current_user_can('manage_options')){
		wp_die(esc_html__('You do not have permissions to perform this action', 'maintenance-mode'));
	}
	
	// Check that nonce field created in configuration form is present
	if (! empty($_POST) && check_admin_referer('azrcrv-mm', 'azrcrv-mm-nonce')){
		
		$option_name = 'enabled';
		if (isset($_POST[$option_name])){
			$options[$option_name] = 1;
		}else{
			$options[$option_name] = 0;
		}
		
		$option_name = 'maintenance-mode-admin-header';
		$options['maintenance-mode']['admin']['header'] = sanitize_text_field($_POST[$option_name]);
		
		$option_name = 'maintenance-mode-admin-message';
		$options['maintenance-mode']['admin']['message'] = sanitize_text_field($_POST[$option_name]);
		
		/*
		* Update options
		*/
		update_option('azrcrv-mm', $options);
		
		// Redirect the page to the configuration form that was processed
		wp_redirect(add_query_arg('page', 'azrcrv-mm&settings-updated', admin_url('admin.php')));
		exit;
	}
}

/*
 * Maintenance Mode
 *
 * @since 1.0.0
 *
 */
function azrcrv_mm_maintenance_mode(){
	
	$options = azrcrv_mm_get_option('azrcrv-mm');
	
    if ($options['enabled'] == 1){
		if (current_user_can('manage_options')){
			echo '<h1 class="azrcrv-mm-admin">'.$options['maintenance-mode']['admin']['header'].'</h1><p class="azrcrv-mm-admin">'.$options['maintenance-mode']['admin']['message'].'</p>';
		}else{
			wp_die('<h1 class="azrcrv-mm-user">'.$options['maintenance-mode']['user']['header'].'</h1><p class="azrcrv-mm-user">'.$options['maintenance-mode']['user']['message'].'</p>');
		}
    }
	
}