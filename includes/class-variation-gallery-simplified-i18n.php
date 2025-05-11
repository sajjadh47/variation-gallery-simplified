<?php
/**
 * This file contains the definition of the Variation_Gallery_Simplified_I18n class, which
 * is used to load the plugin's internationalization.
 *
 * @package       Variation_Gallery_Simplified
 * @subpackage    Variation_Gallery_Simplified/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since    2.0.0
 */
class Variation_Gallery_Simplified_I18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'variation-gallery-simplified',
			false,
			dirname( VARIATION_GALLERY_SIMPLIFIED_PLUGIN_BASENAME ) . '/languages/'
		);
	}
}
