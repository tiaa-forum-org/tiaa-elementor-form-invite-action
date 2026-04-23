<?php
/**
 * Plugin Name: TIAA Elementor
 * Description: Elementor Pro extensions for tiaa-forum.org. Provides a custom Discourse
 *              invite form action and site-wide clickable Loop Grid cards.
 * Plugin URI:  https://tiaa-forum.org/
 * Version:     0.0.8
 * Author:      Lew Grothe, TIAA Admin Platform Subteam
 * Author URI:  https://tiaa-forum.org
 * Text Domain: tiaa-elementor
 *
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * This plugin supersedes the plugin formerly named
 * `tiaa-elementor-forms-invite-action`. That plugin must be deactivated and
 * removed before activating this one.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// ---------------------------------------------------------------------------
// Clickable Loop Grid cards — site-wide
// ---------------------------------------------------------------------------
require_once __DIR__ . '/loop-grid/clickable-cards.php';

// ---------------------------------------------------------------------------
// Elementor Pro form action — Discourse invite
// ---------------------------------------------------------------------------

/**
 * Enqueue the invite form-handler script when an Elementor form widget that
 * uses the 'tiaa' submit action is about to be rendered.
 *
 * Checking at render time (rather than on every page load) keeps the script
 * off pages that have no TIAA invite form.
 *
 * @since 0.0.3
 *
 * @param \ElementorPro\Modules\Forms\Widgets\Form $widget The Elementor widget
 *                                                          being rendered.
 */
add_action( 'elementor/frontend/widget/before_render', function ( $widget ) {
	if ( $widget->get_name() !== 'form' ) {
		return;
	}

	$settings = $widget->get_settings_for_display();

	if (
		! empty( $settings['submit_actions'] ) &&
		is_array( $settings['submit_actions'] ) &&
		in_array( 'tiaa', $settings['submit_actions'], true )
	) {
		wp_enqueue_script(
			'tiaa-plugin-form-handler',
			plugin_dir_url( __FILE__ ) . 'assets/js/form-handler.js',
			[ 'wp-api-fetch' ],
			filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/form-handler.js' ),
			true // Load in the footer.
		);

		wp_localize_script(
			'tiaa-plugin-form-handler',
			'tiaaPluginData',
			[
				'apiUrl' => '/tiaa_wpplugin/v1/invite',
				'nonce'  => wp_create_nonce( 'wp_rest' ),
			]
		);
	}
} );

/**
 * Register the custom 'TIAA Invite' action with Elementor Pro's form action
 * registrar, making it available in the "Actions After Submit" dropdown.
 *
 * @since 0.0.3
 *
 * @param \ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar $form_actions_registrar
 */
function tiaa_elementor_register_invite_form_action( $form_actions_registrar ): void {
	require_once __DIR__ . '/form-action/tiaa-invite-action.php';

	if ( class_exists( 'Tiaa_Invite_After_Submit' ) ) {
		$form_actions_registrar->register( new Tiaa_Invite_After_Submit() );
	}
}

add_action( 'elementor_pro/forms/actions/register', 'tiaa_elementor_register_invite_form_action' );
