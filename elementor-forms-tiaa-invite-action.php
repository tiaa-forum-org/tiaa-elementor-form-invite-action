<?php
/**
 * Plugin Name: Elementor Forms/TIAA Invite Form Action
 * Description: A custom Elementor Pro form action for inviting new users to join tiaa-forum.org via the
 TIAA WordPress plugin.
 * Plugin URI:  https://tiaa-forum.org/
 * Version:     0.0.3
 * Author:      TIAA Admin Platform Subteam
 * Author URI:  https://tiaa-forum.org
 * Text Domain: tiaa-invite-form-action
 *
 * Requires Plugins: elementor, elementor-pro, tiaa-wpplugin
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Elementor tested up to: 3.25.0
 * Elementor Pro tested up to: 3.25.0
 * TIAA-WordPress Plugin tested up to: 0.0.2
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Add new member to tiaa-forum.org
 *
 * Seems over-kill but could not find a reliable way to find from elementor the page has a form
 * and that form uses the `tiaa` action.
 *
 * @since 0.0.3
 * @param ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar $form_actions_registrar
 * @return void
 */
add_action( 'elementor/frontend/widget/before_render', function ( $widget ) {
	// Check if the rendered widget is an Elementor form.
	if ( $widget->get_name() === 'form' ) {
		$settings = $widget->get_settings_for_display();

		// Check if the TIAA invite action exists in the widget's actions.
		if ( ! empty( $settings['submit_actions'] ) && is_array( $settings['submit_actions'] ) ) {
			if ( in_array( 'tiaa', $settings['submit_actions'], true ) ) {
				// Enqueue the script, since the action is present.
				wp_enqueue_script(
					'tiaa-plugin-form-handler',
					plugin_dir_url( __FILE__ ) . 'assets/js/form-handler.js',
					[ 'wp-api-fetch' ],
					'0.0.3',
					true // Load in the footer.
				);

				// Localize script to pass the required data.
				wp_localize_script(
					'tiaa-plugin-form-handler',
					'tiaaPluginData',
					[
						'apiUrl' => '/tiaa_wpplugin/v1/invite',
						'nonce'  => wp_create_nonce( 'wp_rest' ),
					]
				);
			}
		}
	}
});

/**
 * Registers the custom "Tiaa Invite" form action for Elementor Pro Forms.
 *
 * This function hooks into the 'elementor_pro/forms/actions' filter to add
 * the custom Tiaa Invite form action to Elementor Pro. The action is defined
 * by the `Tiaa_Invite_After_Submit` class, allowing it to function as an option
 * in the "Actions After Submit" dropdown of Elementor Pro Forms.
 *
 * @param ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar $form_actions_registrar
 *
 * @return array Updated array of form actions with the custom action included.
 *
 * @since 0.0.3
 */
function register_tiaa_custom_form_action(  $form_actions_registrar ) : void {

	require_once __DIR__ . '/form-action/tiaa-invite-action.php';

	if ( class_exists( 'Tiaa_Invite_After_Submit' ) ) {
		// Add your custom form action to the module.
		$form_actions_registrar->register( new Tiaa_Invite_After_Submit() );
	}
}

add_action( 'elementor_pro/forms/actions/register', 'register_tiaa_custom_form_action' );


