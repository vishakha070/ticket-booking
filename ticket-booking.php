<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/vishakha07/
 * @since             1.0.0
 * @package           Ticket_Booking
 *
 * @wordpress-plugin
 * Plugin Name:       Ticket Booking
 * Plugin URI:        https://github.com/vishakha070/
 * Description:       This plugin provides shortcode for add ticket fields in contact form 7.
 * Version:           1.0.0
 * Author:            Vishakha Gupta
 * Author URI:        https://profiles.wordpress.org/vishakha07/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ticket-booking
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TICKET_BOOKING_VERSION', '1.0.0' );
define( 'TICKET_BOOKING_TABLE', 'ticket_booking_cf7' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ticket-booking-activator.php
 */
function activate_ticket_booking() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ticket-booking-activator.php';
	Ticket_Booking_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ticket-booking-deactivator.php
 */
function deactivate_ticket_booking() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ticket-booking-deactivator.php';
	Ticket_Booking_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ticket_booking' );
register_deactivation_hook( __FILE__, 'deactivate_ticket_booking' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ticket-booking.php';

add_action( 'admin_init', 'wpcf7_ticket_booking_has_parent_plugin' );

function wpcf7_ticket_booking_has_parent_plugin() {
	if ( is_admin() && current_user_can( 'activate_plugins' ) && ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		add_action( 'admin_notices', 'wpcf7_ticket_book_nocf7_notice' );

		deactivate_plugins( plugin_basename( __FILE__ ) ); 

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

function wpcf7_ticket_book_nocf7_notice() { ?>
	<div class="error">
		<p>
			<?php printf(
				__( '%s must be installed and activated for the Ticket Booking plugin to work.', 'ticket-booking' ),
				'<a href="'. admin_url('plugin-install.php?tab=search&s=contact+form+7') . '">' . esc_html__( 'Contact Form 7', 'ticket-booking' ) .'</a>'
			); ?>
		</p>
	</div>
	<?php
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ticket_booking() {

	$plugin = new Ticket_Booking();
	$plugin->run();

}

run_ticket_booking();