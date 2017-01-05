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
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  NEXT
	 * @return void
	 */
	public function hooks() {
		add_shortcode( 'sscrm_form', array( $this, 'shortcode' ) );
	}

	public function render() {
		?>
		<form>
			<label for="sscrm_name">Name:</label> <input id="sscrm_name" name="sscrm_name" type="text" /><br/>
			<label for="sscrm_phone">Phone Number:</label> <input id="sscrm_phone" name="sscrm_phone" type="tel" /><br/>
			<label for="sscrm_email">Email Address:</label> <input id="sscrm_email" name="sscrm_email" type="email" /><br/>
			<label for="sscrm_budget">Desired Budget:</label> <input id="sscrm_budget" name="sscrm_budget" type="text" /><br/>
			<label for="sscrm_message">Message:</label> <textarea id="sscrm_message" name="sscrm_message"></textarea>
			<input type="submit" value="Send" />
		</form>
		<?php
	}

	public function shortcode() {
		ob_start();
		$this->render();
		$form = ob_get_clean();

		return apply_filters( 'sscrm_shortcode_form', $form );
	}
}
