<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

use Automattic\WooCommerce\Enums\ProductType;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

/**
 * Display Product Gallery for Simple and Variable Products.
 */
global $product;

// Display galleries for variable products.
if ( $product->is_type( ProductType::VARIABLE ) ) {
	$variations = $product->get_available_variations();

	if ( $variations ) {
		foreach ( $variations as $variation ) {
			$variation_id = $variation['variation_id'];
			Variation_Gallery_Simplified_Public::render_gallery_html( $product->get_id(), $variation_id );
		}
	}
} else {
	// Display gallery for simple products.
	Variation_Gallery_Simplified_Public::render_gallery_html( $product->get_id() );
}
