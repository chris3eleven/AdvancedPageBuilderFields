( function( $ ) {
	$( document ).on( 'click', '.apbf-tabs__tab', function() {
		if ( !$( this ).hasClass( 'apbf-tabs__tab--active' ) ) {
			$tab_count = $( this ).data( 'tab' );
			$( '.apbf-tabs__tab' ).removeClass( 'apbf-tabs__tab--active' );
			$( '.apbf-tabs__panel' ).removeClass( 'apbf-tabs__panel--active' );
			$( this ).addClass( 'apbf-tabs__tab--active' );
			$( '.apbf-tabs__panel[data-tab=' + $tab_count + ']' ).addClass( 'apbf-tabs__panel--active' );
		}
	} );
} ) ( jQuery );