<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/vishakha07/
 * @since      1.0.0
 *
 * @package    Ticket_Booking
 * @subpackage Ticket_Booking/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ticket_Booking
 * @subpackage Ticket_Booking/admin
 * @author     Vishakha Gupta <vishakha.wordpress02@gmail.com>
 */
class Ticket_Booking_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @author Vishakha Gupta
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue_styles() {

		if ( ! wp_style_is( $this->plugin_name, 'enqueued' ) ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ticket-booking-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @author Vishakha Gupta
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue_scripts() {

		if ( ! wp_script_is( $this->plugin_name, 'enqueued' ) ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ticket-booking-admin.js', array( 'jquery' ), $this->version, false );
		}

	}

	/**
	 * Initialize the shortcode.
	 *
	 * @author Vishakha Gupta
	 * @since  1.0.0
	 * @access public
	 */
	public function wpcf7_add_form_tag_ticket_book_cf7() {

		// Test if new 4.6+ functions exists
		if ( function_exists( 'wpcf7_add_form_tag' ) ) {
			wpcf7_add_form_tag(
				'ticket_book_cf7',
				array( $this, 'wpcf7_ticket_book_cf7_formtag_handler' ),
				array(
					'name-attr'    => true,
					'do-not-store' => true,
					'not-for-mail' => true,
				)
			);
		} else {
			wpcf7_add_shortcode( 'ticket_book_cf7', array( $this, 'wpcf7_ticket_book_cf7_formtag_handler' ), true );
		}

	}

	/**
	 * Form Tag handler, this is where we generate the ticket_book_cf7 HTML from the shortcode options.
	 *
	 * @author Vishakha Gupta
	 * @since  1.0.0
	 * @access public
	 */
	public function wpcf7_ticket_book_cf7_formtag_handler( $tag ) {

		// Test if new 4.6+ functions exists
		$tag = ( class_exists( 'WPCF7_FormTag' ) ) ? new WPCF7_FormTag( $tag ) : new WPCF7_Shortcode( $tag );

		if ( empty( $tag->name ) ) {
			return '';
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );
		$class            = 'wpcf7_ticket';
		$atts             = array();
		$atts['class']    = $tag->get_class_option( $class );
		$atts['id']       = $tag->get_option( 'id', 'id', true );

		$atts['wrapper_id'] = $tag->get_option( 'wrapper-id' );
		$wrapper_id         = ( ! empty( $atts['wrapper_id'] ) ) ? reset( $atts['wrapper_id'] ) : uniqid( 'wpcf7-' );

		$atts['message']             = apply_filters( 'wpcf7_ticket_book_cf7_message', esc_html__( 'Tickets', 'ticket-booking' ) );
		$atts['name']                = $tag->name;
		$atts['type']                = 'checkbox';
		$atts['defaultautoselected'] = $tag->get_option( 'defaultautoselected' );
		$atts['move_inline_css']     = $tag->get_option( 'move-inline-css' );
		$atts['nomessage']           = $tag->get_option( 'nomessage' );
		$atts['required']            = $tag->get_option( 'required' );
		$atts['validation_error']    = $validation_error;
		$atts['css']                 = apply_filters( 'wpcf7_ticket_book_cf7_container_css', '' );
		$inputid                     = ( ! empty( $atts['id'] ) ) ? 'id="' . $atts['id'] . '" ' : '';
		$inputid_for                 = ( $inputid ) ? 'for="' . $atts['id'] . '" ' : '';
		$autocomplete_value          = ( $atts['defaultautoselected'] ) ? 'checked="checked"' : '';

		// Check if we should move the CSS off the element and into the footer.
		if ( ! empty( $atts['move_inline_css'] ) && $atts['move_inline_css'][0] === 'true' ) {
			$hp_css = '#' . $wrapper_id . ' {' . $atts['css'] . '}';
			wp_register_style( 'wpcf7-' . $wrapper_id . '-inline', false );
			wp_enqueue_style( 'wpcf7-' . $wrapper_id . '-inline' );
			wp_add_inline_style( 'wpcf7-' . $wrapper_id . '-inline', $hp_css );
			$el_css = '';
		} else {
			$el_css = 'style="' . $atts['css'] . '"';
		}

		$html = '<span id="' . $wrapper_id . '" class="wpcf7-form-control-wrap ' . $atts['name'] . '-wrap" ' . $el_css . '>';
		if ( ! $atts['nomessage'] ) {
			$html .= '<label ' . $inputid_for . ' class="hp-message">' . $atts['message'] . '</label>';
		}
		global $wpdb;
		$wpcf7   = WPCF7_ContactForm::get_current();
		$form_id = $wpcf7->id();
		$name    = $atts['name'];
		$cf_row  = $wpdb->get_row( "SELECT * FROM `{$wpdb->prefix}" . TICKET_BOOKING_TABLE . "`  WHERE form_id = $form_id AND ticket_id = '$name'", ARRAY_A );

		$html .= '<div class="ticket_booking_cf7_wrapper">';
		if ( empty( $cf_row ) ) {
			for ( $i = 1; $i <= 100; $i++ ) {
				$html .= '<div class="cf7_ticket_wrapper"><input ' . $inputid . 'class="' . $atts['class'] . '"  type="checkbox" name="tickets[' . $atts['name'] . '][]" value="ticket_' . $i . '" size="40" tabindex="-1" ' . $autocomplete_value . '" /><span>' . sprintf( esc_html__( 'Ticket %s', 'ticket-booking' ), esc_attr( $i ) ) . '</span></div>';
			}
		} else {
			for ( $i = 1; $i <= 100; $i++ ) {
				if ( 1 == $cf_row[ 'ticket_' . $i ] ) {
					$autocomplete_value = 'checked="checked"';
					$disabled           = 'disabled="disabled"';
				} else {
					$autocomplete_value = '';
					$disabled           = '';
				}
				$html .= '<div class="cf7_ticket_wrapper"><input ' . $inputid . 'class="' . $atts['class'] . '"  type="checkbox" name="tickets[' . $atts['name'] . '][]" value="ticket_' . $i . '" size="40" tabindex="-1" ' . $autocomplete_value . ' ' . $disabled . '" /><span>' . sprintf( esc_html__( 'Ticket %s', 'ticket-booking' ), esc_attr( $i ) ) . '</span></div>';
			}
		}
		$html .= '</div>';
		$html .= $validation_error . '</span>';

		// Hook for filtering finished ticket book form element.
		return apply_filters( 'wpcf7_ticket_book_cf7_html_output', $html, $atts );
	}

	/**
	 * Tag generator Adds 'ticket book' to the CF7 form editor.
	 *
	 * @author Vishakha Gupta
	 * @since  1.0.0
	 * @access public
	 */
	public function wpcf7_add_tag_generator_ticket_book_cf7() {

		if ( class_exists( 'WPCF7_TagGenerator' ) ) {
			$tag_generator = WPCF7_TagGenerator::get_instance();
			$tag_generator->add( 'ticket_book_cf7', esc_html__( 'ticket book', 'ticket-booking' ), array( $this, 'wpcf7_tg_pane_ticket_book_cf7' ) );
		} elseif ( function_exists( 'wpcf7_add_tag_generator' ) ) {
			wpcf7_add_tag_generator( 'ticket_book_cf7', esc_html__( 'ticket book', 'ticket-booking' ), 'wpcf7-tg-pane-ticket_book_cf7', array( $this, 'wpcf7_tg_pane_ticket_book_cf7' ) );
		}

	}

	/**
	 * Ticket Tag generator.
	 *
	 * @author Vishakha Gupta
	 * @since  1.0.0
	 * @access public
	 */
	public function wpcf7_tg_pane_ticket_book_cf7( $contact_form, $args = '' ) {

		if ( class_exists( 'WPCF7_TagGenerator' ) ) {
			$args        = wp_parse_args( $args, array() );
			$description = esc_html__( 'Generate a form-tag for a ticket_book_cf7 field.', 'ticket-booking' );
			$desc_link   = esc_html__( 'CF7 Ticket Book', 'ticket-booking' );
			?>
			<div class="control-box">
				<fieldset>
					<legend><?php echo esc_html( $description ); ?></legend>

					<table class="form-table"><tbody>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>">
									<?php esc_html_e( 'Name', 'ticket-booking' ); ?>
								</label>
							</th>
							<td>
								<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /><br>
								<em><?php esc_html_e( 'This can be anything, but should be changed from the default generated "ticket_book_cf7". For better security, change "ticket_book_cf7" to something more appealing to a bot, such as text including "email" or "website".', 'ticket-booking' ); ?></em>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php esc_html_e( 'ID (optional)', 'ticket-booking' ); ?></label>
							</th>
							<td>
								<input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" />
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php esc_html_e( 'Class (optional)', 'ticket-booking' ); ?></label>
							</th>
							<td>
								<input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" />
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $args['content'] . '-wrapper-id' ); ?>"><?php echo esc_html( __( 'Wrapper ID (optional)', 'ticket-booking' ) ); ?></label>
							</th>
							<td>
								<input type="text" name="wrapper-id" class="wrapper-id-value oneline option" id="<?php echo esc_attr( $args['content'] . '-wrapper-id' ); ?>" /><br>
								<em><?php esc_html_e( 'By default the markup that wraps this form item has a random ID. You can customize it here. If you\'re unsure, leave blank.', 'ticket-booking' ); ?></em>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $args['content'] . '-defaultautoselected' ); ?>"><?php esc_html_e( 'Default auto selected (optional)', 'ticket-booking' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="defaultautoselected:true" id="<?php echo esc_attr( $args['content'] . '-defaultautoselected' ); ?>" class="validautocompletevalue option" /><br />
								<em><?php esc_html_e( 'Select all tickets checked by default.', 'ticket-booking' ); ?></em>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $args['content'] . '-move-inline-css' ); ?>"><?php echo esc_html( __( 'Move inline CSS (optional)', 'ticket-booking' ) ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="move-inline-css:true" id="<?php echo esc_attr( $args['content'] . '-move-inline-css' ); ?>" class="move-inline-css-value option" /><br />
								<em><?php esc_html_e( 'Moves the CSS to hide the ticket_book_cf7 from the element to the footer of the page. May help confuse bots.', 'ticket-booking' ); ?></em>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $args['content'] . '-nomessage' ); ?>"><?php echo esc_html( __( 'Disable Accessibility Label (optional)', 'ticket-booking' ) ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="nomessage:true" id="<?php echo esc_attr( $args['content'] . '-nomessage' ); ?>" class="messagekillvalue option" /><br />
								<em><?php esc_html_e( 'If checked, the accessibility label will not be generated. This is not recommended, but may improve spam blocking. If you\'re unsure, leave this unchecked.', 'ticket-booking' ); ?></em>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $args['content'] . '-required' ); ?>"><?php echo esc_html( __( 'Field type', 'ticket-booking' ) ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="required:true" id="<?php echo esc_attr( $args['content'] . '-required' ); ?>" class=" option" /><br />
								<em><?php esc_html_e( 'Required field', 'ticket-booking' ); ?></em>
							</td>
						</tr>
					</tbody></table>
				</fieldset>
			</div>

			<div class="insert-box">
				<input type="text" name="ticket_book_cf7" class="tag code" readonly="readonly" onfocus="this.select()" />

				<div class="submitbox">
					<input type="button" class="button button-primary insert-tag" value="<?php esc_attr_e( 'Insert Tag', 'ticket-booking' ); ?>" />
				</div>

				<br class="clear" />
			</div>
		<?php } else { ?>
			<div id="wpcf7-tg-pane-ticket_book_cf7" class="hidden">
				<form action="">
					<table>
						<tr>
							<td>
								<?php esc_html_e( 'Name', 'ticket-booking' ); ?><br />
								<input type="text" name="name" class="tg-name oneline" /><br />
								<em><small><?php esc_html_e( 'For better security, change "ticket_book_cf7" to something less bot-recognizable.', 'ticket-booking' ); ?></small></em>
							</td>
							<td></td>
						</tr>

						<tr>
							<td colspan="2"><hr></td>
						</tr>

						<tr>
							<td>
								<?php esc_html_e( 'ID (optional)', 'ticket-booking' ); ?>
								<br />
								<input type="text" name="id" class="idvalue oneline option" />
							</td>
							<td>
								<?php esc_html_e( 'Class (optional)', 'ticket-booking' ); ?><br />
								<input type="text" name="class" class="classvalue oneline option" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Field type', 'ticket-booking' ); ?></th>
							<td>
								<fieldset>
								<legend class="screen-reader-text"><?php esc_html_e( 'Field type', 'ticket-booking' ); ?></legend>
								<label><input type="checkbox" name="required" /> <?php esc_html__( 'Required field', 'ticket-booking' ); ?></label>
								</fieldset>
							</td>
						</tr>

						<tr>
							<td colspan="2"><hr></td>
						</tr>			
					</table>

					<div class="tg-tag"><?php esc_html_e( 'Copy this code and paste it into the form left.', 'ticket-booking' ); ?><br /><input type="text" name="ticket_book_cf7" class="tag" readonly="readonly" onfocus="this.select()" /></div>
				</form>
			</div>
			<?php
		}

	}
}
