<?php
/**
 * This file contains the definition of the Variation_Gallery_Simplified class, which
 * is used to begin the plugin's functionality.
 *
 * @package       Variation_Gallery_Simplified
 * @subpackage    Variation_Gallery_Simplified/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since    2.0.0
 */
class Variation_Gallery_Simplified {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       Variation_Gallery_Simplified_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function __construct() {
		$this->version     = defined( 'VARIATION_GALLERY_SIMPLIFIED_VERSION' ) ? VARIATION_GALLERY_SIMPLIFIED_VERSION : '1.0.0';
		$this->plugin_name = 'variation-gallery-simplified';

		$this->plugin_name = 'variation-gallery-simplified';

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
	 * - Variation_Gallery_Simplified_Loader. Orchestrates the hooks of the plugin.
	 * - Variation_Gallery_Simplified_i18n.   Defines internationalization functionality.
	 * - Variation_Gallery_Simplified_Admin.  Defines all hooks for the admin area.
	 * - Variation_Gallery_Simplified_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once VARIATION_GALLERY_SIMPLIFIED_PLUGIN_PATH . 'includes/class-variation-gallery-simplified-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once VARIATION_GALLERY_SIMPLIFIED_PLUGIN_PATH . 'includes/class-variation-gallery-simplified-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once VARIATION_GALLERY_SIMPLIFIED_PLUGIN_PATH . 'admin/class-variation-gallery-simplified-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once VARIATION_GALLERY_SIMPLIFIED_PLUGIN_PATH . 'public/class-variation-gallery-simplified-public.php';

		$this->loader = new Variation_Gallery_Simplified_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Variation_Gallery_Simplified_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function set_locale() {
		$plugin_i18n = new Variation_Gallery_Simplified_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Variation_Gallery_Simplified_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_print_footer_scripts', $plugin_admin, 'print_footer_scripts' );

		$this->loader->add_action( 'plugin_action_links_' . VARIATION_GALLERY_SIMPLIFIED_PLUGIN_BASENAME, $plugin_admin, 'add_plugin_action_links' );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices' );

		$this->loader->add_action( 'before_woocommerce_init', $plugin_admin, 'declare_compatibility_with_wc_custom_order_tables' );

		$this->loader->add_action( 'woocommerce_product_after_variable_attributes', $plugin_admin, 'add_upload_gallery_images_input', 10, 3 );
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'save_product_variation', 10, 1 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function define_public_hooks() {
		$plugin_public = new Variation_Gallery_Simplified_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'woocommerce_before_single_product_summary', $plugin_public, 'show_gallery_images', 20 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    Variation_Gallery_Simplified_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
