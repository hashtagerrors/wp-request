<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://hashtagerrors.com
 * @since      1.0.0
 *
 * @package    WPH_Request
 * @subpackage WPH_Request/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WPH_Request
 * @subpackage WPH_Request/includes
 * @author     Hashtag Errors <hashtagerrors@gmail.com>
 */
class WPH_Request {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WPH_Request_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WPH_REQUEST_VERSION' ) ) {
			$this->version = WPH_REQUEST_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wph-request';

		$this->define_constants();
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 */

	public function define_constants() {

		// Set constant path to the plugin directory.
		define( 'WPHR_DIR', trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) ) );

		// Set the constant path to the plugin directory URI.
		define( 'WPHR_URI', trailingslashit( plugin_dir_url( dirname( __FILE__ ) ) ) );

		// Set the constant path to the includes directory.
		define( 'WPHR_INC', WPHR_DIR . trailingslashit( 'includes' ) );

		// Set the constant path to the admin directory.
		define( 'WPHR_ADMIN', WPHR_DIR . trailingslashit( 'admin' ) );

		// Set the constant path to the public directory.
		define( 'WPHR_PUBLIC', WPHR_DIR . trailingslashit( 'public' ) );

		// Set the constant path to the shortcodes directory.
		define( 'WPHR_SH', WPHR_DIR . trailingslashit( 'shortcodes' ) );

	}
	
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WPH_Request_Loader. Orchestrates the hooks of the plugin.
	 * - WPH_Request_i18n. Defines internationalization functionality.
	 * - WPH_Request_Admin. Defines all hooks for the admin area.
	 * - WPH_Request_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * for orchestrating the actions and filters of the core plugin.
		 */
		require_once WPHR_INC . 'class-wph-request-loader.php';

		/**
		 * for defining internationalization functionality of the plugin.
		 */
		require_once WPHR_INC . 'class-wph-request-i18n.php';

		/**
		 * for defining all actions that occur in the admin area.
		 */
		require_once WPHR_ADMIN . 'class-wph-request-admin.php';

		/**
		 * for defining all actions that occur in the public-facing side of the site.
		 */
		require_once WPHR_PUBLIC . 'class-wph-request-public.php';

		/**
		 * for defining all service for shortcodes.
		 */
		require_once WPHR_INC . 'class-wph-request-service.php';

		/**
		 * for defining all shortcodes.
		 */
		require_once WPHR_SH . 'wph-request-variables.php';

		$this->loader = new WPH_Request_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WPH_Request_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WPH_Request_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WPH_Request_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WPH_Request_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WPH_Request_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
