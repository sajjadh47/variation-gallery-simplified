jQuery( document ).ready( function( $ )
{
	function listen_for_variation_changes()
	{
		var variation_id = $( 'input[name="variation_id"]' ).val();

		if ( variation_id != '' )
		{
			if ( $( '.variation-id-' + variation_id ).length )
			{
				$( '.woocommerce-product-gallery' ).hide();

				$( '.variation-gallery-simplified-images.variation-id-' + variation_id ).show();
			}
		}
		else
		{
			$( '.variation-gallery-simplified-images' ).hide();
			
			$( '.woocommerce-product-gallery' ).not( $( '.variation-gallery-simplified-images' ) ).show();
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