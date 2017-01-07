<?php
/**
 * Super Simple CRM Form
 *
 * @since 0.0.1
 * @package Super Simple CRM
 */

/**
 * Super Simple CRM Form.
 *
 * @since 0.0.1
 */
class SSCRM_Form {
	/**
	 * Parent plugin class
	 *
	 * @since  0.0.1
	 *
	 * @var class
	 */
	protected $plugin = null;

	/**
	 * The AJAX URL for posting customer responses.
	 *
	 * @since  0.0.1
	 *
	 * @var string
	 */
	public $ajax_url = '';

	/**
	 * Constructor
	 *
	 * @since  0.0.1
	 *
	 * @param  object $plugin Main plugin object.
	 *
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		/**
		 * Allows filtering of the AJAX URL for submission
		 *
		 * @since 0.0.1
		 *
		 * @param string $url The AJAX URL
		 */
		$this->ajax_url = apply_filters( 'sscrm_ajax_url', get_home_url() . '/wp-json/sscrm/add' );
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function hooks() {
		add_shortcode( 'sscrm_form', array( $this, 'shortcode' ) );
		add_action( 'rest_api_init', array( $this, 'rest_api_endpoint' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );
	}

	/**
	 * Enqueue scripts and styles on the front-end.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function frontend_enqueue() {
		wp_enqueue_script( 'super-simple-crm', $this->plugin->url . 'assets/sscrm.js', array( 'jquery' ), '0.0.1', true );
		wp_enqueue_style( 'super-simple-crm', $this->plugin->url . 'assets/sscrm.css', array(), '0.0.1' );
		wp_localize_script( 'super-simple-crm', 'sscrm_args', array(
			'ajax_url' => esc_url( $this->ajax_url ),
		) );
	}

	/**
	 * Renders the data collection form.
	 *
	 * @since 0.0.1
	 *
	 * @param array $atts {
	 *      An array of attributes that can be passed to the form for modification.
	 *
	 *      @type bool $name_required    Require the name field
	 *      @type bool $phone_required   Require the phone field
	 *      @type bool $email_required   Require the email field
	 *      @type bool $budget_required  Require the budget field
	 *      @type bool $message_required Require the message field
	 *      @type string $name_label Label text for the name field
	 *      @type string $phone_label Label text for the phone field
	 *      @type string $email_label Label text for the email field
	 *      @type string $budget_label Label text for the budget field
	 *      @type string $message_label Label text for the message field
	 *      @type int $name_maxlength The maxlength of the name field
	 *      @type int $phone_maxlength The maxlength of the phone field
	 *      @type int $email_maxlength The maxlength of the email field
	 *      @type int $budget_maxlength The maxlength of the budget field
	 *      @type int $message_maxlength The maxlength of the message field
	 *      @type string $name_placeholder The placeholder text in the name field
	 *      @type string $phone_placeholder The placeholder text in the phone field
	 *      @type string $email_placeholder The placeholder text in the email field
	 *      @type string $budget_placeholder The placeholder text in the budget field
	 *      @type string $message_placeholder The placeholder text in the message field
	 *      @type string $thank_you The thank you message after submission
	 *      @type string $sending_message The message while the form is sending
	 *      @type string $fail_message The message if the form fails
	 *      @type string $submit_text The text within the submit button
	 * }
	 *
	 * @return void
	 */
	public function render( $atts ) {
		$atts = wp_parse_args( $atts, array(
			'name_required'       => true,
			'phone_required'      => true,
			'email_required'      => true,
			'budget_required'     => true,
			'message_required'    => true,
			'name_label'          => esc_html__( 'Name:', 'super-simple-crm' ),
			'phone_label'         => esc_html__( 'Phone Number:', 'super-simple-crm' ),
			'email_label'         => esc_html__( 'Email Address:', 'super-simple-crm' ),
			'budget_label'        => esc_html__( 'Desired Budget:', 'super-simple-crm' ),
			'message_label'       => esc_html__( 'Message:', 'super-simple-crm' ),
			'name_maxlength'      => 0,
			'phone_maxlength'     => 0,
			'email_maxlength'     => 0,
			'budget_maxlength'    => 0,
			'message_maxlength'   => 0,
			'message_rows'        => 0,
			'message_cols'        => 0,
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
		<?php
		/**
		 * Fires before the form container.
		 *
		 * @since 0.0.1
		 *
		 * @param array $atts The array of attributes for modifying the form.
		 */
		do_action( 'sscrm_before_form_container', $atts );
		?>
		<div id="sscrm_form_container">
			<div class="grayed-out">
				<div class="done-message">
					<?php
					/**
					 * Fires before the form done message.
					 *
					 * @since 0.0.1
					 *
					 * @param array $atts The array of attributes for modifying the form.
					 */
					do_action( 'sscrm_before_done_message', $atts );
					?>
					<p><?php echo esc_html( $atts['thank_you'] ); ?></p>
					<?php
					/**
					 * Fires after the form done message.
					 *
					 * @since 0.0.1
					 *
					 * @param array $atts The array of attributes for modifying the form.
					 */
					do_action( 'sscrm_after_done_message', $atts );
					?>
				</div>
				<div class="sending-message">
					<?php
					/**
					 * Fires before the form sending message.
					 *
					 * @since 0.0.1
					 *
					 * @param array $atts The array of attributes for modifying the form.
					 */
					do_action( 'sscrm_before_sending_message', $atts );
					?>
					<div class="spinner"></div>
					<p><?php echo esc_html( $atts['sending_message'] ); ?></p>
					<?php
					/**
					 * Fires after the form sending message.
					 *
					 * @since 0.0.1
					 *
					 * @param array $atts The array of attributes for modifying the form.
					 */
					do_action( 'sscrm_after_sending_message', $atts );
					?>
				</div>
				<div class="fail-message">
					<?php
					/**
					 * Fires before the form fail message.
					 *
					 * @since 0.0.1
					 *
					 * @param array $atts The array of attributes for modifying the form.
					 */
					do_action( 'sscrm_before_fail_message', $atts );
					?>
					<p><?php echo esc_html( $atts['fail_message'] ); ?></p>
					<?php
					/**
					 * Fires after the form fail message.
					 *
					 * @since 0.0.1
					 *
					 * @param array $atts The array of attributes for modifying the form.
					 */
					do_action( 'sscrm_after_fail_message', $atts );
					?>
				</div>
			</div>
			<?php
			/**
			 * Fires before the form tag.
			 *
			 * @since 0.0.1
			 *
			 * @param array $atts The array of attributes for modifying the form.
			 */
			do_action( 'sscrm_before_form', $atts );
			?>
			<form id="sscrm_form">
				<?php
				/**
				 * Fires at the top inside the form.
				 *
				 * @since 0.0.1
				 *
				 * @param array $atts The array of attributes for modifying the form.
				 */
				do_action( 'sscrm_form_top', $atts );
				?>
				<label for="sscrm_name"><?php echo esc_html( $atts['name_label'] ); ?></label><?php if ( $atts['name_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_name" name="sscrm_name" type="text"<?php echo $atts['name_placeholder'] ? ' placeholder="' . esc_attr( $atts['name_placeholder'] ) . '"' : ''; echo $atts['name_required'] ? ' required' : ''; echo $atts['name_maxlength'] ? ' maxlength="' . absint( $atts['name_maxlength'] ) . '"' : ''; ?> /><br/>
				<label for="sscrm_phone"><?php echo esc_html( $atts['phone_label'] ); ?></label><?php if ( $atts['phone_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_phone" name="sscrm_phone" type="tel"<?php echo $atts['phone_placeholder'] ? ' placeholder="' . esc_attr( $atts['phone_placeholder'] ) . '"' : ''; echo $atts['name_required'] ? ' required' : ''; echo $atts['phone_maxlength'] ? ' maxlength="' . absint( $atts['phone_maxlength'] ) . '"' : ''; ?> /><br/>
				<label for="sscrm_email"><?php echo esc_html( $atts['email_label'] ); ?></label><?php if ( $atts['email_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_email" name="sscrm_email" type="email"<?php echo $atts['email_placeholder'] ? ' placeholder="' . esc_attr( $atts['email_placeholder'] ) . '"' : ''; echo $atts['name_required'] ? ' required' : ''; echo $atts['email_maxlength'] ? ' maxlength="' . absint( $atts['email_maxlength'] ) . '"' : ''; ?> /><br/>
				<label for="sscrm_budget"><?php echo esc_html( $atts['budget_label'] ); ?></label><?php if ( $atts['budget_required'] ) { ?> <span class="required">*</span><?php } ?> <input id="sscrm_budget" name="sscrm_budget" type="text"<?php echo $atts['budget_placeholder'] ? ' placeholder="' . esc_attr( $atts['budget_placeholder'] ) . '"' : ''; echo $atts['name_required'] ? ' required' : ''; echo $atts['budget_maxlength'] ? ' maxlength="' . absint( $atts['budget_maxlength'] ) . '"' : ''; ?> /><br/>
				<label for="sscrm_message"><?php echo esc_html( $atts['message_label'] ); ?></label><?php if ( $atts['message_required'] ) { ?> <span class="required">*</span><?php } ?> <textarea id="sscrm_message" name="sscrm_message"<?php echo $atts['message_placeholder'] ? ' placeholder="' . esc_attr( $atts['message_placeholder'] ) . '"' : ''; echo $atts['message_required'] ? ' required' : ''; echo $atts['message_maxlength'] ? ' maxlength="' . absint( $atts['message_maxlength'] ) . '"' : ''; echo $atts['message_rows'] ? ' rows="' . absint( $atts['message_rows'] ) . '"' : ''; echo $atts['message_cols'] ? ' cols="' . absint( $atts['message_cols'] ) . '"' : ''; ?>></textarea>
				<?php wp_nonce_field( 'wp_rest' ); ?>
				<?php
				/**
				 * Fires right before the submit button.
				 *
				 * @since 0.0.1
				 *
				 * @param array $atts The array of attributes for modifying the form.
				 */
				do_action( 'sscrm_before_form_submit', $atts );
				?>
				<input id="sscrm_submit" name="sscrm_submit" type="submit" value="<?php echo esc_html( $atts['submit_text'] ); ?>" />
				<?php
				/**
				 * Fires right after the submit button.
				 *
				 * @since 0.0.1
				 *
				 * @param array $atts The array of attributes for modifying the form.
				 */
				do_action( 'sscrm_after_form_submit', $atts );
				?>
			</form>
			<?php
			/**
			 * Fires right after the form closing tag.
			 *
			 * @since 0.0.1
			 *
			 * @param array $atts The array of attributes for modifying the form.
			 */
			do_action( 'sscrm_after_form', $atts );
			?>
		</div>
		<?php
		/**
		 * Fires after the form container.
		 *
		 * @since 0.0.1
		 *
		 * @param array $atts The array of attributes for modifying the form.
		 */
		do_action( 'sscrm_after_form_container', $atts );
	}

	/**
	 * Creates the shortcode to display the form within content areas
	 *
	 * @since 0.0.1
	 *
	 * @param array $atts {
	 *      An array of attributes that can be passed to the form for modification.
	 *
	 *      @type bool $name_required    Require the name field
	 *      @type bool $phone_required   Require the phone field
	 *      @type bool $email_required   Require the email field
	 *      @type bool $budget_required  Require the budget field
	 *      @type bool $message_required Require the message field
	 *      @type string $name_label Label text for the name field
	 *      @type string $phone_label Label text for the phone field
	 *      @type string $email_label Label text for the email field
	 *      @type string $budget_label Label text for the budget field
	 *      @type string $message_label Label text for the message field
	 *      @type int $name_maxlength The maxlength of the name field
	 *      @type int $phone_maxlength The maxlength of the phone field
	 *      @type int $email_maxlength The maxlength of the email field
	 *      @type int $budget_maxlength The maxlength of the budget field
	 *      @type int $message_maxlength The maxlength of the message field
	 *      @type string $name_placeholder The placeholder text in the name field
	 *      @type string $phone_placeholder The placeholder text in the phone field
	 *      @type string $email_placeholder The placeholder text in the email field
	 *      @type string $budget_placeholder The placeholder text in the budget field
	 *      @type string $message_placeholder The placeholder text in the message field
	 *      @type string $thank_you The thank you message after submission
	 *      @type string $sending_message The message while the form is sending
	 *      @type string $fail_message The message if the form fails
	 *      @type string $submit_text The text within the submit button
	 * }
	 *
	 * @return string HTML of the form to display using a shortcode
	 */
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

		/**
		 * Allows filtering and modification of the full form
		 *
		 * @since 0.0.1
		 *
		 * @param string $form The HTML of the form
		 */
		return apply_filters( 'sscrm_shortcode_form', $form );
	}

	/**
	 * Registers a REST API endpoint for adding CRM entries
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function rest_api_endpoint() {
		register_rest_route( 'sscrm', 'add', array(
			'methods'       => 'POST',
			'callback'      => array( $this, 'process_form_input' ),
			'show_in_index' => false,
		) );
	}

	/**
	 * Processes the input recieved through the REST API
	 *
	 * @since 0.0.1
	 *
	 * @param WP_REST_Request $request The request object from the API Submission
	 *
	 * @return mixed|WP_REST_Response A response including submitted data
	 */
	public function process_form_input( WP_REST_Request $request ) {
		$params = $request->get_params();
		check_ajax_referer( 'wp_rest' );
		/**
		 * Allows filtering of the URL used to fetch time from external API
		 *
		 * @since 0.0.1
		 *
		 * @param string $url The URL used to fetch time
		 */
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
		/**
		 * Allows filtering of the customer data after submitted.
		 *
		 * @since 0.0.1
		 *
		 * @param array  $customer_data A formatted array of submitted customer data.
		 * @param array  $params        An array of all params from the API request
		 * @param object $request       The full API Request
		 */
		$customer_data = apply_filters( 'sscrm_customer_data_to_input', $customer_data, $params, $request );
		$this->insert_post( $customer_data );

		return rest_ensure_response( $customer_data );
	}

	/**
	 * Inserts the post in the database after submission from the form through the REST API
	 *
	 * @since 0.0.1
	 *
	 * @param $customer_data
	 */
	public function insert_post( $customer_data ) {
		/**
		 * Fires before a post is inserted from the API.
		 *
		 * @since 0.0.1
		 *
		 * @param array $customer_data The array of customer data submitted.
		 */
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
		/**
		 * Fires after a post is submitted from the API.
		 *
		 * @since 0.0.1
		 *
		 * @param array $customer_data The array of customer data submitted.
		 */
		do_action( 'sscrm_after_insert_post', $customer_data, $post_id );

		unset( $customer_data['message'] );
		unset( $customer_data['name'] );
		unset( $customer_data['time'] );

		array_walk( $customer_data, array( $this, 'sanitize_meta' ), $post_id );
		array_walk( $customer_data, array( $this, 'add_meta' ), $post_id );
	}

	/**
	 * Adds meta values to the proper posts when inserting customer data posts.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed  $meta    The meta values to be inserted
	 * @param string $key     The key of the meta to be inserted
	 * @param int    $post_id The ID of the current post
	 *
	 * @return bool|int The post meta ID if successful, false if not
	 */
	public function add_meta( $meta, $key, $post_id ) {
		/**
		 * Fires before a meta value is saved.
		 *
		 * @since 0.0.1
		 *
		 * @param mixed $meta     The meta value.
		 * @param string $key     The meta key.
		 * @parak int    $post_id The current post ID.
		 */
		do_action( 'sscrm_before_add_' . $key . '_meta', $meta, $key, $post_id );
		$update = update_post_meta( $post_id, 'sscrm_' . $key, $meta );
		/**
		 * Fires after a meta value is saved.
		 *
		 * @since 0.0.1
		 *
		 * @param mixed $meta     The meta value.
		 * @param string $key     The meta key.
		 * @parak int    $post_id The current post ID.
		 */
		do_action( 'sscrm_after_add_' . $key . '_meta', $meta, $key, $post_id );
		return $update;
	}

	/**
	 * Runs the meta through sanitization before inserting in database.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed  $meta The meta value being inserted
	 * @param string $key  The key of the meta being inserted
	 * @param int$post_id  The ID of the current post
	 *
	 * @return mixed The sanitized value
	 */
	public function sanitize_meta( $meta, $key, $post_id ) {
		/**
		 * Fires before a meta value is sanitized.
		 *
		 * @since 0.0.1
		 *
		 * @param mixed $meta     The meta value.
		 * @param string $key     The meta key.
		 * @parak int    $post_id The current post ID.
		 */
		do_action( 'sscrm_before_sanitize_' . $key . '_meta', $meta, $key, $post_id );
		/**
		 * Allows addition or modification of sanitize functions for submitted data
		 *
		 * @since 0.0.1
		 *
		 * @param array $sanitize_functions An array of meta keys and related sanitize functions
		 */
		$sanitize_functions = apply_filters( 'sscrm_meta_sanitize_functions', array(
			'email' => 'sanitize_email',
		) );
		if ( ! isset( $sanitize_functions[ $key ] ) ) {
			$sanitized = sanitize_text_field( $meta );
		} else {
			$sanitized = call_user_func( $sanitize_functions[ $key ], $meta );
		}

		/**
		 * Fires after a meta value is sanitized.
		 *
		 * @since 0.0.1
		 *
		 * @param mixed $meta     The meta value.
		 * @param string $key     The meta key.
		 * @parak int    $post_id The current post ID.
		 */
		do_action( 'sscrm_after_sanitize_' . $key . '_meta', $meta, $key, $post_id );
		return $sanitized;
	}
}
