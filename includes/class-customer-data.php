<?php
/**
 * Super Simple CRM Customer Data
 *
 * @since 0.0.1
 * @package Super Simple CRM
 */

/**
 * Super Simple CRM Customer Data.
 *
 * @since 0.0.1
 */
class SSCRM_Customer_Data {
	/**
	 * Parent plugin class
	 *
	 * @since 0.0.1
	 *
	 * @var   class
	 */
	protected $plugin = null;

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
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function hooks() {
		add_filter( 'manage_edit-sscrm_customer_columns', array( $this, 'admin_columns' ) ) ;
		add_action( 'manage_sscrm_customer_posts_custom_column', array( $this, 'manage_columns' ), 10, 2 );
		add_filter( 'manage_edit-sscrm_customer_sortable_columns', array( $this, 'sortable_columns' ) );
		add_action( 'load-edit.php', array( $this, 'handle_sorting' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box') );
		add_action( 'save_post_sscrm_customer', array( $this, 'save_customer_data' ) );
	}

	/**
	 * Register the Customer custom post type for storing customer data.
	 *
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function register_customer_post_type() {
		register_post_type( 'sscrm_customer', array(
			'public'          => false,
			'show_ui'         => true,
			'menu_position'   => 30,
			'menu_icon'       => 'dashicons-groups',
			'capability_type' => 'post',
			'supports'        => array( 'title', 'editor' ),
			'rewrite'         => false,
			'query_var'       => false,
			'taxonomies'      => array( 'sscrm_customer_tag', 'sscrm_customer_category' ),
			'labels'          => array(
				'name'               => esc_html_x( 'Customers', 'post type general name', 'super-simple-crm' ),
				'singular_name'      => esc_html_x( 'Customer', 'post type singular name', 'super-simple-crm' ),
				'menu_name'          => esc_html_x( 'Customers', 'admin menu', 'super-simple-crm' ),
				'name_admin_bar'     => esc_html_x( 'Customer', 'add new on admin bar', 'super-simple-crm' ),
				'add_new'            => esc_html_x( 'Add New', 'customer', 'super-simple-crm' ),
				'add_new_item'       => esc_html__( 'Add New Customer', 'super-simple-crm' ),
				'new_item'           => esc_html__( 'New Customer', 'super-simple-crm' ),
				'edit_item'          => esc_html__( 'Edit Customer', 'super-simple-crm' ),
				'view_item'          => esc_html__( 'View Customer', 'super-simple-crm' ),
				'all_items'          => esc_html__( 'All Customers', 'super-simple-crm' ),
				'search_items'       => esc_html__( 'Search Customers', 'super-simple-crm' ),
				'parent_item_colon'  => esc_html__( 'Parent Customers:', 'super-simple-crm' ),
				'not_found'          => esc_html__( 'No Customers found.', 'super-simple-crm' ),
				'not_found_in_trash' => esc_html__( 'No Customers found in Trash.', 'super-simple-crm' )
			),
		) );
	}

	/**
	 * Register the custom taxonomies
	 *
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function register_customer_taxonomies() {
		// Register Tag Taxonomy
		register_taxonomy( 'sscrm_customer_tag', 'sscrm_customer', array(
			'public' => false,
			'show_ui' => true,
			'show_tagcloud' => false,
			'show_admin_column' => true,
			'hierarchical'      => false,
			'query_var'         => false,
			'rewrite'           => false,
			'labels'            => array(
				'name'              => esc_html_x( 'Tags', 'taxonomy general name', 'super-simple-crm' ),
				'singular_name'     => esc_html_x( 'Tag', 'taxonomy singular name', 'super-simple-crm' ),
				'search_items'      => esc_html__( 'Search Tags', 'super-simple-crm' ),
				'all_items'         => esc_html__( 'All Tags', 'super-simple-crm' ),
				'parent_item'       => esc_html__( 'Parent Tag', 'super-simple-crm' ),
				'parent_item_colon' => esc_html__( 'Parent Tag:', 'super-simple-crm' ),
				'edit_item'         => esc_html__( 'Edit Tag', 'super-simple-crm' ),
				'update_item'       => esc_html__( 'Update Tag', 'super-simple-crm' ),
				'add_new_item'      => esc_html__( 'Add New Tag', 'super-simple-crm' ),
				'new_item_name'     => esc_html__( 'New Tag Name', 'super-simple-crm' ),
				'menu_name'         => esc_html__( 'Tag', 'super-simple-crm' ),
			),
		) );

		// Register Category Taxonomy
		register_taxonomy( 'sscrm_customer_category', 'sscrm_customer', array(
			'public' => false,
			'show_ui' => true,
			'show_tagcloud' => false,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'query_var'         => false,
			'rewrite'           => false,
			'labels'            => array(
				'name'              => esc_html_x( 'Categories', 'taxonomy general name', 'super-simple-crm' ),
				'singular_name'     => esc_html_x( 'Category', 'taxonomy singular name', 'super-simple-crm' ),
				'search_items'      => esc_html__( 'Search Categories', 'super-simple-crm' ),
				'all_items'         => esc_html__( 'All Categories', 'super-simple-crm' ),
				'parent_item'       => esc_html__( 'Parent Category', 'super-simple-crm' ),
				'parent_item_colon' => esc_html__( 'Parent Category:', 'super-simple-crm' ),
				'edit_item'         => esc_html__( 'Edit Category', 'super-simple-crm' ),
				'update_item'       => esc_html__( 'Update Category', 'super-simple-crm' ),
				'add_new_item'      => esc_html__( 'Add New Category', 'super-simple-crm' ),
				'new_item_name'     => esc_html__( 'New Category Name', 'super-simple-crm' ),
				'menu_name'         => esc_html__( 'Category', 'super-simple-crm' ),
			),
		) );
	}

	/**
	 * Add new admin columns to the admin list view for customer data.
	 *
	 * @since 0.0.1
	 *
	 * @param array $columns An array of column data.
	 *
	 * @return array Modified array of column data.
	 */
	public function admin_columns( $columns ) {
		$columns = array(
			'cb'                               => '<input type="checkbox" />',
			'title'                            => esc_html__( 'Customer Name', 'super-simple-crm' ),
			'phone'                            => esc_html__( 'Phone', 'super-simple-crm' ),
			'email'                            => esc_html__( 'Email', 'super-simple-crm' ),
			'budget'                           => esc_html__( 'Budget', 'super-simple-crm' ),
			'taxonomy-sscrm_customer_tag'      => esc_html__( 'Tags', 'super-simple-crm' ),
			'taxonomy-sscrm_customer_category' => esc_html__( 'Categories', 'super-simple-crm' ),
			'date'                             => esc_html__( 'Submitted', 'super-simple-crm' ),
		);

		return $columns;
	}

	/**
	 * Adds the customer data to the columns.
	 *
	 * @since 0.0.1
	 *
	 * @param string $column  The column slug.
	 * @param int    $post_id The ID of the post.
	 *
	 * @return void
	 */
	public function manage_columns( $column, $post_id ) {
		switch( $column ) {
			case 'phone' :
				echo esc_html( get_post_meta( $post_id, 'sscrm_phone', true ) );
				break;
			case 'email' :
				echo esc_html( get_post_meta( $post_id, 'sscrm_email', true ) );
				break;
			case 'budget' :
				echo esc_html( get_post_meta( $post_id, 'sscrm_budget', true ) );
				break;
			default :
				break;
		}
	}

	/**
	 * Declare new columns as sortable
	 *
	 * @since  0.0.1
	 *
	 * @return array A modified array of columns which are sortable.
	 */
	public function sortable_columns() {
		$columns['phone'] = 'phone';
		$columns['email'] = 'email';
		$columns['budget'] = 'budget';

		return $columns;
	}

	/**
	 * Hook in the sorting to the request filter.
	 *
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function handle_sorting() {
		add_filter( 'request', array( $this, 'sort' ) );
	}

	/**
	 * Modify the admin query for the list table to handle sorting by our customer data.
	 *
	 * @param $vars An array of variables used for displaying posts in the list table
	 *
	 * @since 0.0.1
	 *
	 * @return array An array of variables used for displaying posts in the list table
	 */
	public function sort( $vars ) {
		if ( isset( $vars['post_type'] ) && 'sscrm_customer' == $vars['post_type'] ) {
			if ( isset( $vars['orderby'] ) && 'phone' == $vars['orderby'] ) {
				$vars = array_merge(
					$vars,
					array(
						'meta_key' => 'sscrm_phone',
						'orderby' => 'meta_value'
					)
				);
			}
			if ( isset( $vars['orderby'] ) && 'email' == $vars['orderby'] ) {
				$vars = array_merge(
					$vars,
					array(
						'meta_key' => 'sscrm_email',
						'orderby' => 'meta_value'
					)
				);
			}
			if ( isset( $vars['orderby'] ) && 'budget' == $vars['orderby'] ) {
				$vars = array_merge(
					$vars,
					array(
						'meta_key' => 'sscrm_budget',
						'orderby' => 'meta_value'
					)
				);
			}
		}

		return $vars;
	}

	/**
	 * Add a customer data metabox to our sscrm_customer CPT edit screen.
	 *
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box( 'sscrm-customer-data', esc_html__( 'Submitted Customer Information', 'super-simple-crm' ), array( $this, 'render_meta_box' ), 'sscrm_customer' );
	}

	/**
	 * Handles the rendering of the customer data metabox
	 *
	 * @since  0.0.1
	 *
	 * @param object $post The current post object
	 *
	 * @return void
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'sscrm_customer_data_meta', 'sscrm_customer_data_meta' );
		/**
		 * Fires at the top of the customer data metabox.
		 *
		 * @since 0.0.1
		 *
		 * @param object $post The current post object
		 */
		do_action( 'sscrm_customer_data_metabox_top', $post );
		?>
		<p>
			<label for="sscrm_phone"><?php echo esc_html__( 'Phone Number:', 'super-simple-crm' ); ?></label><br />
			<input id="sscrm_phone" name="sscrm_phone" type="tel" value="<?php echo esc_html( get_post_meta( $post->ID, 'sscrm_phone', true ) ); ?>" />
		</p>
		<p>
			<label for="sscrm_email"><?php echo esc_html__( 'Email Address:', 'super-simple-crm' ); ?></label><br />
			<input id="sscrm_email" name="sscrm_email" type="email" value="<?php echo sanitize_email( get_post_meta( $post->ID, 'sscrm_email', true ) ); ?>" /><br />
		</p>
		<p>
			<label for="sscrm_budget"><?php echo esc_html__( 'Desired Budget:', 'super-simple-crm' ); ?></label><br />
			<input id="sscrm_budget" name="sscrm_budget" type="text" value="<?php echo esc_html( get_post_meta( $post->ID, 'sscrm_budget', true ) ); ?>" /><br />
		</p>
		<?php
		/**
		 * Fires at the bottom of the customer data metabox.
		 *
		 * @since 0.0.1
		 *
		 * @param object $post The current post object
		 */
		do_action( 'sscrm_customer_data_metabox_bottom', $post );
	}

	/**
	 * Processes our meta data for customer data upon saving of a post in the admin.
	 *
	 * @since 0.0.1
	 *
	 * @param int $post_id The ID of the current post
	 *
	 * @return void
	 */
	public function save_customer_data( $post_id ) {
		if ( ! is_admin() ) {
			return;
		}
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		check_admin_referer( 'sscrm_customer_data_meta', 'sscrm_customer_data_meta' );
		if( ! current_user_can( 'edit_posts' ) ) {
			return;
		}
		if ( isset( $_POST['sscrm_phone'] ) ) {
			update_post_meta( $post_id, 'sscrm_phone', sanitize_text_field( $_POST['sscrm_phone'] ) );
		}
		if ( isset( $_POST['sscrm_email'] ) ) {
			update_post_meta( $post_id, 'sscrm_email', sanitize_text_field( $_POST['sscrm_email'] ) );
		}
		if ( isset( $_POST['sscrm_budget'] ) ) {
			update_post_meta( $post_id, 'sscrm_budget', sanitize_text_field( $_POST['sscrm_budget'] ) );
		}
		/**
		 * Fires at the top of the customer data metabox.
		 *
		 * @since 0.0.1
		 *
		 * @param int   $post_id    The current post ID
		 * @param array $post_array An array of $_POST data
		 */
		do_action( 'sscrm_admin_save_customer_data', $post_id, $_POST );
	}
}
