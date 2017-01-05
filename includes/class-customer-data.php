<?php
/**
 * Super Simple CRM Customer Data
 *
 * @since NEXT
 * @package Super Simple CRM
 */

/**
 * Super Simple CRM Customer Data.
 *
 * @since NEXT
 */
class SSCRM_Customer_Data {
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
		$this->register_customer_post_type();
		$this->register_customer_taxonomies();
	}

	/**
	 * Register the Customer custom post type for storing customer data.
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
}
