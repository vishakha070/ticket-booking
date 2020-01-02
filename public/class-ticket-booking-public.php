<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/vishakha07/
 * @since      1.0.0
 *
 * @package    Ticket_Booking
 * @subpackage Ticket_Booking/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ticket_Booking
 * @subpackage Ticket_Booking/public
 * @author     Vishakha Gupta <vishakha.wordpress02@gmail.com>
 */
class Ticket_Booking_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @author Vishakha Gupta
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue_styles() {

		if ( ! wp_style_is( $this->plugin_name, 'enqueued' ) ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ticket-booking-public.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
 	 * @author Vishakha Gupta
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue_scripts() {

		if ( ! wp_script_is( $this->plugin_name, 'enqueued' ) ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ticket-booking-public.js', array( 'jquery' ), $this->version, false );
		}	

	}

	/**
	 * Validation of ticket fields.
	 *
 	 * @author Vishakha Gupta
	 * @since  1.0.0
	 * @access public
	 */
	public function wpcf7_ticket_book_cf7_filter( $result, $tag ) {

		// Test if new 4.6+ functions exists.
		$tag = ( class_exists( 'WPCF7_FormTag' ) ) ? new WPCF7_FormTag( $tag ) : new WPCF7_Shortcode( $tag );

		$name        = $tag->name;
		$is_required = $tag->get_option( 'required' );
		$_POST       = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
		$form_data   = wp_unslash( $_POST );
		$value       = isset( $form_data['tickets'][ $name ] ) ? (array) $form_data['tickets'][ $name ] : array();
		if ( $is_required && empty( $value ) ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}

		return $result;
	}

	/**
	 * Save ticket details.
	 *
 	 * @author Vishakha Gupta
	 * @since  1.0.0
	 * @access public
	 */
	public function ta_wpcf7_contact_submit( $contact_form, $result ) {
		global $wpdb;
		$_POST     = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
		$form_data = wp_unslash( $_POST );
		if ( 'mail_sent' == $result['status'] ) {
			if ( isset( $form_data['tickets'] ) ) {
				if ( ! empty( $form_data['tickets'] ) ) {
					$form_id = $form_data['_wpcf7'];
					foreach ( $form_data['tickets'] as $ticket_id => $ticket ) {
						$ticket_col = array();
						if ( ! empty( $ticket ) ) {
							foreach ( $ticket as $single_ticket ) {
								$ticket_col[ $single_ticket ] = '1';
							}
						}
						$previous = $wpdb->get_row( "SELECT * from `{$wpdb->prefix}" . TICKET_BOOKING_TABLE . "` where form_id = $form_id AND ticket_id = '$ticket_id'" );
						if ( ! empty( $previous ) ) {
							$wpdb->update(
								"{$wpdb->prefix}" . TICKET_BOOKING_TABLE,
								$ticket_col,
								array(
									'form_id'   => $form_data['_wpcf7'],
									'ticket_id' => $ticket_id,
								)
							);
						} else {
							$ticket_col['form_id']   = $form_data['_wpcf7'];
							$ticket_col['ticket_id'] = $ticket_id;
							$wpdb->insert(
								"{$wpdb->prefix}" . TICKET_BOOKING_TABLE,
								$ticket_col
							);
						}
					}
				}
			}
		}
	}

}
