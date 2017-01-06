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

	public function render() {
		?>
		<form id="sscrm_form">
			<label for="sscrm_name">Name:</label> <input id="sscrm_name" name="sscrm_name" type="text" /><br/>
			<label for="sscrm_phone">Phone Number:</label> <input id="sscrm_phone" name="sscrm_phone" type="tel" /><br/>
			<label for="sscrm_email">Email Address:</label> <input id="sscrm_email" name="sscrm_email" type="email" /><br/>
			<label for="sscrm_budget">Desired Budget:</label> <input id="sscrm_budget" name="sscrm_budget" type="text" /><br/>
			<label for="sscrm_message">Message:</label> <textarea id="sscrm_message" name="sscrm_message"></textarea>
			<button id="sscrm_submit">Send</button>
		</form>
		<script>
			jQuery( document ).ready( function( $ ) {
				$('#sscrm_submit').click( function( e ) {
					e.preventDefault();
					$.post(
						'<?php echo esc_url( $this->ajax_url ); ?>',
						$( "#sscrm_form" ).serialize(),
						function( data ) {
							console.log( data );
						}
					);
				} )
			} );
		</script>
		<?php
	}

	public function shortcode() {
		ob_start();
		$this->render();
		$form = ob_get_clean();

		return apply_filters( 'sscrm_shortcode_form', $form );
	}

	public function rest_api_endpoint() {
		register_rest_route( 'sscrm', 'add', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'process_form_input' ),

		) );
	}

	public function process_form_input( WP_REST_Request $request ) {
		$params = $request->get_params();
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
