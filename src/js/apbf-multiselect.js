( function( $ ) {
	var $keyUpTimer;

	// update the listbox value field with an array of the selected post ids
	function updateListBoxValue( $listBox ) {
		// array to hold the selected post ids
		$selectedPosts = [];
		// iterate through the items in the selected posts list, adding their ids to the above-declared array
		$listBox.find( '.apbf-multiselect__posts-selected' ).find( '.apbf-multiselect__item' ).each( function() {
			if ( $( this ).attr( 'data-id' ) != undefined ) $selectedPosts.push( parseInt( $( this ).attr( 'data-id' ) ) );
		} );
		// update the listbox's value field with a stringy-fied version of the $selectedPosts array
		$listBox.find( '.apbf-multiselect__value' ).val( JSON.stringify( $selectedPosts ) );
	}

	// handler for when a list item is clicked
	function listItemClicked( $listItem ) {
		// if the item is not disabled...
		if ( ! $listItem.hasClass( 'apbf-multiselect__item--disabled' ) ) {
			$listBox = $listItem.closest( '.apbf-multiselect' );

			// if the item is in the posts lists
			if ( $listItem.closest( '.apbf-multiselect__posts' ).hasClass( 'apbf-multiselect__posts-posts' ) ) {
				$listItem.clone( true ).addClass( 'ui-sortable-handle' ).appendTo( $listItem.closest( '.apbf-multiselect' ).find( '.apbf-multiselect__posts-selected' ) );
				$listItem.addClass( 'apbf-multiselect__item--disabled' );
			// the item is in the selected posts list
			} else {
				// remove the disabled class from the item in the posts list
				$listItem.closest( '.apbf-multiselect' ).find( '.apbf-multiselect__posts-posts' ).find( '[data-id=' + $listItem.attr( 'data-id' ) + ']' ).removeClass( 'apbf-multiselect__item--disabled' );
				// remove the clicked item from the selected posts list
				$listItem.remove();
			}
			// update the listbox's value
			updateListBoxValue( $listBox );
		}
	}

	// check whether the given listbox has been scrolled to the bottom. if it has, and there is more data, get the data
	function chk_scroll( e ) {
		if ( ( e[ 0 ].scrollHeight - e.scrollTop() <= e.outerHeight() ) && !e.data( 'loadcomplete' ) && !e.data( 'loading' ) ) {
			getListItems( e, null, e.attr( 'data-post_type' ), e.attr( 'data-post_taxonomy' ), e.attr( 'data-post_term' ), e.attr( 'data-posts' ), 10, null, e.data( 'nextPage' ), false );
		}
	}

	function getListItems( $posts_list, $search_title, $post_type, $post_taxonomy, $post_term, $post__in, $posts_per_page, $order, $paged, $clear ) {
		$listBox = $posts_list.closest( '.apbf-multiselect' );
		$posts_list.data( 'loading', true );
		$posts_list.data( 'loadcomplete', false );
		if ( $clear ) $posts_list.empty();
		$posts_list.find( '.apbf-multiselect__item--message' ).remove();
		$posts_list.append( '<li class="apbf-multiselect__item apbf-multiselect__item--message apbf-multiselect__item--disabled apbf-multiselect__item--loading">Loading...</ul>' );
		$.ajax( {
			type: "POST",
			url: ajax.url,
			data: { action: 'apbf_get_posts', 'post_type': $post_type, 'search_title': $search_title, 'post_taxonomy': $post_taxonomy, 'post_term': $post_term, 'post__in': $post__in, 'order': $order, 'paged': $paged, 'posts_per_page': $posts_per_page },
			success: function ( response ) {
				$paged++;
				$posts_list.find( '.apbf-multiselect__item--message' ).remove();
				$posts_list.data( 'nextPage', parseInt( $paged ) );
				$response = JSON.parse( response );
				if ( $response.result == 'success' ) {
					$selectedPosts = $posts_list.closest( '.apbf-multiselect' ).find( '.apbf-multiselect__value' ).val();
					if ( $selectedPosts != '' && $selectedPosts != undefined ) {
						$selectedPosts = JSON.parse( $selectedPosts );
					} else {
						$selectedPosts = [];
					}
					if ( !$.isEmptyObject( $response.results ) ) {
						if ( !$response.pagination.more ) $posts_list.data( 'loadcomplete', true );
						$.each( $response.results, function( index, value ) {
							$listItem = $( '<li></li>' ).addClass( 'apbf-multiselect__item' );
							$listItem.attr( 'data-id', value.id );
							$listItem.text( value.text );
							$listItem.click( function() { listItemClicked( $( this ) ); } );
							
							if ( $posts_list.hasClass( 'apbf-multiselect__posts-posts' ) && $.inArray( value.id.toString(), $selectedPosts ) != -1 ) {
								$listItem.addClass( 'apbf-multiselect__item--disabled' );
							}

							$posts_list.append( $listItem );
						} );
						$posts_list.data( 'loading', false );
					}
				} else if ( $response.result == 'empty' ) {
					$posts_list.append( '<li class="apbf-multiselect__item apbf-multiselect__item--message apbf-multiselect__item--disabled">Nothing found.</ul>' );
				}
			},
			error: function( data ) {
				$posts_list.find( '.apbf-multiselect__item--message' ).remove();
				$posts_list.append( '<li class="apbf-multiselect__item apbf-multiselect__item--message apbf-multiselect__item--disabled apbf-multiselect__item--error">An error occurred, please reload the page and try again.</ul>' );
			}
		} );
	}

	// when a widget edit panel is opened, initialise any listboxes
	$( window ).on( 'panelsopen', function( e ) {
		$( '.so-panels-dialog .apbf-multiselect' ).each( function() {
			// bind chk_scroll function to posts list
			$( this ).find( '.apbf-multiselect__posts-posts').scroll( function() {
				chk_scroll( $( this ) );
			} );

			$posts_list = $( this ).find( '.apbf-multiselect__posts-posts' );

			// populate the posts list
			getListItems( $posts_list, null, $posts_list.attr( 'data-post_type' ), $posts_list.attr( 'data-post_taxonomy' ), $posts_list.attr( 'data-post_term' ), null, 10, null, 1, true );

			// get the field value
			$selectedPosts = $( this ).find( '.apbf-multiselect__value' ).val();
			if ( $selectedPosts != '' && $selectedPosts != undefined ) {
				$selectedPosts = JSON.parse( $selectedPosts ).toString();
				if ( $.isArray( $selectedPosts ) && $selectedPosts.length ) getListItems( $( this ).find( '.apbf-multiselect__posts-selected' ), null, null, null, null, $selectedPosts, -1, 'custom', 1, true );
			}		

			// make the selected posts list sortable
			$( this ).find( '.apbf-multiselect__posts-selected' ).sortable( {
				axis: 'y',
				stop: function() {
					updateListBoxValue( $( this ).closest( '.apbf-multiselect' ) );
				}
			} );
			$( this ).find( '.apbf-multiselect__posts-selected' ).disableSelection();
		} );
	} );

	function searchPosts( $search, $posts_list ) {
		getListItems( $posts_list, $search, $posts_list.attr( 'data-post_type' ), $posts_list.attr( 'data-post_taxonomy' ), $posts_list.attr( 'data-post_term' ), null, 10, null, 1, true );
	}

	$( document ).ready( function() {
		// act on changes to the filter dropdowns
		$( document ).on( 'change', '.apbf-multiselect__filters select', function() {
			var $listBox = $( this ).closest( '.apbf-multiselect' );
			var $search = $listBox.find( '.apbf-multiselect__search' ).val();
			$posts_list = $listBox.find( '.apbf-multiselect__posts-posts' );
			
			var $selectedOption = $( this ).val();
			if ( $selectedOption == '' ) {
				// don't do anything
			} else {
				$selectedOption = $selectedOption.split( ':' );
				
				if ( $selectedOption[ 0 ] == 'post_type' ) {
					$posts_list.attr( 'data-post_type', $selectedOption[ 1 ] );
				}

				if ( $selectedOption[ 0 ] == 'term' ) {
					if ( $selectedOption[ 1 ] == 'all' ) {
						$posts_list.attr( 'data-post_taxonomy', '' );
						$posts_list.attr( 'data-post_term', '' );
					} else {
						$posts_list.attr( 'data-post_taxonomy', $selectedOption[ 1 ] );
						$posts_list.attr( 'data-post_term', $selectedOption[ 2 ] );
					}
				}

				// re-populate the posts list
				getListItems( $posts_list, $search, $posts_list.attr( 'data-post_type' ), $posts_list.attr( 'data-post_taxonomy' ), $posts_list.attr( 'data-post_term' ), null, 10, null, 1, true );
			}
		} );

		// act on changes to the search field
		$( document ).on( 'keyup', '.apbf-multiselect__search', function () {
    		if ( $keyUpTimer != undefined ) clearTimeout( $keyUpTimer );
						
			$search = $( this ).val();
			$posts_list = $( this ).closest( '.apbf-multiselect' ).find( '.apbf-multiselect__posts-posts' );
			
			$keyUpTimer = setTimeout( function() { searchPosts( $search, $posts_list ) }, 250 );
		} );
	} );

} ) ( jQuery );