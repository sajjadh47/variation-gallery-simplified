<?php
/**
 * This file contains the definition of the Variation_Gallery_Simplified_Admin class, which
 * is used to load the plugin's admin-specific functionality.
 *
 * @package       Variation_Gallery_Simplified
 * @subpackage    Variation_Gallery_Simplified/admin
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
 *
 * @since    2.0.0
 */
class Variation_Gallery_Simplified_Admin {
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
	 * @param     string $plugin_name The name of this plugin.
	 * @param     string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_styles() {
		global $pagenow;

		// check if current page is product edit page.
		if ( 'post.php' === $pagenow && 'product' === get_post_type() ) {
			wp_enqueue_style( $this->plugin_name, VARIATION_GALLERY_SIMPLIFIED_PLUGIN_URL . 'admin/css/admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_scripts() {
		global $pagenow;

		// check if current page is product edit page.
		if ( 'post.php' === $pagenow && 'product' === get_post_type() ) {
			wp_enqueue_media();

			wp_enqueue_script( $this->plugin_name, VARIATION_GALLERY_SIMPLIFIED_PLUGIN_URL . 'admin/js/admin.js', array( 'jquery', 'jquery-ui-sortable', 'wp-util' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name,
				'VariationGallerySimplified',
				array(
					'ajaxurl'     => admin_url( 'admin-ajax.php' ),
					'chooseImage' => esc_html__( 'Choose Image', 'variation-gallery-simplified' ),
					'addImage'    => esc_html__( 'Add Images', 'variation-gallery-simplified' ),
				)
			);
		}
	}

	/**
	 * Prints footer scripts for the product edit page in the admin.
	 *
	 * This function outputs JavaScript templates to the page footer on the product
	 * edit screen in the WordPress admin area.  These templates are used to
	 * dynamically render image elements for the simplified variation gallery.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function print_footer_scripts() {
		global $pagenow;

		if ( 'post.php' === $pagenow && 'product' === get_post_type() ) :
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
	}

	/**
	 * Adds a settings link to the plugin's action links on the plugin list table.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     array $links The existing array of plugin action links.
	 * @return    array $links The updated array of plugin action links, including the settings link.
	 */
	public function add_plugin_action_links( $links ) {
		$links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=product' ) ), __( 'Settings', 'variation-gallery-simplified' ) );

		return $links;
	}

	/**
	 * Displays admin notices in the admin area.
	 *
	 * This function checks if the required plugin is active.
	 * If not, it displays a warning notice and deactivates the current plugin.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function admin_notices() {
		// Check if required plugin is active.
		if ( ! class_exists( 'WooCommerce', false ) ) {
			sprintf(
				'<div class="notice notice-warning is-dismissible"><p>%s <a href="%s">%s</a> %s</p></div>',
				__( 'Variation Gallery Simplified requires', 'variation-gallery-simplified' ),
				esc_url( 'https://wordpress.org/plugins/woocommerce/' ),
				__( 'WooCommerce', 'variation-gallery-simplified' ),
				__( 'plugin to be active!', 'variation-gallery-simplified' ),
			);

			// Deactivate the plugin.
			deactivate_plugins( VARIATION_GALLERY_SIMPLIFIED_PLUGIN_BASENAME );
		}
	}

	/**
	 * Declares compatibility with WooCommerce's custom order tables feature.
	 *
	 * This function is hooked into the `before_woocommerce_init` action and checks
	 * if the `FeaturesUtil` class exists in the `Automattic\WooCommerce\Utilities`
	 * namespace. If it does, it declares compatibility with the 'custom_order_tables'
	 * feature. This is important for ensuring the plugin works correctly with
	 * WooCommerce versions that support this feature.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function declare_compatibility_with_wc_custom_order_tables() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}

	/**
	 * Adds an input field for uploading gallery images to a product variation.
	 *
	 * This function generates the HTML markup for a custom input field in the
	 * WooCommerce variation settings. This input field allows users to upload
	 * multiple gallery images specifically for that product variation.  The
	 * generated HTML includes a container for displaying uploaded images, a
	 * button to add more images, and hidden fields to store the image IDs.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     int     $loop           The loop counter for the variation.
	 * @param     array   $variation_data The variation data.
	 * @param     WP_Post $variation      The variation object.
	 */
	public function add_upload_gallery_images_input( $loop, $variation_data, $variation ) {
		$variation_id   = absint( $variation->ID );
		$gallery_images = get_post_meta( $variation_id, 'woo_variation_gallery_simplified_images', true );
		?>
			<div data-product_variation_id="<?php echo esc_attr( $variation_id ); ?>" class="form-row form-row-full variation-gallery-simplified-wrapper">
				<div class="variation-gallery-simplified-postbox">
					<div class="postbox-header">
						<h2><?php esc_html_e( 'Variation Product Gallery', 'variation-gallery-simplified' ); ?></h2>
						<button type="button" class="handle-div" aria-expanded="true">
							<span class="toggle-indicator" aria-hidden="true"></span>
						</button>
					</div>

					<div class="variation-gallery-simplified-inside">
						<div class="variation-gallery-simplified-image-container">
							<ul class="variation-gallery-simplified-images">
							<?php
							if ( is_array( $gallery_images ) && ! empty( $gallery_images ) ) {
								foreach ( $gallery_images as $image_id ) :
									$image = wp_get_attachment_image_src( absint( $image_id ) );
									?>
									<li class="image">
										<input class="wvgs_variation_id_input" type="hidden" name="woo_variation_gallery_simplified[<?php echo esc_attr( $variation_id ); ?>][]" value="<?php echo absint( $image_id ); ?>">
										<img src="<?php echo esc_url( $image[0] ); ?>">
										<a href="#" class="delete remove-variation-gallery-simplified-image"><span class="dashicons dashicons-dismiss"></span></a>
									</li>
									<?php
								endforeach;
							}
							?>
							</ul>
						</div>
						<div class="add-variation-gallery-simplified-image-wrapper hide-if-no-js">
							<a href="#" data-product_variation_loop="<?php echo absint( $loop ); ?>" data-product_variation_id="<?php echo esc_attr( $variation_id ); ?>" class="button-primary add-variation-gallery-simplified-image"><?php esc_html_e( 'Add Variation Gallery Image', 'variation-gallery-simplified' ); ?></a>
						</div>
						<?php wp_nonce_field( 'save_variation_gallery', 'vgs_nonce' ); ?>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * Saves or deletes variation gallery images for a product variation.
	 *
	 * This function handles saving or deleting the gallery images associated with a
	 * specific product variation. If the 'woo_variation_gallery_simplified' data is
	 * present in the $_POST array for the given variation ID, it sanitizes and
	 * saves the image IDs to the 'woo_variation_gallery_simplified_images' meta key.
	 * If the data is not present, it deletes the meta key to remove any previously
	 * saved gallery images.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     int $variation_id The ID of the product variation.
	 */
	public function save_product_variation( $variation_id ) {
		// Verify nonce.
		if ( ! isset( $_POST['vgs_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vgs_nonce'] ) ), 'save_variation_gallery' ) ) {
			return;
		}

		// Check if the variation gallery data is set in the POST request.
		if ( isset( $_POST['woo_variation_gallery_simplified'] ) ) {
			// Check if the data for the specific variation ID is an array.
			if ( isset( $_POST['woo_variation_gallery_simplified'][ $variation_id ] ) && is_array( $_POST['woo_variation_gallery_simplified'][ $variation_id ] ) ) {
				// Initialize an empty array to store the sanitized image IDs.
				$gallery_image_ids = array();

				// Initialize an array to store sanitized image IDs.
				$posted_data = array_map( 'absint', $_POST['woo_variation_gallery_simplified'][ $variation_id ] );

				// Loop through the posted image IDs, sanitize, and add them to the array.
				if ( $posted_data ) {
					foreach ( $posted_data as $key => $value ) {
						$gallery_image_ids[] = absint( $value );
					}
				}

				// Update the post meta with the sanitized gallery image IDs.
				update_post_meta( $variation_id, 'woo_variation_gallery_simplified_images', $gallery_image_ids );

				return; // Exit the function after saving the data.
			}
		}

		// If the variation gallery data is not set or is not an array, delete the post meta.
		delete_post_meta( $variation_id, 'woo_variation_gallery_simplified_images' );
	}
}
