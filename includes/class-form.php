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
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );
	}

	public function frontend_enqueue() {
		wp_enqueue_script( 'super-simple-crm', $this->plugin->url . 'assets/sscrm.js', array( 'jquery' ), '0.0.1', true );
		wp_enqueue_style( 'super-simple-crm', $this->plugin->url . 'assets/sscrm.css', array(), '0.0.1' );
		wp_localize_script( 'super-simple-crm', 'sscrm_args', array(
			'ajax_url' => esc_url( $this->ajax_url ),
		) );
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
			'name_maxlength'      => '',
			'phone_maxlength'     => '',
			'email_maxlength'     => '',
			'budget_maxlength'    => '',
			'message_maxlength'   => '',
			'message_rows'        => '',
			'message_cols'        => '',
			'name_placeholder'    => '',
			'phone_placeholder'   => '',
			'email_placeholder'   => '',
			'budget_placeholder'  => '',
			'message_placeholder' => '',
			'thank_you'           => esc_html__( 'Thank you for contacting us. We will contact you shortly to discuss.', 'super-simple-crm' ),
			'sending_message'     => esc_html__( 'Sending...', 'super-simple-crm' ),
			'fail_message'        => esc_html__( 'Oops. Something went wrong. Try again and if it keeps up, let us know.', 'super-simple-crm' ),
			'submit_text'         => esc_html__( 'Send', 'sumper-simple-crm' ),
		) )
		?>
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
				<div class="fail-message">
					<?php do_action( 'sscrm_before_fail_message', $atts ); ?>
					<p><?php echo esc_html( $atts['fail_message'] ); ?></p>
					<?php do_action( 'sscrm_after_fail_message', $atts ); ?>
				</div>
			</div>
			<?php do_action( 'sscrm_before_form', $atts ); ?>
			<form id="sscrm_form">
				<?php do_action( 'sscrm_form_top', $atts ); ?>
				<label for="sscrm_name"><?php echo esc_html( $atts['name_label'] ); ?></label><?php if ( $atts['name_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_name" name="sscrm_name" type="text"<?php echo $atts['name_placeholder'] ? ' placeholder="' . esc_attr( $atts['name_placeholder'] ) . '"' : ''; echo $atts['name_required'] ? ' required' : ''; echo $atts['name_maxlength'] ? ' maxlength="' . absint( $atts['name_maxlength'] ) . '"' : ''; ?> /><br/>
				<label for="sscrm_phone"><?php echo esc_html( $atts['phone_label'] ); ?></label><?php if ( $atts['phone_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_phone" name="sscrm_phone" type="tel"<?php echo $atts['phone_placeholder'] ? ' placeholder="' . esc_attr( $atts['phone_placeholder'] ) . '"' : ''; echo $atts['name_required'] ? ' required' : ''; echo $atts['phone_maxlength'] ? ' maxlength="' . absint( $atts['phone_maxlength'] ) . '"' : ''; ?> /><br/>
				<label for="sscrm_email"><?php echo esc_html( $atts['email_label'] ); ?></label><?php if ( $atts['email_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_email" name="sscrm_email" type="email"<?php echo $atts['email_placeholder'] ? ' placeholder="' . esc_attr( $atts['email_placeholder'] ) . '"' : ''; echo $atts['name_required'] ? ' required' : ''; echo $atts['email_maxlength'] ? ' maxlength="' . absint( $atts['email_maxlength'] ) . '"' : ''; ?> /><br/>
				<label for="sscrm_budget"><?php echo esc_html( $atts['budget_label'] ); ?></label><?php if ( $atts['budget_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_budget" name="sscrm_budget" type="text"<?php echo $atts['budget_placeholder'] ? ' placeholder="' . esc_attr( $atts['budget_placeholder'] ) . '"' : ''; echo $atts['name_required'] ? ' required' : ''; echo $atts['budget_maxlength'] ? ' maxlength="' . absint( $atts['budget_maxlength'] ) . '"' : ''; ?> /><br/>
				<label for="sscrm_message"><?php echo esc_html( $atts['message_label'] ); ?></label><?php if ( $atts['message_required'] ) { ?> <span class="required">*</span><?php } ?> <textarea id="sscrm_message" name="sscrm_message"<?php echo $atts['message_placeholder'] ? ' placeholder="' . esc_attr( $atts['message_placeholder'] ) . '"' : ''; echo $atts['message_required'] ? ' required' : ''; echo $atts['message_maxlength'] ? ' maxlength="' . absint( $atts['message_maxlength'] ) . '"' : ''; ?>></textarea>
				<?php wp_nonce_field( 'wp_rest' ); ?>
				<?php do_action( 'sscrm_before_form_submit', $atts ); ?>
				<input id="sscrm_submit" name="sscrm_submit" type="submit" value="<?php echo esc_html( $atts['submit_text'] ); ?>" />
				<?php do_action( 'sscrm_after_form_submit', $atts ); ?>
			</form>
			<?php do_action( 'sscrm_after_form', $atts ); ?>
		</div>
		<?php do_action( 'sscrm_after_form_container', $atts ); ?>
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
			'name_maxlength'      => '',
			'phone_maxlength'     => '',
			'email_maxlength'     => '',
			'budget_maxlength'    => '',
			'message_maxlength'   => '',
			'message_rows'        => '',
			'message_cols'        => '',
			'name_placeholder'    => '',
			'phone_placeholder'   => '',
			'email_placeholder'   => '',
			'budget_placeholder'  => '',
			'message_placeholder' => '',
			'thank_you'           => esc_html__( 'Thank you for contacting us. We will contact you shortly to discuss.', 'super-simple-crm' ),
			'sending_message'     => esc_html__( 'Sending...', 'super-simple-crm' ),
			'fail_message'        => esc_html__( 'Oops. Something went wrong. Try again and if it keeps up, let us know.', 'super-simple-crm' ),
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
			'name'    => $params['sscrm_name'],
			'phone'   => $params['sscrm_phone'],
			'email'   => $params['sscrm_email'],
			'budget'  => $params['sscrm_budget'],
			'message' => $params['sscrm_message'],
			'time'    => $timestamp,
		);
		$customer_data = apply_filters( 'sscrm_customer_data_to_input', $customer_data, $params, $request );
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

		unset( $customer_data['message'] );
		unset( $customer_data['name'] );
		unset( $customer_data['time'] );

		array_walk( $customer_data, array( $this, 'sanitize_meta' ), $post_id );
		array_walk( $customer_data, array( $this, 'add_meta' ), $post_id );
	}

	public function add_meta( $meta, $key, $post_id ) {
		do_action( 'sscrm_before_add_' . $key . '_meta', $meta, $key, $post_id );
		$update = update_post_meta( $post_id, 'sscrm_' . $key, $meta );
		do_action( 'sscrm_after_add_' . $key . '_meta', $meta, $key, $post_id );
		return $update;
	}

	public function sanitize_meta( $meta, $key, $post_id ) {
		do_action( 'sscrm_before_sanitize_' . $key . '_meta', $meta, $key, $post_id );
		$sanitize_functions = apply_filters( 'sscrm_meta_sanitize_functions', array(
			'email' => 'sanitize_email',
		) );
		if ( ! isset( $sanitize_functions[ $key ] ) ) {
			$sanitized = sanitize_text_field( $meta );
		} else {
			$sanitized = call_user_func( $sanitize_functions[ $key ], $meta );
		}


		do_action( 'sscrm_after_sanitize_' . $key . '_meta', $meta, $key, $post_id );
		return $sanitized;
	}
}
