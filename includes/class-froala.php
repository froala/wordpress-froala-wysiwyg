<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       www.froala.com
 * @since      1.0.0
 *
 * @package    Froala
 * @subpackage Froala/includes
 */

/**
 * Core plugin class.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Froala
 * @subpackage Froala/includes
 * @author     Radu <Radu@froala.com>
 */
class Froala {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Froala_Loader    $loader    Maintains and registers all hooks for the plugin.
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

		$this->plugin_name = 'froala';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Froala_Loader. Orchestrates the hooks of the plugin.
	 * - Froala_i18n. Defines internationalization functionality.
	 * - Froala_Admin. Defines all hooks for the admin area.
	 * - Froala_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-froala-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-froala-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-froala-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-froala-public.php';

		$this->loader = new Froala_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Froala_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Froala_i18n();

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

		$plugin_admin = new Froala_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles', 5 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts', 5 );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page', 6 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_setting', 7);
		$this->loader->add_filter( 'wp_editor_settings', $plugin_admin, 'froala_editor', 10);

		// Added custom hooks, helps adding new plugins or any other custom functionality
		$this->loader->add_action( 'froala_before_init', $plugin_admin, 'froala_editor_before', 7, 4);
		$this->loader->add_action( 'froala_new_plugin', $plugin_admin, 'froala_new_plugin', 9, 2);
		$this->loader->add_action( 'froala_after_init', $plugin_admin, 'froala_editor_after' , 10, 4);

		// Added hooks for image upload and image mananger
		$this->loader->add_action('wp_ajax_froala_upload_files', $plugin_admin, 'froala_upload_files');
		$this->loader->add_action('wp_ajax_froala_image_manager', $plugin_admin,'froala_image_manager');


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Froala_Editor( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 10);
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 10);

		// Added custom hooks, helps adding new plugins or any other custom functionality
		$this->loader->add_action( 'froala_before_public_init', $plugin_public, 'froala_editor_before', 10, 4);
		$this->loader->add_action( 'froala_after_public_init', $plugin_public, 'froala_editor_after' , 10, 4);

		// Added hooks for image upload and image mananger on the front-end part of the website.
		$this->loader->add_action('wp_ajax_froala_upload_files', $plugin_public, 'froala_upload_files');
		$this->loader->add_action('wp_ajax_nopriv_froala_upload_files', $plugin_public, 'froala_upload_files'); // Allow front-end submission
		$this->loader->add_action('wp_ajax_froala_image_manager', $plugin_public,'froala_image_manager');
		$this->loader->add_action('wp_ajax_nopriv_froala_image_manager', $plugin_public,'froala_image_manager'); // Allow front-end image browsing


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
	 * @return    Froala_Loader    Orchestrates the hooks of the plugin.
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
