<?php
/*
	other plugins tab on settings page
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\MaintenanceMode;

/**
 * Instructions tab.
 */
$tab_instructions_label = esc_html__( 'Instructions', 'azrcrv-mm' );
$tab_instructions       = '
<table class="form-table azrcrv-settings">

	<tr>
	
		<th scope="row" colspan=2 class="azrcrv-settings-section-heading">
			
				<h2 class="azrcrv-settings-section-heading">' . esc_html__( 'Maintenance Mode Usage', 'azrcrv-mm' ) . '</h2>
			
		</th>

	</tr>

	<tr>
	
		<td scope="row" colspan=2>
		
			<p>' .
				esc_html__( 'Mark the enable checkbox on the Settings tab and the site will be put into maintenance mode, displaying the configured messages.', 'azrcrv-mm' ) . '
					
			</p>
		
			<p>' .
				esc_html__( 'Unmark the enable checkbox to disable maintenance mode.', 'azrcrv-mm' ) . '
					
			</p>
		
		</td>
	
	</tr>
	
</table>';
