<?php
/**
 * Plugin Name:       Follow Up Boss for Forminator
 * Plugin URI:        https://github.com/fullrealtyservices/forminator-followup-boss
 * Description:       Adds Follow Up Boss as a native integration inside Forminator. Connect with your Follow Up Boss API key and send form entries to Follow Up Boss as leads.
 * Version:           1.0.0
 * Author:            Full Realty Services
 * Author URI:        https://21stcenturylending.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       forminator-followup-boss
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Requires Plugins:  forminator
 *
 * @package ForminatorFollowUpBoss
 */

defined( 'ABSPATH' ) || exit;

define( 'FFUB_VERSION', '1.0.0' );
define( 'FFUB_FILE', __FILE__ );
define( 'FFUB_DIR', plugin_dir_path( __FILE__ ) );
define( 'FFUB_URL', plugin_dir_url( __FILE__ ) );
define( 'FFUB_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Register the Follow Up Boss integration with Forminator.
 *
 * Forminator fires this action after loading its own addons; external plugins
 * register here (require the classes first — Forminator does not autoload them).
 */
add_action(
	'forminator_addons_loaded',
	static function () {
		if ( ! class_exists( 'Forminator_Integration_Loader' ) ) {
			return;
		}
		require_once FFUB_DIR . 'includes/lib/class-forminator-followupboss-api.php';
		require_once FFUB_DIR . 'includes/class-forminator-followupboss.php';
		require_once FFUB_DIR . 'includes/class-forminator-followupboss-form-settings.php';
		require_once FFUB_DIR . 'includes/class-forminator-followupboss-form-hooks.php';

		Forminator_Integration_Loader::get_instance()->register( 'Forminator_Followupboss' );
	}
);

/**
 * Bridge for programmatically-created entries.
 *
 * Entries added via Forminator_API::add_form_entry() fire no Forminator
 * submission hooks, so other plugins trigger the push explicitly:
 *   do_action( 'forminator_fub/push', $form_id, $entry_id );
 */
add_action(
	'forminator_fub/push',
	static function ( $form_id, $entry_id ) {
		if ( class_exists( 'Forminator_Followupboss' ) ) {
			Forminator_Followupboss::push_entry( (int) $form_id, (int) $entry_id );
		}
	},
	10,
	2
);
