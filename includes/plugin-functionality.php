<?php
/*
	plugin functionality
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\MaintenanceMode;


/*
 * Maintenance Mode
 *
 * @since 1.0.0
 *
 */
function maintenance_mode() {

	$options = get_option_with_defaults( 'azrcrv-mm' );

	if ( $options['enabled'] == 1 ) {
		
		if ( current_user_can( 'manage_options' ) ) {
			
			// display admin message
			
			if ( $options['maintenance-mode']['admin']['disabled'] == 0 ) {
				
				if ( strlen( $options['maintenance-mode']['admin']['header'] ) > 0 ) {
					echo '<h1 class="azrcrv-mm-admin">' . esc_html( wp_unslash( $options['maintenance-mode']['admin']['header'] ) ) . '</h1>';
				}
				
				if ( strlen( $options['maintenance-mode']['admin']['message'] ) > 0 ) {
					echo '<p class="azrcrv-mm-admin">' . esc_html( wp_unslash( $options['maintenance-mode']['admin']['message'] ) ) . '</p>';
				}
				
			}
			
		} else {
		
			// die and display user message
			
			$user_message = '';
			
			if ( strlen( $options['maintenance-mode']['user']['header'] ) > 0 ) {
				$user_message .= '<h1 class="azrcrv-mm-user">' . esc_html( wp_unslash( $options['maintenance-mode']['user']['header'] ) ) . '</h1>';
			}
			
			if ( strlen( $options['maintenance-mode']['user']['message'] ) > 0 ) {
				$user_message .= '<p class="azrcrv-mm-user">' . esc_html( wp_unslash( $options['maintenance-mode']['user']['message'] ) ) . '</p>';
			}
			
			wp_die ( $user_message );
			
		}
		
	}

}
