jQuery( document ).ready( function( $ )
{
	function listen_for_variation_changes()
	{
		const variation_id = $( 'input[name="variation_id"]' ).val();

		if ( variation_id != '' )
		{
			if ( $( '.variation_id_' + variation_id ).length )
			{
				$( '.woocommerce-product-gallery' ).hide();

				$( '.variation_gallery_simplified_images.variation_id_' + variation_id ).show();
			}
		}
		else
		{
			$( '.variation_gallery_simplified_images' ).hide();
			
			$( '.woocommerce-product-gallery' ).not( $( '.variation_gallery_simplified_images' ) ).show();
		}
	}
	
	$( document ).on( 'change', '.variations select', function( event )
	{
		listen_for_variation_changes();
	} );

	$( document ).on( 'click', '.reset_variations', function( event )
	{
		listen_for_variation_changes();
	} );
} );