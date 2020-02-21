<?php
/*
Plugin Name: Advanced Page Builder Fields
Description: Form Fields for SiteOrigin Page Builder Widgets.
Version: 1.0.0
Author: 3ELEVEN DIGITAL
Author URI: https://3eleven.net
*/

// Tell SiteOrigin Widgets Bundle what the class prefix is so the fields can be autoloaded and instantiated
function register_custom_fields_class_prefixes( $class_prefixes ) {
	$class_prefixes[] = 'APBF_Widget_Field_';
	return $class_prefixes;
}

add_filter( 'siteorigin_widgets_field_class_prefixes', 'register_custom_fields_class_prefixes' );


// Tell SiteOrigin Widgets Bundle where the custom fields are
function apbf_custom_fields_class_paths( $class_paths ) {
	$class_paths[] = plugin_dir_path( __FILE__ ) . 'custom-fields/';
	return $class_paths;
}

add_filter( 'siteorigin_widgets_field_class_paths', 'apbf_custom_fields_class_paths' );


// Search filter for WP_Query
function title_filter( $where, &$wp_query ) {
    global $wpdb;
    
	if ( $search_term = $wp_query->get( 'search_title' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\'';
    }

    return $where;
}

// Return posts filtered by options specified in $_POST
function apbf_get_posts() {
	$return = array();

	$post_status = isset( $_POST[ 'post_status' ] ) ? $_POST[ 'post_status' ] : 'publish';
	$orderby = isset( $_POST[ 'orderby' ] ) ? $_POST[ 'orderby' ] : 'publish_date';
	$order = isset( $_POST[ 'order' ] ) ? $_POST[ 'order' ] : 'DESC';
	$paged = isset( $_POST[ 'paged' ] ) ? $_POST[ 'paged' ] : 1;
	$search_title = isset( $_POST[ 'search_title' ] ) ? $_POST[ 'search_title' ] : '';

	if ( $order == 'custom' ) {
		$orderby = 'post__in';
		$order = '';
	}

    $args = array(
		'post_status' => $post_status,
		'posts_per_page' => 20,
		'orderby' => $orderby,
		'order' => $order,
		'paged' => $paged,
	);

	if ( isset( $_POST[ 'post_type' ] ) ) {
		if ( $_POST[ 'post_type' ] != '' ) $args[ 'post_type' ] = $_POST[ 'post_type' ];
	}

	if ( !isset( $args[ 'post_type' ] ) ) $args[ 'post_type' ] = 'any';

	if ( isset( $_POST[ 'search_title' ] ) ) {
		if ( $_POST[ 'search_title' ] != '' ) $args[ 'search_title' ] = $_POST[ 'search_title' ];
	}

	if ( isset( $_POST[ 'post_taxonomy' ] ) && isset( $_POST[ 'post_term' ] ) ) {
		if ( $_POST[ 'post_taxonomy' ] != '' && $_POST[ 'post_term' ] != '' ) {
			$args[ 'tax_query' ] = array(
				array(
					'taxonomy' => $_POST[ 'post_taxonomy' ],
					'field' => 'slug',
					'terms' => $_POST[ 'post_term' ],
				),
    		);
		}
	}

	if ( isset( $_POST[ 'post__in' ] ) ) {
		if ( $_POST[ 'post__in' ] != '' ) $args[ 'post__in' ] = explode( ',', $_POST[ 'post__in' ] );
	}

	add_filter( 'posts_where', 'title_filter', 10, 2 );
	$query = new WP_Query( $args );
	remove_filter( 'posts_where', 'title_filter', 10, 2 );

	if ( $query->have_posts() ) {
		$return[ 'result' ] = 'success';
		$return[ 'args' ] = $args;
		
		$results = array();
		
		foreach ( $query->posts as $post ) {
			$results[] = array(
				'id' => $post->ID,
				'text' => $post->post_title
			);
		}

		$return[ 'results' ] = $results;
		$return[ 'pagination' ] = array(
			'more' => $paged < $query->max_num_pages
		);
	} else {
		$return[ 'result' ] = 'empty';
	}

	die( json_encode( $return ) );
}

add_action( 'wp_ajax_apbf_get_posts', 'apbf_get_posts' );
add_action( 'wp_ajax_nopriv_apbf_get_posts', 'apbf_get_posts' );