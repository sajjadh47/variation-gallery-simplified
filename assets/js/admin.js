jQuery( document ).ready( function( $ )
{
	var WooVariationGallerySimplified =
	{
		'HandleDiv': function()
		{
			// Meta-Boxes - Open/close
			$( document ).on( 'click', '.variation-gallery-simplified-wrapper .handle-div', function()
			{
				$( this ).closest( '.variation-gallery-simplified-postbox' ).toggleClass( 'closed' );

				var ariaExpandedValue = ! $( this ).closest( '.variation-gallery-simplified-postbox' ).hasClass( 'closed' );

				$( this ).attr( 'aria-expanded', ariaExpandedValue );
			} );
		},
		'ImageUploader': function()
		{
			$( document ).off( 'click', '.add-variation-gallery-simplified-image' );
		   
			$( document ).off( 'click', '.remove-variation-gallery-simplified-image' );
			
			$( document ).on( 'click', '.add-variation-gallery-simplified-image', this.AddImage );
			
			$( document ).on( 'click', '.remove-variation-gallery-simplified-image', this.RemoveImage );
			
			$( '.woocommerce_variation ' ).each( function ()
			{
				var optionsWrapper = $( this ).find( '.options:first' );
				
				var galleryWrapper = $( this ).find( '.variation-gallery-simplified-wrapper' );
				
				galleryWrapper.insertBefore( optionsWrapper );
			} );			
		},
		'AddImage': function( event )
		{
			var _this = this;

			event.preventDefault();
			
			event.stopPropagation();
			
			var frame;
			
			var product_variation_id = $( this ).data( 'product_variation_id' );
			
			var loop = $( this ).data( 'product_variation_loop' );

			if ( typeof wp !== 'undefined' && wp.media && wp.media.editor )
			{
				// If the media frame already exists, reopen it.
				if ( frame )
				{
					frame.open(); return;
				}

				// Create the media frame.
				frame = wp.media(
				{
					title: WOO_VARIATION_GALLERY_SIMPLIFIED.choose_image,
					button:
					{
					  text: WOO_VARIATION_GALLERY_SIMPLIFIED.add_image
					},
					library:
					{
					  type: ['image']
					}
				} );
				
				frame.on( 'select', function ()
				{
					var images = frame.state().get( 'selection' ).toJSON();
					
					var html = images.map( function ( image )
					{
						if ( image.type === 'image' )
						{
							var id = image.id,
								
								_image$sizes = image.sizes;
								
								_image$sizes = _image$sizes === void 0 ? {}: _image$sizes;
							
							var thumbnail = _image$sizes.thumbnail,
								
								full = _image$sizes.full;
							
							var url = thumbnail ? thumbnail.url: full.url;
							
							var template = wp.template( 'variation-gallery-simplified-image' );
							
							return template(
							{
								id: id,
								url: url,
								product_variation_id: product_variation_id,
								loop: loop
							} );
						}
					} ).join( '' );
					
					$( _this ).parent().prev().find( '.variation-gallery-simplified-images' ).append( html );

					WooVariationGallerySimplified.Sortable();
					
					WooVariationGallerySimplified.VariationChanged( _this );
				} );

				frame.open();
			}
		},
		'VariationChanged': function( $el )
		{			
			$( $el ).closest( '.woocommerce_variation' ).addClass( 'variation-needs-update' );
			
			$( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );
			
			$( '#variable_product_options' ).trigger( 'woocommerce_variations_input_changed' );
		},
		'RemoveImage': function( event )
		{
			var _this2 = this;

			event.preventDefault();
			
			event.stopPropagation();

			WooVariationGallerySimplified.VariationChanged( this );

			_.delay( function ()
			{
				$( _this2 ).parent().remove();
			
			}, 1 );
		},
		'Sortable': function()
		{
			$( '.variation-gallery-simplified-images' ).sortable(
			{
				items: 'li.image',
				cursor: 'move',
				scrollSensitivity: 40,
				forcePlaceholderSize: true,
				forceHelperSize: false,
				helper: 'clone',
				opacity: 0.65,
				placeholder: 'variation-gallery-simplified-sortable-placeholder',
				start: function( event, ui )
				{
					ui.item.css( 'background-color', '#F6F6F6' );
				},
				stop: function( event, ui )
				{
					ui.item.removeAttr( 'style' );
				},
				update: function()
				{
					WooVariationGallerySimplified.VariationChanged( this );
				}
			} );
		}
	};

	$( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', function()
	{
		WooVariationGallerySimplified.ImageUploader();
		
		WooVariationGallerySimplified.HandleDiv();

		WooVariationGallerySimplified.Sortable();
	} );
} );