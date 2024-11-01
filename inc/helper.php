<?php
/**
 * Uses for global
 */
namespace WTWE\Helper;

use WP_Query;

if( ! class_exists( 'WTWE\Helper\WTWE_Helper' ) ) {
	class WTWE_Helper {
		/**
		 * Get itinerary data
		 */
		public static function wtwe_get_trip_itinerary() {
			$trip_args            = array(
				'posts_per_page' => -1,
				'post_type'      => 'itineraries',
				'post_status'    => 'publish',
			);
			$trip_data            = new WP_Query( $trip_args );
			$trip_itinerary       = array();
			$trip_include         = array();
			$trip_exclude         = array();
			$wptravel_time_format = get_option( 'time_format' );
			while ( $trip_data->have_posts() ) {
				$trip_data->the_post();
				$trip_id        = get_the_ID();
				$itinerary_data = get_post_meta( $trip_id, 'wp_travel_trip_itinerary_data', true );
				
				if( !is_string( $itinerary_data ) ){
					foreach ( $itinerary_data as $key => $value ) {
						$itinerary_data[ $key ]['date']  = isset( $value['date'] ) ? wptravel_format_date( $value['date'] ) : '';
						$itinerary_data[ $key ]['title'] = isset( $value['title'] ) ? stripslashes( $value['title'] ) : '';
						$itinerary_data[ $key ]['desc']  = isset( $value['desc'] ) ? wp_kses_post( $value['desc'] ) : '';
						$wptravel_itinerary_time         = isset( $value['time'] ) ? stripslashes( $value['time'] ) : '';
						$itinerary_data[ $key ]['time']  = ! empty( $wptravel_itinerary_time ) ? date( $wptravel_time_format, strtotime( $wptravel_itinerary_time ) ) : ''; // @phpcs:ignore
					}
				}
				
				$trip_itinerary[ $trip_id ] = $itinerary_data;
				$trip_include[ $trip_id ]   = get_post_meta( $trip_id, 'wp_travel_trip_include', true );
				$trip_exclude[ $trip_id ]   = get_post_meta( $trip_id, 'wp_travel_trip_exclude', true );
			}
			$trp_data = array(
				'itinerary'    => $trip_itinerary,
				'trip_include' => $trip_include,
				'trip_exclude' => $trip_exclude,
			);
			return $trp_data;
		}
	
		/**
		 * Get WP Travel Trips by taxonomy.
		 * 
		 * @since 1.0.0
		 * @access public
		 * @static
		 * @return array
		 */
		public static function wtwe_get_wp_travel_trips_by_type() {
			$term_trips = [];
			$query_trips_ids = new WP_Query( array(
				'posts_per_page'   => -1,
				'post_type'        => 'itineraries',
				'post_status'      => 'publish',
			) );
	
			$trip_details = [];
			while( $query_trips_ids->have_posts() ) {
				$query_trips_ids->the_post();
	
				$trip_id = get_the_ID();
				$args = $args_regular = array( 'trip_id' => $trip_id );
				$trip_price = \WP_Travel_Helpers_Pricings::get_price( $args );
				$args_regular = $args;
				$args_regular['is_regular_price'] = true;
				$regular_price = \WP_Travel_Helpers_Pricings::get_price( $args_regular );
				$enable_sale = \WP_Travel_Helpers_Trips::is_sale_enabled(
					array(
						'trip_id'                => $trip_id,
						'from_price_sale_enable' => true,
					)
				);
	
				$taxonomies = [];
				$term_list = get_the_terms( $trip_id, 'itinerary_types', true );
				if ( is_array( $term_list ) && count( $term_list ) > 0 ) {
					foreach(  $term_list as $tax ) {
						array_push( $taxonomies, $tax->name );
					}
				}
				$is_fixed_departure = \WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	
				$trip_details[] = [
					'trip_id' => $trip_id,
					'permalink' => esc_url( get_the_permalink($trip_id) ),
					'title' => esc_html( get_the_title() ),
					'thumbnail_image' => wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ),
					'tags' => $taxonomies,
					'excerpt' => esc_html( get_the_excerpt() ),
					'pax' => esc_html( wptravel_get_group_size( $trip_id ) ),
					'is_fixed_departure' => $is_fixed_departure,
					'duration' => $is_fixed_departure ? wptravel_get_fixed_departure_date( $trip_id ) : wp_travel_get_trip_durations( $trip_id ),
					'average_rating' => esc_html( wptravel_get_average_rating( $trip_id ) ),
					'trip_price' => $trip_price,
					'trip_code'		=> esc_html( get_post_meta( $trip_id, 'wp_travel_trip_code', true ) ),
					'trip_location'		=> esc_html( get_post_meta( $trip_id, 'wp_travel_location', true ) ),
					'regular_price' =>  $regular_price,
					'trip_price_html' => wptravel_get_formated_price_currency( $trip_price ),
					'regular_price_html' => wptravel_get_formated_price_currency( $regular_price ),
				];
			}
			$term_trips[ 'trips_ids' ] = $trip_details;
	
			wp_reset_postdata();
	
			$trip_details = [];
			$query_featured_trips = new WP_Query( array(
				'posts_per_page'   => -1,
				'meta_key'         => 'wp_travel_featured',
				'meta_value'       => 'yes',
				'post_type'        => 'itineraries',
				'post_status'      => 'publish',
			) );
	
			while( $query_featured_trips->have_posts() ) {
				$query_featured_trips->the_post();
	
				$trip_id = get_the_ID();
				$args = $args_regular = array( 'trip_id' => $trip_id );
				$trip_price = \WP_Travel_Helpers_Pricings::get_price( $args );
				$args_regular = $args;
				$args_regular['is_regular_price'] = true;
				$regular_price = \WP_Travel_Helpers_Pricings::get_price( $args_regular );
				$enable_sale = \WP_Travel_Helpers_Trips::is_sale_enabled(
					array(
						'trip_id'                => $trip_id,
						'from_price_sale_enable' => true,
					)
				);
	
				$taxonomies = [];
				if(get_the_terms( $trip_id, 'itinerary_types' )){
                    foreach( get_the_terms( $trip_id, 'itinerary_types' ) as $tax ) {
                        array_push( $taxonomies, $tax->name );
                    }
				}
	
				$is_fixed_departure = \WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	
				$trip_details[] = [
					'trip_id' => $trip_id,
					'permalink' => esc_url( get_the_permalink($trip_id) ),
					'title' => esc_html( get_the_title() ),
					'thumbnail_image' => wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ),
					'tags' => $taxonomies,
					'excerpt' => esc_html( get_the_excerpt() ),
					'pax' => esc_html( wptravel_get_group_size( $trip_id ) ),
					'is_fixed_departure' => $is_fixed_departure,
					'duration' => $is_fixed_departure ? wptravel_get_fixed_departure_date( $trip_id ) : wp_travel_get_trip_durations( $trip_id ),
					'average_rating' => esc_html( wptravel_get_average_rating( $trip_id ) ),
					'trip_price' => $trip_price,
					'regular_price' =>  $regular_price,
					'trip_price_html' => wptravel_get_formated_price_currency( $trip_price ),
					'regular_price_html' => wptravel_get_formated_price_currency( $regular_price ),
				];
			}
			$term_trips[ 'featured_trips' ] = $trip_details;
	
			wp_reset_postdata();
	
			$query_term_itinerary_types = get_terms( array( 
				'taxonomy' => 'itinerary_types',
				'hide_empty' => false,
				'number' => '',
			) );
	
			$query_term_travel_locations = get_terms( array( 
				'taxonomy' => 'travel_locations',
				'hide_empty' => false,
				'number' => '',
			) );
	
			$query_term_activity = get_terms( array( 
				'taxonomy' => 'activity',
				'hide_empty' => false,
				'number' => '',
			) );
	
			foreach( $query_term_itinerary_types as $key => $value ) {
				$term_id = $value->term_id;
				$query_trips = new WP_Query( array(
					'posts_per_page'   => -1,
					'post_type'        => 'itineraries',
					'post_status'      => 'publish',
					'tax_query'        => array(
						array(
							'taxonomy' => 'itinerary_types',
							'field'    => 'term_id',
							'terms'    => $term_id,
						),
					),
				) );
	
				$trip_details = [];
				while( $query_trips->have_posts() ) {
					$query_trips->the_post();
	
					$trip_id = get_the_ID();
					$args = $args_regular = array( 'trip_id' => $trip_id );
					$trip_price = \WP_Travel_Helpers_Pricings::get_price( $args );
					$args_regular = $args;
					$args_regular['is_regular_price'] = true;
					$regular_price = \WP_Travel_Helpers_Pricings::get_price( $args_regular );
					$enable_sale = \WP_Travel_Helpers_Trips::is_sale_enabled(
						array(
							'trip_id'                => $trip_id,
							'from_price_sale_enable' => true,
						)
					);
	
					$taxonomies = [];
					foreach( get_the_terms( $trip_id, 'itinerary_types' ) as $tax ) {
						array_push( $taxonomies, $tax->name );
					}
	
					$is_fixed_departure = \WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	
					$trip_details[] = [
						'trip_id' => $trip_id,
						'permalink' => esc_url( get_the_permalink($trip_id) ),
						'title' => esc_html( get_the_title() ),
						'thumbnail_image' => wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ),
						'tags' => $taxonomies,
						'excerpt' => esc_html( get_the_excerpt() ),
						'pax' => esc_html( wptravel_get_group_size( $trip_id ) ),
						'is_fixed_departure' => $is_fixed_departure,
						'duration' => $is_fixed_departure ? wptravel_get_fixed_departure_date( $trip_id ) : wp_travel_get_trip_durations( $trip_id ),
						'average_rating' => esc_html( wptravel_get_average_rating( $trip_id ) ),
						'trip_price' => $trip_price,
						'regular_price' =>  $regular_price,
						'trip_price_html' => wptravel_get_formated_price_currency( $trip_price ),
						'regular_price_html' => wptravel_get_formated_price_currency( $regular_price ),
					];
				}
				$term_trips[ 'itinerary_types' ][ $term_id ] = $trip_details;
			}
	
			wp_reset_postdata();
	
			foreach( $query_term_travel_locations as $key => $value ) {
				$term_id = $value->term_id;
				$query_trips = new WP_Query( array(
					'posts_per_page'   => -1,
					'post_type'        => 'itineraries',
					'post_status'      => 'publish',
					'tax_query'        => array(
						array(
							'taxonomy' => 'travel_locations',
							'field'    => 'term_id',
							'terms'    => $term_id,
						),
					),
				) );
	
				$trip_details = [];
				while( $query_trips->have_posts() ) {
					$query_trips->the_post();
	
					$trip_id = get_the_ID();
					$args = $args_regular = array( 'trip_id' => $trip_id );
					$trip_price = \WP_Travel_Helpers_Pricings::get_price( $args );
					$args_regular = $args;
					$args_regular['is_regular_price'] = true;
					$regular_price = \WP_Travel_Helpers_Pricings::get_price( $args_regular );
					$enable_sale = \WP_Travel_Helpers_Trips::is_sale_enabled(
						array(
							'trip_id'                => $trip_id,
							'from_price_sale_enable' => true,
						)
					);
	
					$taxonomies = [];
					foreach( get_the_terms( $trip_id, 'travel_locations' ) as $tax ) {
						array_push( $taxonomies, $tax->name );
					}
	
					$is_fixed_departure = \WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	
					$trip_details[] = [
						'trip_id' => $trip_id,
						'permalink' => esc_url( get_the_permalink($trip_id) ),
						'title' => esc_html( get_the_title() ),
						'thumbnail_image' => wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ),
						'tags' => $taxonomies,
						'excerpt' => esc_html( get_the_excerpt() ),
						'pax' => esc_html( wptravel_get_group_size( $trip_id ) ),
						'is_fixed_departure' => $is_fixed_departure,
						'duration' => $is_fixed_departure ? wptravel_get_fixed_departure_date( $trip_id ) : wp_travel_get_trip_durations( $trip_id ),
						'average_rating' => esc_html( wptravel_get_average_rating( $trip_id ) ),
						'trip_price' => $trip_price,
						'regular_price' =>  $regular_price,
						'trip_price_html' => wptravel_get_formated_price_currency( $trip_price ),
						'regular_price_html' => wptravel_get_formated_price_currency( $regular_price ),
					];
				}
				$term_trips[ 'travel_locations' ][ $term_id ] = $trip_details;
			}
	
			wp_reset_postdata();
	
			foreach( $query_term_activity as $key => $value ) {
				$term_id = $value->term_id;
				$query_trips = new WP_Query( array(
					'posts_per_page'   => -1,
					'post_type'        => 'itineraries',
					'post_status'      => 'publish',
					'tax_query'        => array(
						array(
							'taxonomy' => 'activity',
							'field'    => 'term_id',
							'terms'    => $term_id,
						),
					),
				) );
	
				$trip_details = [];
				while( $query_trips->have_posts() ) {
					$query_trips->the_post();
	
					$trip_id = get_the_ID();
					$args = $args_regular = array( 'trip_id' => $trip_id );
					$trip_price = \WP_Travel_Helpers_Pricings::get_price( $args );
					$args_regular = $args;
					$args_regular['is_regular_price'] = true;
					$regular_price = \WP_Travel_Helpers_Pricings::get_price( $args_regular );
					$enable_sale = \WP_Travel_Helpers_Trips::is_sale_enabled(
						array(
							'trip_id'                => $trip_id,
							'from_price_sale_enable' => true,
						)
					);
	
					$taxonomies = [];
					foreach( get_the_terms( $trip_id, 'activity' ) as $tax ) {
						array_push( $taxonomies, $tax->name );
					}
	
					$is_fixed_departure = \WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	
					$trip_details[] = [
						'trip_id' => $trip_id,
						'permalink' => esc_url( get_the_permalink($trip_id) ),
						'title' => esc_html( get_the_title() ),
						'thumbnail_image' => wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ),
						'tags' => $taxonomies,
						'excerpt' => esc_html( get_the_excerpt() ),
						'pax' => esc_html( wptravel_get_group_size( $trip_id ) ),
						'is_fixed_departure' => $is_fixed_departure,
						'duration' => $is_fixed_departure ? wptravel_get_fixed_departure_date( $trip_id ) : wp_travel_get_trip_durations( $trip_id ),
						'average_rating' => esc_html( wptravel_get_average_rating( $trip_id ) ),
						'trip_price' => $trip_price,
						'regular_price' =>  $regular_price,
						'trip_price_html' => wptravel_get_formated_price_currency( $trip_price ),
						'regular_price_html' => wptravel_get_formated_price_currency( $regular_price ),
					];
				}
				$term_trips[ 'activity' ][ $term_id ] = $trip_details;
			}
	
			wp_reset_postdata();
	
			return $term_trips;
		}
	
		/**
		 * Get WP Travel Featured Trips
		 * 
		 * @since 1.0.0
		 * @access public
		 * @static
		 */
		public static function wtwe_get_wp_travel_featured_trips() {
			$featured_trips = [];
			$args_asc  = [
				'posts_per_page'   => -1,
				'meta_key'         => 'wp_travel_featured',
				'meta_value'       => 'yes',
				'post_type'        => 'itineraries',
				'post_status'      => 'publish',
				'order'		   	   => "ASC",
			];
	
			$itineraries = new WP_Query( $args_asc );
	
			$args_desc  = [
				'posts_per_page'   => -1,
				'meta_key'         => 'wp_travel_featured',
				'meta_value'       => 'yes',
				'post_type'        => 'itineraries',
				'post_status'      => 'publish',
				'order'		   	   => "DESC",
			];
	
			$itineraries2 = new WP_Query( $args_desc );
	
			if( $itineraries->have_posts() ) {
				while( $itineraries->have_posts() ) {
					$itineraries->the_post();
	
					$trip_id = get_the_ID();
					$args = $args_regular = array( 'trip_id' => $trip_id );
					$trip_price = \WP_Travel_Helpers_Pricings::get_price( $args );
					$args_regular = $args;
					$args_regular['is_regular_price'] = true;
					$regular_price = \WP_Travel_Helpers_Pricings::get_price( $args_regular );
					$enable_sale = \WP_Travel_Helpers_Trips::is_sale_enabled(
						array(
							'trip_id'                => $trip_id,
							'from_price_sale_enable' => true,
						)
					);
	
					$taxonomies = [];
			
					
					if(get_the_terms( $trip_id, 'itinerary_types' )){ 
						foreach( get_the_terms( $trip_id, 'itinerary_types' ) as $tax ) {
                            array_push( $taxonomies, $tax->name );
                        }
					}
					
	
					$is_fixed_departure = \WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	
					$featured_trips["ASC"][] = [
						'trip_id' => $trip_id,
						'permalink' => esc_url( get_the_permalink($trip_id) ),
						'title' => esc_html( get_the_title() ),
						'thumbnail_image' => wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ),
						'tags' => $taxonomies,
						'excerpt' => esc_html( get_the_excerpt() ),
						'pax' => esc_html( wptravel_get_group_size( $trip_id ) ),
						'is_fixed_departure' => $is_fixed_departure,
						'duration' => $is_fixed_departure ? wptravel_get_fixed_departure_date( $trip_id ) : wp_travel_get_trip_durations( $trip_id ),
						'average_rating' => esc_html( wptravel_get_average_rating( $trip_id ) ),
						'trip_price' => $trip_price,
						'regular_price' =>  $regular_price,
						'trip_price_html' => wptravel_get_formated_price_currency( $trip_price ),
						'regular_price_html' => wptravel_get_formated_price_currency( $regular_price ),
					];
				}
				// $featured_trips = $trip_details;
			}
	
			if( $itineraries2->have_posts() ) {
				while( $itineraries2->have_posts() ) {
					$itineraries2->the_post();
	
					$trip_id = get_the_ID();
					$args = $args_regular = array( 'trip_id' => $trip_id );
					$trip_price = \WP_Travel_Helpers_Pricings::get_price( $args );
					$args_regular = $args;
					$args_regular['is_regular_price'] = true;
					$regular_price = \WP_Travel_Helpers_Pricings::get_price( $args_regular );
					$enable_sale = \WP_Travel_Helpers_Trips::is_sale_enabled(
						array(
							'trip_id'                => $trip_id,
							'from_price_sale_enable' => true,
						)
					);
	
					$taxonomies = [];
					if(get_the_terms( $trip_id, 'itinerary_types' )){ 
                        foreach( get_the_terms( $trip_id, 'itinerary_types' ) as $tax ) {
                            array_push( $taxonomies, $tax->name );
                        }
					}	
	
					$is_fixed_departure = \WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	
					$featured_trips["DESC"][] = [
						'trip_id' => $trip_id,
						'permalink' => esc_url( get_the_permalink($trip_id) ),
						'title' => esc_html( get_the_title() ),
						'thumbnail_image' => wptravel_get_post_thumbnail( $trip_id, 'wp_travel_thumbnail' ),
						'tags' => $taxonomies,
						'excerpt' => esc_html( get_the_excerpt() ),
						'pax' => esc_html( wptravel_get_group_size( $trip_id ) ),
						'is_fixed_departure' => $is_fixed_departure,
						'duration' => $is_fixed_departure ? wptravel_get_fixed_departure_date( $trip_id ) : wp_travel_get_trip_durations( $trip_id ),
						'average_rating' => esc_html( wptravel_get_average_rating( $trip_id ) ),
						'trip_price' => $trip_price,
						'regular_price' =>  $regular_price,
						'trip_price_html' => wptravel_get_formated_price_currency( $trip_price ),
						'regular_price_html' => wptravel_get_formated_price_currency( $regular_price ),
					];
				}
			}
			
			return $featured_trips;
		}
	
		/**
		 * Get WP Travel trips for category trips widget.
		 * 
		 * @since 1.0.0
		 * @access public
		 * @static
		 */
		public static function wtwe_get_wp_travel_category_trips() {
			$terms = [];
			$terms_itinerary_types = get_terms(
				array(
					'taxonomy'   => 'itinerary_types',
					'hide_empty' => false,
				)
			);
	
			$terms_travel_locations = get_terms(
				array(
					'taxonomy'   => 'travel_locations',
					'hide_empty' => false,
				)
			);
	
			$terms_activity = get_terms(
				array(
					'taxonomy'   => 'activity',
					'hide_empty' => false,
				)
			);
	
			$itinerary_post_type_slug = get_post_type_archive_link('itineraries');
	
			foreach( $terms_itinerary_types as $key => $term ) {
				$term_id = $term->term_id;
				$thumbnail_id = get_term_meta( $term_id, 'wp_travel_trip_type_image_id', true );
				$query_slug = '';
				if ( strpos( $itinerary_post_type_slug, '?' ) == true ) {
					$query_slug = $itinerary_post_type_slug . '&' . 'itinerary_types' . '=' .  $term->slug;
				} else {
					$query_slug = $itinerary_post_type_slug . '?' . 'itinerary_types' . '=' . $term->slug;
				}
				$terms[ 'itinerary_types' ][] = [ 
					'term_id' => $term_id,
					'term_name' => $term->name,
					'thumbnail_id' => get_term_meta( $term_id, 'wp_travel_trip_type_image_id', true ),
					'term_image_url' => wp_get_attachment_thumb_url($thumbnail_id, 'full'),
					'query_slug' => $query_slug,
					'trip_count' => $term->count,
				];
			}
	
			foreach( $terms_travel_locations as $key => $term ) {
				$term_id = $term->term_id;
				$thumbnail_id = get_term_meta( $term_id, 'wp_travel_trip_type_image_id', true );
				$query_slug = '';
				if ( strpos( $itinerary_post_type_slug, '?' ) == true ) {
					$query_slug = $itinerary_post_type_slug . '&' . 'travel_locations' . '=' .  $term->slug;
				} else {
					$query_slug = $itinerary_post_type_slug . '?' . 'travel_locations' . '=' . $term->slug;
				}
				$terms[ 'travel_locations' ][] = [ 
					'term_id' => $term_id,
					'term_name' => $term->name,
					'thumbnail_id' => get_term_meta( $term_id, 'wp_travel_trip_type_image_id', true ),
					'term_image_url' => wp_get_attachment_thumb_url($thumbnail_id, 'full'),
					'query_slug' => $query_slug,
					'trip_count' => $term->count,
				];
			}
	
			foreach( $terms_activity as $key => $term ) {
				$term_id = $term->term_id;
				$thumbnail_id = get_term_meta( $term_id, 'wp_travel_trip_type_image_id', true );
				$query_slug = '';
				if ( strpos( $itinerary_post_type_slug, '?' ) == true ) {
					$query_slug = $itinerary_post_type_slug . '&' . 'activity' . '=' .  $term->slug;
				} else {
					$query_slug = $itinerary_post_type_slug . '?' . 'activity' . '=' . $term->slug;
				}
				$terms[ 'activity' ][] = [ 
					'term_id' => $term_id,
					'term_name' => $term->name,
					'thumbnail_id' => get_term_meta( $term_id, 'wp_travel_trip_type_image_id', true ),
					'term_image_url' => wp_get_attachment_thumb_url($thumbnail_id, 'full'),
					'query_slug' => $query_slug,
					'trip_count' => $term->count,
				];
			}
	
			return $terms;
		}
	
		/**
		 * Get WP Travel trips for hero slider.
		 *
		 * @since 1.0.0
		 * @access public
		 * @static
		 */
		public static function wtwe_get_wp_travel_trips_hero_slider() {
			$trips = [];
	
			$terms_itinerary_types = get_terms(
				array(
					'taxonomy'   => 'itinerary_types',
					'hide_empty' => false,
				)
			);
	
			$terms_travel_locations = get_terms(
				array(
					'taxonomy'   => 'travel_locations',
					'hide_empty' => false,
				)
			);
	
			$terms_activity = get_terms(
				array(
					'taxonomy'   => 'activity',
					'hide_empty' => false,
				)
			);
	
			$query_trips_ids = new \WP_Query( array(
				'posts_per_page'   => -1,
				'offset'           => 0,
				'orderby'          => 'date',
				'order'            => 'ASC',
				'post_type'        => 'itineraries',
				'post_status'      => 'publish',
			) );
	
			if($query_trips_ids->have_posts()){
				while($query_trips_ids->have_posts()) {
					$query_trips_ids->the_post();
					$trip_id =  get_the_ID();
					$trip_title = get_the_title();
	
					$trips[ 'trips_ids' ][ $trip_id ] = $trip_title;
				}
			}
	
			if($query_trips_ids->have_posts()){
				while($query_trips_ids->have_posts()) {
					$query_trips_ids->the_post();
					$trip_id =  get_the_ID();
					$trip_title = get_the_title();
	
					$trips[ 'trips_ids' ][ $trip_id ] = $trip_title;
				}
			}
	
			$query_itinerary_types = new \WP_Query( array(
				'posts_per_page'   => -1,
				'offset'           => 0,
				'orderby'          => 'date',
				'order'            => 'ASC',
				'post_type'        => 'itineraries',
				'post_status'      => 'publish',
				'tax_query'        => array(
					array(
						'taxonomy' => 'itinerary_types',
					),
				),
			) );
	
			if($query_itinerary_types->have_posts()){
				while($query_itinerary_types->have_posts()) {
					$query_itinerary_types->the_post();
					$trip_id =  get_the_ID();
					$trip_title = get_the_title();
	
					$trips[ 'itinerary_types' ][ $trip_id ] = $trip_title;
				}
			}
		}
	
		/**
		 * Get WP Travel trips for Trip Meta.
		 *
		 * @since 1.0.0
		 * @access public
		 * @static
		 */
		public static function wtwe_get_wp_travel_trips_meta() {
			$trip_data = [];
			$trips = new WP_Query( [
				'posts_per_page' => -1,
				'post_type'      => 'itineraries',
				'post_status'    => 'publish',
			] );
	
			if($trips->have_posts()) {
				while($trips->have_posts()){
					$trips->the_post();
	
					$trip_id = get_the_ID();
					$separators = ['', ', ', ' - ', ' . ', ' _ ', ' * '];
					$content_types = [ 'itinerary_types', 'travel_locations', 'activity' ];
					foreach($content_types as $content_type) {
						foreach($separators as $separator) {
							$trip_types_list[ $content_type ][] = get_the_term_list( $trip_id, $content_type, '', $separator, '' );
						}
					}
	
					$group_size = wptravel_get_group_size( $trip_id );
					$review_count = (int) get_comments_number( $trip_id );
					$fixed_departure = \WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
					if( $fixed_departure ) {
						$date = wptravel_get_fixed_departure_date( $trip_id );
					} else {
						$date = wp_travel_get_trip_durations( $trip_id );
					}
	
					$trip_data[ $trip_id ] = [
						'trip_meta_value_list' => $trip_types_list,
						'group_size' => $group_size,
						'review_count' => $review_count,
						'fixed_departure' => $fixed_departure,
						'date' => $date,
					];
				}
			}
			return $trip_data;
		}
	
		/**
		 * Get WP Travel trips for Trip Rating.
		 *
		 * @since 1.0.0
		 * @access public
		 * @static
		 */
		
		public static function wtwe_get_wp_travel_meta_trip_rating() {
			$trip_data = [];
			$trips = new WP_Query( [
				'posts_per_page' => -1,
				'post_type'      => 'itineraries',
				'post_status'    => 'publish',
			] );
			
			if($trips->have_posts()) {
				while($trips->have_posts()){
					$trips->the_post();
					$trip_id = get_the_ID();
					$rating_html = esc_html( wptravel_single_trip_rating( $trip_id ) );
					$trip_data[ $trip_id ] = [
						'single_trip_rating' => $rating_html,
						'average_trip_rating' => wptravel_get_average_rating( $trip_id ),
					];
				}
			}
	
			return $trip_data;
		}
	
		/**
		 * Get WP Travel trips for Trip Rating.
		 *
		 * @since 1.0.0
		 * @access public
		 * @static
		 */
		public static function wtwe_get_wp_travel_trip_faq() {
			$trip_data = [];
			$trips = new WP_Query( [
				'posts_per_page' => -1,
				'post_type'      => 'itineraries',
				'post_status'    => 'publish',
			] );
	
			if($trips->have_posts()) {
				while($trips->have_posts()){
					$trips->the_post();
					$trip_id = get_the_ID();
					$trip_faqs = wptravel_get_faqs( $trip_id);
					foreach($trip_faqs as $faq) {
						$trip_data[ $trip_id ][] = $faq;
					}
				}
			}
	
			return $trip_data;
		}
	
		/**
		 * Trip Map datas.
		 *
		 * @return HTML.
		 * @param int $trip_id ID.
		 */
		public static function wtwe_get_wp_travel_elementor_map( $trip_id = null ) {
			if ( ! $trip_id ) {
				global $post;
				$trip_id = $post->ID;
			}
			$trip_map = array();
			ob_start();
			if ( function_exists( 'wptravel_trip_map' ) ) {
				wptravel_trip_map( $trip_id );
			} else {
				wp_travel_trip_map( $trip_id );
			}
				$content = ob_get_contents();
			ob_end_clean();
	
			$trip_map[ $trip_id ] = $content;
			return $trip_map;
		}
	
		/**
		 * Related Trips Data.
		 *
		 * @return HTML.
		 * @param int $trip_id ID.
		 */
		public static function wtwe_get_wp_travel_elementor_related_trips( $trip_id = null ) {
			if ( ! $trip_id ) {
				global $post;
				$trip_id = $post->ID;
			}
			$related_trips = array();
			ob_start();
			if ( function_exists( 'wptravel_get_related_post' ) ) {
				wptravel_get_related_post( $trip_id );
			} else {
				wp_travel_get_related_post( $trip_id );
			}
				$content = ob_get_contents();
			ob_end_clean();
	
			$related_trips[ $trip_id ] = $content;
			return $related_trips;
		}
	
		/**
		 * Trips Facts datas.
		 *
		 * @return HTML.
		 * @param int $trip_id ID.
		 */
		public static function wtwe_get_wp_travel_elementor_trips_facts_content( $trip_id = null ) {
			if ( ! $trip_id ) {
				global $post;
				$trip_id = $post->ID;
			}
			$WTWE_Trip_Facts = array();
			ob_start();
			if ( function_exists( 'wptravel_frontend_trip_facts' ) ) {
				wptravel_frontend_trip_facts( $trip_id );
			} else {
				wp_travel_frontend_trip_facts( $trip_id );
			}
				$content = ob_get_contents();
			ob_end_clean();
	
			$WTWE_Trip_Facts[ $trip_id ] = $content;
			if ( '' === $WTWE_Trip_Facts[ $trip_id ] ) {
				$WTWE_Trip_Facts[ $trip_id ] = esc_html__( '*Trip Facts Not Available.', 'wt-widgets-elementor' );
			}
			return $WTWE_Trip_Facts;
		}

		/**
		 * WTWE Notice.
		 *
		 * @return HTML.
		 * @param string message
		 * @param string error | success | info
		 */
		public static function wtwe_get_widget_notice( $message = "", $category = "info" ) {
			?>
                <div class="wtwe-notice-wrap">
                    <div class="wtwe-notice <?php echo esc_html($category) ?>">
					<?php
						if( $category == "info" ) { ?>
							<span class="dashicons dashicons-info"></span>
						<?php } elseif( $category == "success" ) { ?>
							<span class="dashicons dashicons-saved"></span>
						<?php } elseif( $category == "error" ) { ?>
							<span class="dashicons dashicons-dismiss"></span>
						<?php }
					?>
                        <p><?php echo esc_html( $message ) ?></p>
                    </div>
                </div>
            <?php
		}
	}
}