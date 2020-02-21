( function( $ ) {
	// when a widget edit panel is opened...
	$( window ).on( 'panelsopen', function( e ) {
		// if dataselect controls were found...
		if ( $( '.so-panels-dialog .apbf-dataselect' ).length ) {
			// iterate through found dataselect controls and convert them to select2 instances
			$( '.so-panels-dialog .apbf-dataselect' ).each( function() {
				$select = $( this ).select2( {
					dropdownCssClass: "-apbf",
					ajax: {
    					url: ajax.url,
						type: 'POST',
						dataType: 'json',
						delay: 250,
						data: function ( params ) {
							var query = {
								action: 'apbf_get_posts',
								post_type: $( this ).data( 'post_type' ),
								status: $( this ).data( 'status' ),
								orderby: $( this ).data( 'orderby' ),
								order: $( this ).data( 'order' ),
								paged: params.page || 1,
								search_title: params.term
							}

							return query;
						},
						cache: true
					},
					minimumInputLength: 0,
				} );
				var $container = $select.next( '.select2-container' );
				$container.addClass( '-apbf' );
			} );
		}
	} );
} ) ( jQuery );