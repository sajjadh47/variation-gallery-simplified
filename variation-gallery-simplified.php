<?php
/*
Plugin Name: Variation Gallery Simplified
Plugin URI : https://wordpress.org/plugins/variation-gallery-simplified
Description: Add multiple images per product variation on WooCommerce.
Version: 1.0.2
Author: Sajjad Hossain Sagor
Author URI: https://sajjadhsagor.com
Text Domain: variation-gallery-simplified
Domain Path: /languages
Requires PHP: 7.4
Requires at least: 5.7
Tested up to: 6.6
WC requires at least: 5.8
WC tested up to: 9.3

License: GPL2
This WordPress Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

This free software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this software. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! defined( 'WOOVGIS_ROOT_DIR' ) )
{
	define( 'WOOVGIS_ROOT_DIR', dirname( __FILE__ ) ); // Plugin root dir
}

if( ! defined( 'WOOVGIS_ROOT_URL' ) )
{
	define( 'WOOVGIS_ROOT_URL', plugin_dir_url( __FILE__ ) ); // Plugin root url
}

/**
 * Plugin Activation Hook
 */
register_activation_hook( __FILE__, 'woovgis_plugin_activated' );

if ( ! function_exists( 'woovgis_plugin_activated' ) )
{
	function woovgis_plugin_activated()
	{
		woovgis_check_dependency();
	}
}

/**
 * Plugin Deactivation Hook
 */
register_deactivation_hook( __FILE__, 'woovgis_plugin_deactivated' );

if ( ! function_exists( 'woovgis_plugin_deactivated' ) )
{
	function woovgis_plugin_deactivated() {}
}

/**
 * Plugin Uninstalled / Deleted Hook
 */
register_uninstall_hook( __FILE__, 'woovgis_plugin_uninstalled' );

if ( ! function_exists( 'woovgis_plugin_uninstalled' ) )
{
	function woovgis_plugin_uninstalled() {}
}

if ( ! function_exists( 'woovgis_check_dependency' ) )
{
	function woovgis_check_dependency()
	{
		if ( ! class_exists( 'WooCommerce' ) )
		{
			// is this plugin active?
			if ( is_plugin_active( plugin_basename( __FILE__ ) ) )
			{
				// deactivate the plugin
				deactivate_plugins( plugin_basename( __FILE__ ) );
				
				// unset activation notice
				unset( $_GET[ 'activate' ] );
				
				// display notice
				add_action( 'admin_notices', function()
				{
					if ( ! class_exists( 'WooCommerce' ) )
					{
						echo '<div class="error notice is-dismissible">';
						
							echo __('<p><strong>Variation Gallery Simplified</strong> plugin needs <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> to be installed & activated.</p>', 'variation-gallery-simplified' );
						
						echo '</div>';
					}
				} );
			}
		}
	}
}

/**
 * Check if WooCommerce plugin is active
 */
add_action( 'admin_init', function()
{
	woovgis_check_dependency();
} );

/**
 * Load the plugin after the main plugin is loaded.
 */
