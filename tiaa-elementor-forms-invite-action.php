<?php
/**
 * Plugin Name: TIAA/Elementor Forms Invite Form Action
 * Description: An Elementor Pro form action for inviting new users to join tiaa-forum.org via the TIAA WordPress plugin.
 * Plugin URI:  https://tiaa-forum.org/
 * Version:     0.0.7
 * Author:      Lew Grothe, TIAA Admin Platform Subteam
 * Author URI:  https://tiaa-forum.org
 * Text Domain: tiaa-invite-form-action
 *
 * ***This plugin deprecates the plugin formerly named `elementor-forms-tiaa-invite-action` plugin***
 *
 * Ideally, this plugin should link closely (require?) to elementor, elementor-pro and, tiaa-wpplugin
 * plugins. However, closely linked in WordPress complicates to the level of making impossible
 * doing updates on any of those other plugins.
 *
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
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
                    filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/form-handler.js' ),
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

/**
 * Make Elementor Loop Item cards clickable site-wide.
 *
 * Adds a full-cover transparent anchor overlay over each .e-loop-item card
 * container, making the entire card clickable rather than just the title or
 * button link inside it. The overlay links to the first <a> href found inside
 * the card, which is typically the post title link.
 *
 * Runs on wp_footer on all front-end pages. The script is lightweight and
 * self-contained — it only activates when .e-loop-item elements are present,
 * so it is harmless on pages without Loop Grids.
 *
 * Previously restricted to is_front_page() only (v0.0.5). Extended site-wide
 * in v0.0.6 to support Loop Grids on additional pages (e.g. Related Organizations,
 * Hot Topics archive, Discourse Categories).
 *
 * @since 0.0.3
 * @updated 0.0.6 — removed is_front_page() restriction; now runs site-wide.
 */
add_action( 'wp_footer', function() {
	?>
	<script>
        document.querySelectorAll('.e-loop-item').forEach(function(card) {
            var link = card.querySelector('a');
            if (link) {
                var url = link.href;
                card.style.position = 'relative';
                card.style.cursor = 'pointer';
                var overlay = document.createElement('a');
                overlay.href = url;
                overlay.style.cssText = 'position:absolute;inset:0;z-index:1;';
                overlay.setAttribute('aria-hidden', 'true');
                card.appendChild(overlay);
            }
        });
	</script>
	<?php
} );
