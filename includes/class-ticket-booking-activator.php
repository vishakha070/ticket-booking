<?php

/**
 * Fired during plugin activation
 *
 * @link       https://profiles.wordpress.org/vishakha07/
 * @since      1.0.0
 *
 * @package    Ticket_Booking
 * @subpackage Ticket_Booking/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ticket_Booking
 * @subpackage Ticket_Booking/includes
 * @author     Vishakha Gupta <vishakha.wordpress02@gmail.com>
 */
class Ticket_Booking_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// create table if not exists.
		global $wpdb;
		$tickets = array();
		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}" . TICKET_BOOKING_TABLE . "` (
					`id`			INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					`form_id`		INT(11) UNSIGNED NOT NULL DEFAULT '0',
					`ticket_id`		VARCHAR(255) NOT NULL DEFAULT '0'," ;
		for ( $i = 1; $i <= 100; $i++ ) {
			$tickets["ticket_" . $i] = 0;
			$sql .= "`ticket_" . $i ."`	TINYINT(1) NOT NULL DEFAULT '0'," ;
		}

		$sql .=	"PRIMARY KEY (`id`), UNIQUE KEY (`ticket_id`) )";
		$wpdb->query( $sql );
	}

}