add_action( 'plugins_loaded', function()
{
	/**
	 * Load Text Domain
	 * This gets the plugin ready for translation
	 */
	load_plugin_textdomain( 'variation-gallery-simplified', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
} );

add_action( 'admin_enqueue_scripts', function()
{
	global $pagenow;

	if ( $pagenow == 'post.php' && get_post_type() == 'product' ) :
		
		wp_enqueue_media();

		wp_enqueue_style( 'variation-gallery-simplified-admin', WOOVGIS_ROOT_URL . 'assets/css/admin.css' );

		wp_enqueue_script( 'variation-gallery-simplified-admin', WOOVGIS_ROOT_URL . 'assets/js/admin.js', array(
			'jquery',
			'jquery-ui-sortable',
			'wp-util'
		), time(), true );

		wp_localize_script( 'variation-gallery-simplified-admin', 'WOO_VARIATION_GALLERY_SIMPLIFIED', array(
			'choose_image' => esc_html__( 'Choose Image', 'variation-gallery-simplified' ),
			'add_image'    => esc_html__( 'Add Images', 'variation-gallery-simplified' )
		) );
	
	endif;
} );

add_action( 'wp_enqueue_scripts', function()
{
	wp_enqueue_style( 'variation-gallery-simplified-public', WOOVGIS_ROOT_URL . 'assets/css/public.css' );
	
	wp_enqueue_script( 'variation-gallery-simplified-public', WOOVGIS_ROOT_URL . 'assets/js/public.js', array( 'jquery' ), time(), true );
} );

add_action( 'admin_print_footer_scripts', function()
{
	global $pagenow;

	if ( $pagenow == 'post.php' && get_post_type() == 'product' ) :
		?>
			<script type="text/html" id="tmpl-variation-gallery-simplified-image">
				<li class="image">
					<input class="wvgss_variation_id_input" type="hidden" name="woo_variation_gallery_simplified[{{data.product_variation_id}}][]" value="{{data.id}}">
					<img src="{{data.url}}">
					<a href="#" class="delete remove-variation-gallery-simplified-image"><span class="dashicons dashicons-dismiss"></span></a>
				</li>
			</script>
		<?php
	endif;
} );

add_action( 'woocommerce_before_single_product_summary', function()
{
	include 'templates/product-image.php';

}, 20 );

add_action( 'woocommerce_save_product_variation', function( $variation_id, $loop )
{
	if ( isset( $_POST['woo_variation_gallery_simplified'] ) )
	{
		if ( isset( $_POST['woo_variation_gallery_simplified'][ $variation_id ] ) && is_array( $_POST['woo_variation_gallery_simplified'][ $variation_id ] ) )
		{
			$gallery_image_ids = [];
			
			foreach ( $_POST['woo_variation_gallery_simplified'][ $variation_id ] as $key => $value )
			{
				$gallery_image_ids[] = absint( sanitize_text_field( $value ) );
			}
			
			update_post_meta( $variation_id, 'woo_variation_gallery_simplified_images', $gallery_image_ids ); return;
		}
	}

	delete_post_meta( $variation_id, 'woo_variation_gallery_simplified_images' );

}, 10, 2 );

add_action( 'woocommerce_product_after_variable_attributes', function( $loop, $variation_data, $variation )
{
	$variation_id   = absint( $variation->ID );
	
	$gallery_images = get_post_meta( $variation_id, 'woo_variation_gallery_simplified_images', true );
	
	?>
		<div data-product_variation_id="<?php echo esc_attr( $variation_id ) ?>" class="form-row form-row-full variation-gallery-simplified-wrapper">
			<div class="variation-gallery-simplified-postbox">
				<div class="postbox-header">
					<h2><?php esc_html_e( 'Variation Product Gallery', 'variation-gallery-simplified' ) ?></h2>
					<button type="button" class="handle-div" aria-expanded="true">
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				</div>

				<div class="variation-gallery-simplified-inside">
					<div class="variation-gallery-simplified-image-container">
						<ul class="variation-gallery-simplified-images">
							<?php
								if ( is_array( $gallery_images ) && ! empty( $gallery_images ) )
								{
									foreach ( $gallery_images as $image_id ):
										$image = wp_get_attachment_image_src( absint( $image_id ) );
										?>
										<li class="image">
											<input class="wvgs_variation_id_input" type="hidden" name="woo_variation_gallery_simplified[<?php echo esc_attr( $variation_id ) ?>][]" value="<?php echo absint( $image_id ) ?>">
											<img src="<?php echo esc_url( $image[ 0 ] ) ?>">
											<a href="#" class="delete remove-variation-gallery-simplified-image"><span class="dashicons dashicons-dismiss"></span></a>
										</li>
									<?php endforeach;
								}
							?>
						</ul>
					</div>
					<div class="add-variation-gallery-simplified-image-wrapper hide-if-no-js">
						<a href="#" data-product_variation_loop="<?php echo absint( $loop ) ?>" data-product_variation_id="<?php echo esc_attr( $variation_id ) ?>" class="button-primary add-variation-gallery-simplified-image"><?php esc_html_e( 'Add Variation Gallery Image', 'variation-gallery-simplified' ) ?></a>
					</div>
				</div>
			</div>
		</div>
	<?php

}, 10, 3 );

if ( ! function_exists( 'woovgis_get_available_variations' ) )
{
	function woovgis_get_available_variations( $product )
	{
		if ( is_numeric( $product ) )
		{
			$product = wc_get_product( absint( $product ) );
		}

		return $product->get_available_variations();
	}
}
