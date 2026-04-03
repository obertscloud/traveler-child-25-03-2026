<?php
/**
 * Global WP Job Manager functions.
 *
 * New global functions are discouraged whenever possible.
 *
 * @package wp-job-manager
 */

if ( ! function_exists( 'get_job_listings' ) ) :
	/**
	 * Queries job listings with certain criteria and returns them.
	 */
	function get_job_listings( $args = [] ) {
		global $job_manager_keyword;

		$args = wp_parse_args(
			$args,
			[
				'search_location'   => '',
				'search_keywords'   => '',
				'search_categories' => [],
				'job_types'         => [],
				'post_status'       => [],
				'offset'            => 0,
				'posts_per_page'    => 20,
				'orderby'           => 'date',
				'order'             => 'DESC',
				'featured'          => null,
				'filled'            => null,
				'remote_position'   => null,
				'fields'            => 'all',
				'featured_first'    => 0,
			]
		);

		do_action( 'get_job_listings_init', $args );

		if ( ! empty( $args['post_status'] ) ) {
			$post_status = $args['post_status'];
		} elseif ( 0 === intval( get_option( 'job_manager_hide_expired', get_option( 'job_manager_hide_expired_content', 1 ) ) ) ) {
			$post_status = [ 'publish', 'expired' ];
		} else {
			$post_status = 'publish';
		}

		$query_args = [
			'post_type'              => \WP_Job_Manager_Post_Types::PT_LISTING,
			'post_status'            => $post_status,
			'ignore_sticky_posts'    => 1,
			'offset'                 => absint( $args['offset'] ),
			'posts_per_page'         => intval( $args['posts_per_page'] ),
			'orderby'                => $args['orderby'],
			'order'                  => $args['order'],
			'tax_query'              => [],
			'meta_query'             => [],
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'cache_results'          => false,
			'fields'                 => $args['fields'],
		];

		// ==================== FORCE SORT BY LOCATION A-Z ====================
		$query_args['orderby']  = 'meta_value';
		$query_args['meta_key'] = '_job_location';
		$query_args['order']    = 'ASC';

		if ( $args['posts_per_page'] < 0 ) {
			$query_args['no_found_rows'] = true;
		}

		$remote_position_search = false;

		if ( ! is_null( $args['remote_position'] ) ) {
			$remote_position_search = [
				'key'     => '_remote_position',
				'value'   => '1',
				'compare' => $args['remote_position'] ? '=' : '!=',
			];

			if ( '!=' === $remote_position_search['compare'] && apply_filters( 'job_manager_get_job_listings_remote_position_check_not_exists', true, $args ) ) {
				$remote_position_search = [
					'relation' => 'OR',
					$remote_position_search,
					[
						'key'     => '_remote_position',
						'compare' => 'NOT EXISTS',
					],
				];
			}
		}

		if ( ! empty( $args['search_location'] ) ) {
			$location_meta_keys = [ 'geolocation_formatted_address', '_job_location', 'geolocation_state_long' ];
			$location_search    = [ 'relation' => 'OR' ];
			$locations          = explode( ';', $args['search_location'] );

			foreach ( $locations as $location ) {
				$location = trim( $location );
				if ( ! empty( $location ) ) {
					$location_subquery = [ 'relation' => 'OR' ];
					foreach ( $location_meta_keys as $meta_key ) {
						$location_subquery[] = [
							'key'     => $meta_key,
							'value'   => $location,
							'compare' => 'like',
						];
					}
					$location_search[] = $location_subquery;
				}
			}

			if ( $remote_position_search ) {
				$location_search = [
					'relation' => 'AND',
					$remote_position_search,
					$location_search,
				];
			}

			$query_args['meta_query'][] = $location_search;

		} elseif ( $remote_position_search ) {
			$query_args['meta_query'][] = $remote_position_search;
		}

		if ( ! is_null( $args['featured'] ) ) {
			$query_args['meta_query'][] = [
				'key'     => '_featured',
				'value'   => '1',
				'compare' => $args['featured'] ? '=' : '!=',
			];
		}

		if ( ! is_null( $args['filled'] ) || 1 === absint( get_option( 'job_manager_hide_filled_positions' ) ) ) {
			$query_args['meta_query'][] = [
				'key'     => '_filled',
				'value'   => '1',
				'compare' => $args['filled'] ? '=' : '!=',
			];
		}

		if ( ! empty( $args['job_types'] ) ) {
			$query_args['tax_query'][] = [
				'taxonomy' => \WP_Job_Manager_Post_Types::TAX_LISTING_TYPE,
				'field'    => 'slug',
				'terms'    => $args['job_types'],
			];
		}

		if ( ! empty( $args['search_categories'] ) ) {
			$field                     = is_numeric( $args['search_categories'][0] ) ? 'term_id' : 'slug';
			$operator                  = 'all' === get_option( 'job_manager_category_filter_type', 'all' ) && count( $args['search_categories'] ) > 1 ? 'AND' : 'IN';
			$query_args['tax_query'][] = [
				'taxonomy'         => \WP_Job_Manager_Post_Types::TAX_LISTING_CATEGORY,
				'field'            => $field,
				'terms'            => array_values( $args['search_categories'] ),
				'include_children' => 'AND' !== $operator,
				'operator'         => $operator,
			];
		}

		$job_manager_keyword = sanitize_text_field( $args['search_keywords'] );
		if ( ! empty( $job_manager_keyword ) && strlen( $job_manager_keyword ) >= apply_filters( 'job_manager_get_listings_keyword_length_threshold', 2 ) ) {
			$query_args['s'] = $job_manager_keyword;
			add_filter( 'posts_search', 'get_job_listings_keyword_search', 10, 2 );
		}

		$query_args = apply_filters( 'job_manager_get_listings', $query_args, $args );

		if ( empty( $query_args['meta_query'] ) ) {
			unset( $query_args['meta_query'] );
		}
		if ( empty( $query_args['tax_query'] ) ) {
			unset( $query_args['tax_query'] );
		}

		$query_args['lang'] = apply_filters( 'wpjm_lang', null );

		$query_args = apply_filters( 'get_job_listings_query_args', $query_args, $args );

		do_action( 'before_get_job_listings', $query_args, $args );

		$should_cache = true; // we disabled random ordering

		if ( apply_filters( 'get_job_listings_cache_results', $should_cache ) ) {
			$to_hash         = wp_json_encode( $query_args );
			$query_args_hash = 'jm_' . md5( $to_hash . JOB_MANAGER_VERSION ) . WP_Job_Manager_Cache_Helper::get_transient_version( 'get_job_listings' );

			$cached = get_transient( $query_args_hash );
			if ( false !== $cached && is_string( $cached ) ) {
				$cached = json_decode( $cached, false );
				if ( $cached && isset( $cached->posts ) ) {
					$result = new WP_Query();
					$result->parse_query( $query_args );
					$result->posts = ( in_array( $query_args['fields'], [ 'ids', 'id=>parent' ], true ) ) 
						? $cached->posts 
						: array_map( 'get_post', $cached->posts );
					$result->found_posts   = intval( $cached->found_posts ?? 0 );
					$result->max_num_pages = intval( $cached->max_num_pages ?? 0 );
					$result->post_count    = count( $result->posts );
					return $result;
				}
			}
		}

		$result = new WP_Query( $query_args );

		if ( apply_filters( 'get_job_listings_cache_results', $should_cache ) ) {
			$cacheable = [
				'posts'        => array_values( $result->posts ),
				'found_posts'  => $result->found_posts,
				'max_num_pages' => $result->max_num_pages,
			];
			set_transient( $query_args_hash, wp_json_encode( $cacheable ), DAY_IN_SECONDS );
		}

		do_action( 'after_get_job_listings', $query_args, $args );
		remove_filter( 'posts_search', 'get_job_listings_keyword_search', 10 );

		return $result;
	}
endif;

// Keep the rest of your original file (all other functions) unchanged.
// Just make sure this get_job_listings function is replaced with the one above.
