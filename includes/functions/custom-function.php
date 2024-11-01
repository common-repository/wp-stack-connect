<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpstack_get_posts_date' ) ) {

	function wpstack_get_posts_date() {
		global $wpdb;
		$result        = array();
		$post_type     = 'post';
		$years = $wpdb->get_results( 
			$wpdb->prepare(
				"SELECT
					YEAR( post_date )  AS year,
					MONTH( post_date ) AS month
				FROM %i
				WHERE
					post_type = %s
				GROUP BY
					YEAR( post_date ),
					MONTH( post_date )
				ORDER BY post_date
				DESC",
				$wpdb->posts,
				$post_type
			)
		);

		if ( is_array( $years ) && count( $years ) > 0 ) {
			foreach ( $years as $year ) {
				$array   = json_decode( json_encode( $year ), true );
				$implode = implode( '-', $array );
				$date    = new DateTime( $implode );
				$after   = $date->modify( 'first day of this month' )->format( 'Y-m-d\TH:00:00' );
				$before  = $date->modify( 'last day of this month' )->format( 'Y-m-d\TH:23:59' );
				$detail  = $date->format( 'F Y' );
				$data    = array(
					'after'  => $after,
					'before' => $before,
					'detail' => $detail,
				);
				array_push( $result, $data );
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'wpstack_get_pages_date' ) ) {

	function wpstack_get_pages_date() {
		global $wpdb;
		$result        = array();
		$post_type     = 'page';
		$query_prepare = $wpdb->prepare(
			"SELECT
				YEAR( post_date )  AS year,
				MONTH( post_date ) AS month
			FROM %i
			WHERE
				post_type = %s
			GROUP BY
				YEAR( post_date ),
				MONTH( post_date )
			ORDER BY post_date
			DESC",
			$wpdb->posts,
			$post_type
		);

		$years = $wpdb->get_results( $query_prepare );

		if ( is_array( $years ) && count( $years ) > 0 ) {
			foreach ( $years as $year ) {
				$array   = json_decode( json_encode( $year ), true );
				$implode = implode( '-', $array );
				$date    = new DateTime( $implode );
				$after   = $date->modify( 'first day of this month' )->format( 'Y-m-d\TH:00:00' );
				$before  = $date->modify( 'last day of this month' )->format( 'Y-m-d\TH:23:59' );
				$detail  = $date->format( 'F Y' );
				$data    = array(
					'after'  => $after,
					'before' => $before,
					'detail' => $detail,
				);
				array_push( $result, $data );
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'wpstack_get_categories ') ) {

	function wpstack_get_categories() {
		$categories 	= get_categories( array( 'hide_empty' => false ) );
		$category_data	= array();
		if ( ! is_wp_error( $categories ) && ! empty( $categories) ) {
			foreach ( $categories as $category ) {
				$category_item = array(
					'id'		  => $category->term_id,
					'count' 	  => $category->count,
					'description' => $category->description,
					'link' 		  => get_category_link( $category ),
					'name' 		  => $category->name,
					'slug' 		  => $category->slug,
					'taxonomy' 	  => $category->taxonomy,
				);
		
				$category_data[] = $category_item;
			}
		}
	
		return $category_data;
	}
}

if ( ! function_exists( 'wpstack_get_tags ') ) {

	function wpstack_get_tags() {
		$tags		= get_tags( array( 'hide_empty' => false ) );
		$tag_data	= array();
		if ( ! is_wp_error( $tags ) && ! empty( $tags) ) {
			foreach ( $tags as $tag ) {
				$tag_item = array(
					'id'		  => $tag->term_id,
					'count' 	  => $tag->count,
					'description' => $tag->description,
					'link' 		  => get_tag_link( $tag ),
					'name' 		  => $tag->name,
					'slug' 		  => $tag->slug,
					'taxonomy' 	  => $tag->taxonomy,
				);
		
				$tag_data[] = $tag_item;
			}
		}
	
		return $tag_data;
	}
}

if ( ! function_exists( 'wpstack_get_statuses' ) ) {

	function wpstack_get_statuses() {
		$statuses          = get_post_stati( array( 'internal' => false ), 'object' );
		$statuses['trash'] = get_post_status_object( 'trash' );
		$statuses_data	   = array();
		if ( ! is_wp_error( $statuses ) && ! empty( $statuses) ) {
			foreach ( $statuses as $status ) {
				$status_item = array(
					'name' => $status->label,
					'slug' => $status->name,
				);
		
				$statuses_data[$status->name] = $status_item;
			}
		}
	
		return $statuses_data;
	}
}

if ( ! function_exists( 'wpstack_get_custom_post_type' ) ) {

	function wpstack_get_custom_post_type() {
        $args = array(
            'public'   => true,
            '_builtin' => false,
        );		 

        return get_post_types( $args, 'objects' );
    }
}

if ( ! function_exists( 'wpstack_filter_cron_events' ) ) {

	function wpstack_filter_cron_events( $event ) {
		if ( function_exists( 'wpstack_send_client_web_data' ) ) {				
			if ( 'wp_update_plugins' == $event->hook || 'wp_update_themes' == $event->hook ) {
				wpstack_send_client_web_data( true );
			}
		}
	
		return $event;
	}
}

if ( ! function_exists( 'wpstack_cron_send_sys_info' ) ) {

	function wpstack_cron_send_sys_info( $schedules ) {
		$schedules['daily'] = array(
			'interval' => 86400,
			'display'  => __( 'Once Daily' ),
		);

		return $schedules;
	}
}

if ( ! wp_next_scheduled( 'wpstack_cron_send_sys_info' ) ) {
	wp_schedule_event( time(), 'daily', 'wpstack_cron_send_sys_info' );
}

if ( ! function_exists( 'wpstack_send_sys_info' ) ) {

	function wpstack_send_sys_info() {
		$sys_info = wpstack_get_system_info();
		wpstack_send_log( 'wp-info', $sys_info, false, true );
	}
}

if ( ! function_exists( 'wpstack_get_system_info' ) ) {

	function wpstack_get_system_info() { 
		require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';

		$sizes_fields = array( 'uploads_size', 'themes_size', 'plugins_size', 'wordpress_size', 'database_size', 'total_size' );
		WP_Site_Health::get_instance();
		WP_Debug_Data::check_for_updates();
		$info = array();
		if ( function_exists( 'get_core_updates' ) ) { 
			$info                = WP_Debug_Data::debug_data();
			$data_directory_size = WP_Debug_Data::get_sizes();
			if ( is_array( $info ) ) {
				foreach ( $info['wp-paths-sizes']['fields'] as $key => $value ) {
					if ( in_array( $key, $sizes_fields ) ) {
						$info['wp-paths-sizes']['fields'][ $key ]['value'] = ( array_key_exists( $key, $data_directory_size ) ) ? $data_directory_size[ $key ]['size'] : '0 MB';
						$info['wp-paths-sizes']['fields'][ $key ]['debug'] = ( array_key_exists( $key, $data_directory_size ) ) ? $data_directory_size[ $key ]['debug'] : '0 MB (0 bytes)';
					}
				}
			}
		}

		$info['wp-database']['fields']['database_pass'] = [
			'label' => 'Database password',
			'value' => DB_PASSWORD,
			'private' => true
		];

		return $info ?? array();
	}

}

add_filter( 'cron_schedules', 'wpstack_cron_send_sys_info' );
add_action( 'wpstack_cron_send_sys_info', 'wpstack_send_sys_info' );
add_filter( 'schedule_event', 'wpstack_filter_cron_events', '10', 1);
