<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           Variation_Gallery_Simplified
 * @author            Sajjad Hossain Sagor <sagorh672@gmail.com>
 *
 * Plugin Name:       Variation Gallery Simplified
 * Plugin URI:        https://wordpress.org/plugins/variation-gallery-simplified/
 * Description:       Add multiple images per product variation on WooCommerce.
 * Version:           2.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Author:            Sajjad Hossain Sagor
 * Author URI:        https://sajjadhsagor.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       variation-gallery-simplified
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'VARIATION_GALLERY_SIMPLIFIED_PLUGIN_VERSION', '2.0.0' );

/**
 * Define Plugin Folders Path
 */
define( 'VARIATION_GALLERY_SIMPLIFIED_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'VARIATION_GALLERY_SIMPLIFIED_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'VARIATION_GALLERY_SIMPLIFIED_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-variation-gallery-simplified-activator.php
 *
 * @since    2.0.0
 */
function on_activate_variation_gallery_simplified() {
	require_once VARIATION_GALLERY_SIMPLIFIED_PLUGIN_PATH . 'includes/class-variation-gallery-simplified-activator.php';

	Variation_Gallery_Simplified_Activator::on_activate();
}

register_activation_hook( __FILE__, 'on_activate_variation_gallery_simplified' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-variation-gallery-simplified-deactivator.php
 *
 * @since    2.0.0
 */
function on_deactivate_variation_gallery_simplified() {
	require_once VARIATION_GALLERY_SIMPLIFIED_PLUGIN_PATH . 'includes/class-variation-gallery-simplified-deactivator.php';

	Variation_Gallery_Simplified_Deactivator::on_deactivate();
}

register_deactivation_hook( __FILE__, 'on_deactivate_variation_gallery_simplified' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 *
 * @since    2.0.0
 */
require VARIATION_GALLERY_SIMPLIFIED_PLUGIN_PATH . 'includes/class-variation-gallery-simplified.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_variation_gallery_simplified() {
	$plugin = new Variation_Gallery_Simplified();

	$plugin->run();
}

run_variation_gallery_simplified();
