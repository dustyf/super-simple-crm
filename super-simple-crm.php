<?php
/**
 * Plugin Name: Super Simple CRM
 * Plugin URI:  http://dustyf.com
 * Description: A Super Simple CRM Solution to collect leads
 * Version:     0.0.1
 * Author:      Dustin Filippini
 * Author URI:  http://dustyf.com
 * Donate link: http://dustyf.com
 * License:     GPLv2
 * Text Domain: super-simple-crm
 * Domain Path: /languages
 *
 * @link http://dustyf.com
 *
 * @package Super Simple CRM
 * @version 0.0.1
 */

/**
 * Copyright (c) 2016 Dustin Filippini (email : dusty@dustyf.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 * Autoloads files with classes when needed
 *
 * @since 0.0.1
 *
 * @param  string $class_name Name of the class being requested.
 *
 * @return void
 */
function super_simple_crm_autoload_classes( $class_name ) {
	if ( 0 !== strpos( $class_name, 'SSCRM_' ) ) {
		return;
	}

	$filename = strtolower( str_replace(
		'_', '-',
		substr( $class_name, strlen( 'SSCRM_' ) )
	) );

	Super_Simple_CRM::include_file( 'includes/class-' . $filename );
}
spl_autoload_register( 'super_simple_crm_autoload_classes' );

/**
 * Main initiation class
 *
 * @since 0.0.1
 */
final class Super_Simple_CRM {

	/**
	 * Current version
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	const VERSION = '0.0.1';

	/**
	 * URL of plugin directory
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $url = '';

	/**
	 * Path of plugin directory
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $path = '';

	/**
	 * Plugin basename
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $basename = '';
	/**
	 * Instance of SSCRM_Customer Data
	 *
	 * @since 0.0.1
	 *
	 * @var SSCRM_Customer_Data
	 */
	protected $customer_data;

	/**
	 * Instance of SSCRM_Form
	 *
	 * @since 0.0.1
	 *
	 * @var SSCRM_Form
	 */
	protected $form;

	/**
	 * Singleton instance of plugin
	 *
	 * @since 0.0.1
	 *
	 * @var Super_Simple_CRM
	 */
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since 0.0.1
	 *
	 * @return Super_Simple_CRM A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function plugin_classes() {
		$this->customer_data = new SSCRM_Customer_Data( $this );
		$this->form = new SSCRM_Form( $this );
	}

	/**
	 * Add hooks and filters
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Activate the plugin
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function _deactivate() {}

	/**
	 * Init hooks
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function init() {
		load_plugin_textdomain( 'super-simple-crm', false, dirname( $this->basename ) . '/languages/' );
		$this->plugin_classes();
		$this->customer_data->register_customer_post_type();
		$this->customer_data->register_customer_taxonomies();
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since 0.0.1
	 *
	 * @param string $field Field to get.
	 * @throws Exception Throws an exception if the field is invalid.
	 *
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory
	 *
	 * @since 0.0.1
	 *
	 * @param  string $filename Name of the file to be included.
	 *
	 * @return bool   Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( $filename . '.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory
	 *
	 * @since 0.0.1
	 *
	 * @param  string $path (optional) appended path.
	 *
	 * @return string       Directory and path
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url
	 *
	 * @since 0.0.1
	 *
	 * @param  string $path (optional) appended path.
	 *
	 * @return string       URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the Super_Simple_CRM object and return it.
 * Wrapper for Super_Simple_CRM::get_instance()
 *
 * @since 0.0.1
 *        
 * @return Super_Simple_CRM  Singleton instance of plugin class.
 */
function super_simple_crm() {
	return Super_Simple_CRM::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( super_simple_crm(), 'hooks' ) );

register_activation_hook( __FILE__, array( super_simple_crm(), '_activate' ) );
register_deactivation_hook( __FILE__, array( super_simple_crm(), '_deactivate' ) );
