<?php
/*
	other plugins tab on settings page
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\MaintenanceMode;

/**
 * Settings tab.
 */

$tab_settings_label = PLUGIN_NAME . ' ' . esc_html__( 'Settings', 'azrcrv-mm' );
$tab_settings       = '
<table class="form-table azrcrv-settings">
		
	<tr>
	
		<th scope="row" colspan="2">
		
			<label for="explanation">
				' . esc_html__( PLUGIN_NAME . ' allows the site to be put into maintenance mode.', 'azrcrv-mm' ) . '
			</label>
			
		</th>
		
	</tr>
	
	<tr>
		<th scope="row"><label for="widget-width">
		
			' . esc_html__( 'Enable maintenance mode', 'azrcrv-mm' ) . '
			
		</th>
		
		<td>
		
			<input name="enabled" type="checkbox" id="enabled" value="1" ' . checked( '1', $options['enabled'], false ) . ' />
			
			<label for="enabled">
			
				<span class="description">
					
					' . esc_html__( 'Enable maintenance mode. Only a logged in administrator will be able to access the site.', 'azrcrv-mm' ) . '
					
				</span>
				
			</label>
			
		</td>
	</tr>

	<tr>
	
		<th scope="row" colspan=2 class="azrcrv-settings-section-heading">
			
				<h2 class="azrcrv-settings-section-heading">' . esc_html__( 'Admin Banner', 'azrcrv-mm' ) . '</h2>
			
		</th>

	</tr>
						
	<tr>
	
		<th scope="row"><label for="widget-width">
		
			' . esc_html__( 'Disable admin banner', 'azrcrv-mm' ) . '
			
		</th>
		
		<td>
		
			<input name="maintenance-mode-admin-disabled" type="checkbox" id="maintenance-mode-admin-disabled" value="1" ' . checked( '1', $options['maintenance-mode']['admin']['disabled'], false ) . ' />
			
			<label for="maintenance-mode-admin-disabled">
			
				<span class="description">
				
					' . esc_html__( 'Maintenance mode is still enabled, but the admin banner is disabled so admins won\'t see the warning banner.', 'azrcrv-mm' ) . '
					
				</span>
			</label>
			
		</td>
		
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
	
		<th scope="row" colspan=2 class="azrcrv-settings-section-heading">
			
				<h2 class="azrcrv-settings-section-heading">' . esc_html__( 'User Banner', 'azrcrv-mm' ) . '</h2>
			
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
