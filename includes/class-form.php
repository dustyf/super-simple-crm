<?php
/**
 * Super Simple CRM Form
 *
 * @since NEXT
 * @package Super Simple CRM
 */

/**
 * Super Simple CRM Form.
 *
 * @since NEXT
 */
class SSCRM_Form {
	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since NEXT
	 */
	protected $plugin = null;

	public $ajax_url = '';

	/**
	 * Constructor
	 *
	 * @since  NEXT
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		$this->ajax_url = apply_filters( 'sscrm_ajax_url', get_home_url() . '/wp-json/sscrm/add' );
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  NEXT
	 * @return void
	 */
	public function hooks() {
		add_shortcode( 'sscrm_form', array( $this, 'shortcode' ) );
		add_action( 'rest_api_init', array( $this, 'rest_api_endpoint' ) );
	}

	public function render( $atts ) {
		$atts = wp_parse_args( $atts, array(
			'name_required'       => 'true',
			'phone_required'      => 'true',
			'email_required'      => 'true',
			'budget_required'     => 'true',
			'message_required'    => 'true',
			'name_label'          => esc_html__( 'Name:', 'super-simple-crm' ),
			'phone_label'         => esc_html__( 'Phone Number:', 'super-simple-crm' ),
			'email_label'         => esc_html__( 'Email Address:', 'super-simple-crm' ),
			'budget_label'        => esc_html__( 'Desired Budget:', 'super-simple-crm' ),
			'message_label'       => esc_html__( 'Message:', 'super-simple-crm' ),
			'name_max_length'     => '',
			'phone_max_length'    => '',
			'email_max_length'    => '',
			'budget_max_length'   => '',
			'message_max_length'  => '',
			'message_rows'        => '',
			'message_cols'        => '',
			'name_placeholder'    => '',
			'phone_placeholder'   => '',
			'email_placeholder'   => '',
			'budget_placeholder'  => '',
			'message_placeholder' => '',
			'thank_you'           => esc_html__( 'Thank you for contacting us. We will contact you shortly to discuss.', 'super-simple-crm' ),
			'sending_message'     => esc_html__( 'Sending...', 'super-simple-crm' ),
			'submit_text'         => esc_html__( 'Send', 'sumper-simple-crm' ),
		) )
		?>
		<style>
			.spinner {
				background: url('/wp-admin/images/wpspin_light.gif') no-repeat;
				background-size: 16px 16px;
				opacity: .7;
				filter: alpha(opacity=70);
				width: 16px;
				height: 16px;
				margin: 5px 5px 0;
				display: inline-block;
			}
			#sscrm_form_container {
				position: relative;
				padding: 32px;

			}
			#sscrm_form_container .grayed-out {
				background-color: rgba( 0,0,0,0.7 );
				width: 100%;
				height: 100%;
				display: none;
				position: absolute;
				top: 0;
				left: 0;
				color: #fff;
				padding: 32px;
				text-align: center;
			}
			#sscrm_form_container .done-message {
				display: none;
			}
		</style>
		<?php do_action( 'sscrm_before_form_container', $atts ); ?>
		<div id="sscrm_form_container">
			<div class="grayed-out">
				<div class="done-message">
					<?php do_action( 'sscrm_before_done_message', $atts ); ?>
					<p><?php echo esc_html( $atts['thank_you'] ); ?></p>
					<?php do_action( 'sscrm_after_done_message', $atts ); ?>
				</div>
				<div class="sending-message">
					<?php do_action( 'sscrm_before_sending_message', $atts ); ?>
					<div class="spinner"></div>
					<p><?php echo esc_html( $atts['sending_message'] ); ?></p>
					<?php do_action( 'sscrm_after_sending_message', $atts ); ?>
				</div>
			</div>
			<?php do_action( 'sscrm_before_form', $atts ); ?>
			<form id="sscrm_form">
				<?php do_action( 'sscrm_form_top', $atts ); ?>
				<label for="sscrm_name"><?php echo esc_html( $atts['name_label'] ); ?></label><?php if ( $atts['name_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_name" name="sscrm_name" type="text" required /><br/>
				<label for="sscrm_phone"><?php echo esc_html( $atts['phone_label'] ); ?></label><?php if ( $atts['phone_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_phone" name="sscrm_phone" type="tel" required /><br/>
				<label for="sscrm_email"><?php echo esc_html( $atts['email_label'] ); ?></label><?php if ( $atts['email_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_email" name="sscrm_email" type="email" required /><br/>
				<label for="sscrm_budget"><?php echo esc_html( $atts['budget_label'] ); ?></label><?php if ( $atts['budget_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_budget" name="sscrm_budget" type="text" required /><br/>
				<label for="sscrm_message"><?php echo esc_html( $atts['message_label'] ); ?></label><?php if ( $atts['message_required'] ) { ?> <span class="required">*</span><?php } ?> <textarea id="sscrm_message" name="sscrm_message" required></textarea>
				<?php wp_nonce_field( 'wp_rest' ); ?>
				<?php do_action( 'sscrm_before_form_submit', $atts ); ?>
				<input id="sscrm_submit" name="sscrm_submit" type="submit" value="<?php echo esc_html( $atts['submit_text'] ); ?>" />
				<?php do_action( 'sscrm_after_form_submit', $atts ); ?>
			</form>
			<?php do_action( 'sscrm_after_form', $atts ); ?>
		</div>
		<?php do_action( 'sscrm_after_form_container', $atts ); ?>
		<script>
			jQuery( document ).ready( function( $ ) {
				$( '#sscrm_form' ).submit( function( e ) {
					e.preventDefault();
					$( '#sscrm_form_container .grayed-out' ).show();
					$( '#sscrm_form .spinner' ).show();
					$( '#sscrm_submit' ).prop( 'disabled', true );
					$.post(
						'<?php echo esc_url( $this->ajax_url ); ?>',
						$( "#sscrm_form" ).serialize(),
						function( data ) {
							$( '#sscrm_form_container .sending-message' ).hide();
							$( '#sscrm_form_container .done-message' ).show();
						}
					).fail( function( data ) {
						$( '#sscrm_form_container .sending-message' ).hide();
						$( '#sscrm_form_container .fail-message' ).show();
						console.log( data );
					} );
				} )
			} );
		</script>
		<?php
	}

	public function shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'name_required'       => 'true',
			'phone_required'      => 'true',
			'email_required'      => 'true',
			'budget_required'     => 'true',
			'message_required'    => 'true',
			'name_label'          => esc_html__( 'Name:', 'super-simple-crm' ),
			'phone_label'         => esc_html__( 'Phone Number:', 'super-simple-crm' ),
			'email_label'         => esc_html__( 'Email Address:', 'super-simple-crm' ),
			'budget_label'        => esc_html__( 'Desired Budget:', 'super-simple-crm' ),
			'message_label'       => esc_html__( 'Message:', 'super-simple-crm' ),
			'name_max_length'     => '',
			'phone_max_length'    => '',
			'email_max_length'    => '',
			'budget_max_length'   => '',
			'message_max_length'  => '',
			'message_rows'        => '',
			'message_cols'        => '',
			'name_placeholder'    => '',
			'phone_placeholder'   => '',
			'email_placeholder'   => '',
			'budget_placeholder'  => '',
			'message_placeholder' => '',
			'thank_you'           => esc_html__( 'Thank you for contacting us. We will contact you shortly to discuss.', 'super-simple-crm' ),
			'sending_message'     => esc_html__( 'Sending...', 'super-simple-crm' ),
			'submit_text'         => esc_html__( 'Send', 'sumper-simple-crm' ),
		), $atts, 'sscrm_form' );
		ob_start();
		$this->render( $atts );
		$form = ob_get_clean();

		return apply_filters( 'sscrm_shortcode_form', $form );
	}

	public function rest_api_endpoint() {
		register_rest_route( 'sscrm', 'add', array(
			'methods'       => 'POST',
			'callback'      => array( $this, 'process_form_input' ),
			'show_in_index' => false,
		) );
	}

	public function process_form_input( WP_REST_Request $request ) {
		$params = $request->get_params();
		check_ajax_referer( 'wp_rest' );
		$time_api = apply_filters( 'sscrm_time_api', 'http://www.timeapi.org/utc/now.json' );
		$time = wp_remote_get( $time_api );
		$time_json = json_decode( wp_remote_retrieve_body($time) );
		$timestamp = $time_json->dateString;
		$customer_data = array(
			'name'    => sanitize_text_field( $params['sscrm_name'] ),
			'phone'   => sanitize_text_field( $params['sscrm_phone'] ),
			'email'   => sanitize_email( $params['sscrm_email'] ),
			'budget'  => sanitize_text_field( $params['sscrm_budget'] ),
			'message' => sanitize_text_field( $params['sscrm_message'] ),
			'time'    => sanitize_text_field( $timestamp ),
		);
		$this->insert_post( $customer_data );

		return rest_ensure_response( $customer_data );
	}

	public function insert_post( $customer_data ) {
		do_action( 'sscrm_before_insert_post', $customer_data );
		$gmt = iso8601_to_datetime( $customer_data['time'] );
		$post_id = wp_insert_post( array(
			'post_type'     => 'sscrm_customer',
			'post_date'     => get_date_from_gmt( $gmt ),
			'post_date_gmt' => $gmt,
			'post_content'  => wp_strip_all_tags( $customer_data['message'] ),
			'post_title'    => wp_strip_all_tags( $customer_data['name'] ),
			'post_status'   => 'publish',
		) );
		do_action( 'sscrm_after_insert_post', $customer_data, $post_id );
		foreach ( $customer_data as $key => $meta ) {
			if ( in_array( $key, array( 'name', 'message', 'time' ) ) ) {
				continue;
			}
			$this->add_meta( $post_id, 'sscrm_' . $key, $meta );
		}
	}

	public function add_meta( $post_id, $key, $meta ) {
		do_action( 'sscrm_before_add_' . $key . '_meta', $meta, $key, $post_id );
		$update = update_post_meta( $post_id, $key, $meta );
		do_action( 'sscrm_after_add_' . $key . '_meta', $meta, $key, $post_id );
		return $update;
	}
}
