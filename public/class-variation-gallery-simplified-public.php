<?php
/**
 * This file contains the definition of the Variation_Gallery_Simplified_Public class, which
 * is used to load the plugin's public-facing functionality.
 *
 * @package       Variation_Gallery_Simplified
 * @subpackage    Variation_Gallery_Simplified/public
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
 *
 * @since    2.0.0
 */
class Variation_Gallery_Simplified_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     string $plugin_name The name of the plugin.
	 * @param     string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, VARIATION_GALLERY_SIMPLIFIED_PLUGIN_URL . 'public/css/public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, VARIATION_GALLERY_SIMPLIFIED_PLUGIN_URL . 'public/js/public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name,
			'VariationGallerySimplified',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Retrieves available variations for a product.
	 *
	 * This function retrieves the available variations for a given product.  It
	 * accepts either a product ID or a product object. If a product ID is
	 * provided, it retrieves the product object using `wc_get_product()`.  It
	 * then calls the `$product->get_available_variations()` method to get the
	 * variations.
	 *
	 * @since     1.0.0
	 * @static
	 * @access    public
	 * @param     int|WC_Product $product The product ID or product object.
	 * @return    array|false             An array of available variations, or false on error.
	 */
	public static function get_available_variations( $product ) {
		// Check if the provided $product is a numeric ID.
		if ( is_numeric( $product ) ) {
			// If it's a numeric ID, get the product object using wc_get_product().
			$product = wc_get_product( absint( $product ) );
		}

		// Return the result of calling the get_available_variations() method on the product object.
		return $product->get_available_variations();
	}

	/**
	 * Displays gallery images for a product variation.
	 *
	 * This function includes the template file responsible for rendering the gallery
	 * images display in the product edit page. The template file is located in the
	 * plugin's admin views directory.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function show_gallery_images() {
		require VARIATION_GALLERY_SIMPLIFIED_PLUGIN_PATH . 'public/views/product-image.php';
	}

	/**
	 * Renders the HTML for the product gallery.
	 *
	 * This function generates the HTML markup for displaying the product gallery,
	 * including the main image and thumbnails. It handles both simple and variable
	 * products, and allows for customization of the gallery classes and image HTML.
	 *
	 * @since     1.0.0
	 * @static
	 * @access    public
	 * @param     int $product_id   The ID of the product.
	 * @param     int $variation_id The ID of the variation (optional, defaults to 0).
	 */
	public static function render_gallery_html( $product_id, $variation_id = 0 ) {
		$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
		$product           = wc_get_product( $product_id );
		$post_thumbnail_id = $variation_id ? wc_get_product( $variation_id )->get_image_id() : $product->get_image_id();
		$wrapper_classes   = apply_filters(
			'woocommerce_single_product_image_gallery_classes',
			array(
				'woocommerce-product-gallery',
				'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
				'woocommerce-product-gallery--columns-' . absint( $columns ),
				'images',
				$variation_id ? 'variation-gallery-simplified-images' : '',
				$variation_id ? 'variation-id-' . $variation_id : '',
			)
		);

		?>
		<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
			<div class="woocommerce-product-gallery__wrapper">
				<?php
				if ( $post_thumbnail_id ) {
					$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
				} else {
					$wrapper_classname = $product->is_type( ProductType::VARIABLE ) && ! empty( $product->get_available_variations( 'image' ) ) ?
						'woocommerce-product-gallery__image woocommerce-product-gallery__image--placeholder' :
						'woocommerce-product-gallery__image--placeholder';
					$html              = sprintf( '<div class="%s">', esc_attr( $wrapper_classname ) );
					$html             .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'variation-gallery-simplified' ) );
					$html             .= '</div>';
				}

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id );

				if ( $variation_id ) {
					require VARIATION_GALLERY_SIMPLIFIED_PLUGIN_PATH . 'public/views/product-thumbnails.php';
				}

				do_action( 'woocommerce_product_thumbnails' );
				?>
			</div>
		</div>
		<?php
	}
}
