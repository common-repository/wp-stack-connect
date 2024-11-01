<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/core/class-wp-stack-connect-connection.php';

if ( ! function_exists( 'wpstack_add_failed_login_log' ) ) {

	function wpstack_add_failed_login_log( $id, $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $object_id, $description ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpstack_connect_activity_log';
		$check_duplicate = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT `alid` FROM %i
					WHERE `id` = %d
						AND `severity` = %s
						AND `modified_date` = %s
						AND `user_id` = %d
						AND `user_name` = %s
						AND `user_role` = %s
						AND `user_email` = %s
						AND `user_avatar` = %s
						AND `user_bio` = %s
						AND `ip_address` = %s
						AND `object_type` = %s
						AND `action` = %s
						AND `object_id` = %s
						AND `description` = %s
				;",
				$table_name,
				$id,
				$severity,
				$modified_date,
				$current_user_id,
				$current_user_name,
				$current_user_role,
				$current_user_email,
				$current_user_avatar,
				$current_user_bio,
				$ip_address,
				$object_type,
				$action,
				$object_id,
				$description
			)
		);
		if ( $check_duplicate ) {
			return;
		}
		
		$different_time			= 0;
		$now 				 	= time();
		$old					= $now - 60;
		$delay 					= 60 * 10;
		$thresshold   			= $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM %i WHERE `id` = %d AND `modified_date` <= %s AND `modified_date` >= %s',
				$table_name,
				$id,
				$now,
				$old
			)
		);
		$wpstack_connect_delay_request	= get_option( 'wpstack_connect_delay_request' );
		if ( $wpstack_connect_delay_request ) {
			$different_time	= $now - $wpstack_connect_delay_request;
		} 

		if ( ! $wpstack_connect_delay_request || $delay <= $different_time ) {
			if (count($thresshold) > 20) {				
				$id                  = 9982;
				$severity            = 'High';
				$modified_date       = time();
				$current_user_id     = 0;
				$current_user_name   = 'WP-Stack';
				$current_user_role   = '';
				$current_user_email  = '';
				$current_user_avatar = '';
				$current_user_bio    = '';
				$ip_address          = '-';
				$object_type         = 'System';
				$action              = 'Bruteforce Attemps';
				$object_id			 = 0;
				$description         = 'We suspect that your website is indicated to be hit by a bruteforce attack';
				$bruteforce_data 	 = wpstack_log_data_format( $id, $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $object_id, $description );
				$next_send_request	 = $now + $delay;
				$old_data   		 = $wpdb->get_results(
					$wpdb->prepare(
						'SELECT * FROM %i WHERE `id` = %d',
						$table_name,
						$id
					)
				);
				array_push( $old_data, $bruteforce_data );
				wpstack_send_log( 'login-log', $old_data, true );
				$wpdb->get_results( 
					$wpdb->prepare(
						"DELETE FROM %i WHERE `id` = %d",
						$table_name,
						$id
					)
				);
				if ( ! $wpstack_connect_delay_request ) {
					add_option( 'wpstack_connect_delay_request', $next_send_request );
				} else {
					update_option( 'wpstack_connect_delay_request', $next_send_request );
				}
			} else {
				$older_data		= array();
				$log_data 		= wpstack_log_data_format( $id, $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $object_id, $description );
				$insert_query	= $wpdb->insert( $table_name, $log_data );
				$older_time		= $now - $delay;
				if ( $wpstack_connect_delay_request ) {
					$older_data = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT * FROM %i WHERE `id` = %d AND `modified_date` < %s",
							$table_name,
							$id,
							$older_time
						)
					);
				}
				
				if ( 1 == $insert_query ) {
					array_push( $older_data, $log_data );
					wpstack_send_log( 'login-log', $older_data, true );
					$wpdb->get_results( 
						$wpdb->prepare(
							"DELETE FROM %i WHERE `id` = %d AND `modified_date` < %s",
							$table_name,
							$id,
							$older_time
						)
					);
				}
			}
		} else {
			$log_data	= wpstack_log_data_format( $id, $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $object_id, $description );
			$wpdb->insert( $table_name, $log_data );
		}
	}
}

if ( ! function_exists ( 'wpstack_add_login_success_log' ) ) {

	function wpstack_add_login_success_log ( $id, $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $object_id, $description ) {
		global $wpdb;
		$table_name     = $wpdb->prefix . 'wpstack_connect_activity_log';
		$now			= time();
		$old			= $now - 60 * 60 * 24;

		$check_duplicate = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT `alid` FROM %i
					WHERE `id` = %s
						AND `severity` = %s
						AND `modified_date` = %s
						AND `user_id` = %d
						AND `user_name` = %s
						AND `user_role` = %s
						AND `user_email` = %s
						AND `user_avatar` = %s
						AND `user_bio` = %s
						AND `ip_address` = %s
						AND `object_type` = %s
						AND `action` = %s
						AND `object_id` = %s
						AND `description` = %s
				',
				$table_name,
				$id,
				$severity,
				$modified_date,
                $current_user_id,
                $current_user_name,
                $current_user_role,
                $current_user_email,
                $current_user_avatar,
				$current_user_bio,
                $ip_address,
                $object_type,
                $action,
                $object_id,
                $description
			)
		);
		if ( $check_duplicate ) {
			return;
		}

		$log_data	= wpstack_log_data_format( $id, $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $object_id, $description );
		$wpdb->insert( $table_name, $log_data );	
		$subscription = get_option( 'wpstack_connect_website_subscription' );
		if ( isset( $subscription ) && 'premium' === $subscription ) {
			wpstack_send_log( 'login-log', $log_data );
		}
		
		$wpdb->get_results( 
			$wpdb->prepare(
				'DELETE FROM %i WHERE `id` = %d AND `modified_date` < %s',
				$table_name,
				$id,
				$old
			)
		);
	}
}

if ( ! function_exists( 'wpstack_get_activity' ) ) {

	function wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description ) {
		$modified_date     = time();
		$user              = wp_get_current_user();
		$current_user_id   = $user->ID;
		$current_user_name = $user->user_login;
		global $wp_roles;
		$role_name = array();
		if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
			foreach ( $user->roles as $user_role ) {
				$role_name[] = $wp_roles->role_names[ $user_role ];
			}
			$current_user_role = implode( ', ', $role_name );
		}
		$current_user_email  = $user->user_email;
		$current_user_avatar = get_avatar_url( $user );
		$current_user_bio    = $user->description;
		$ip_address          = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		if ( ! empty( $current_user_name ) && ! empty( $current_user_role ) ) {
			$log_data = wpstack_log_data_format( $id, $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $object_id, $description );
			$endpoint = 'log';
			if ( 'Logged Out' == $action ) {
				$endpoint = 'login-log';
			}

			wpstack_send_log( $endpoint, $log_data );
		}
	}
}

if ( ! function_exists( 'wpstack_log_data_format' ) ) {
	function wpstack_log_data_format( $id, $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $object_id, $description ) {
		return array(
			'id'            => $id,
			'severity'      => $severity,
			'modified_date' => $modified_date,
			'user_id'       => $current_user_id,
			'user_name'     => $current_user_name,
			'user_role'     => $current_user_role,
			'user_email'    => $current_user_email,
			'user_avatar'   => $current_user_avatar,
			'user_bio'      => $current_user_bio,
			'ip_address'    => $ip_address,
			'object_type'   => $object_type,
			'action'        => $action,
			'object_id'     => $object_id,
			'description'   => $description,
		);
	}
}

if ( ! function_exists( 'wpstack_before_edit_post' ) ) {

	function wpstack_before_edit_post( $post_id ) {
		global $old_post_data;
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		};
		$prev_post_data = get_post( $post_id );
		$post_type      = $prev_post_data->post_type;
		$post_tax       = array();
		$old_data       = array( 'link' => get_permalink( $post_id ) );
		if ( '' != $post_type && 'nav_menu_item' != $post_type ) {
			$taxonomy_names = get_object_taxonomies( $post_type );
			if ( is_array( $taxonomy_names ) && ! empty( $taxonomy_names ) ) {
				foreach ( $taxonomy_names as $taxonomy_name ) {
					$post_cats     = wp_get_post_terms( $post_id, $taxonomy_name );
					$post_cats_ids = array();
					foreach ( $post_cats as $post_cat ) {
						$post_cats_ids[] = $post_cat->term_id;
					}
					if ( is_array( $post_cats_ids ) && ! empty( $post_cats_ids ) ) {
						$post_tax[ $taxonomy_name ] = $post_cats_ids;
					}
				}
			}
		}
		$old_post_data = array(
			'post_data' => $prev_post_data,
			'post_meta' => get_post_custom( $post_id ),
			'post_tax'  => $post_tax,
			'old_data'  => $old_data,
		);
	}
}

if ( ! function_exists( 'wpstack_get_post_transition' ) ) {

	function wpstack_get_post_transition( $post_id, $post ) {
		global $old_post_data;
		$old_data             = isset( $old_post_data['old_data'] ) ? $old_post_data['old_data'] : array();
		$old_post_data_detail = isset( $old_post_data['post_data'] ) ? $old_post_data['post_data'] : '';

		if ( isset( $old_post_data_detail ) && '' != $old_post_data_detail ) {
			$old_status = $old_post_data_detail->post_status;
		}

		$new_status  = $post->post_status;
		$old_status  = isset( $old_status ) ? $old_status : '';
		$new_status  = isset( $new_status ) ? $new_status : '';
		$object_type = ucwords( $post->post_type );

		if ( 'nav_menu_item' === get_post_type( $post ) || 'wpcf7_contact_form' === get_post_type( $post ) || wp_is_post_revision( $post ) || 'customize_changeset' === get_post_type( $post ) || 'wp_global_styles' === get_post_type( $post ) ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'auto-draft' === $new_status || ( 'new' === $old_status && 'inherit' === $new_status ) ) {
			return;
		}

		if ( $new_status === $old_status ) {
			// if ( wpstack_check_draft_resave( $old_post_data_detail, $post ) ) {
			// return;
			// }

			// $id				= 2024;
			// $severity		= 'Low';
			// $action			= 'Modified';
			// $description	= 'Modified the post ' . $post->post_title;
		} else {
			if ( 'trash' === $old_status ) {
				$id          = 2034;
				$severity    = 'Low';
				$action      = 'Restored';
				$description = 'Restored the post ' . $post->post_title . ' from trash';
			} elseif ( 'trash' === $new_status ) {
				$id          = 2013;
				$severity    = 'Medium';
				$action      = 'Trashed';
				$description = 'Moved the post ' . $post->post_title . ' to trash';
			} elseif ( 'publish' === $new_status ) {
				$id          = 2004;
				$severity    = 'Low';
				$action      = 'Published';
				$description = 'Published the post ' . $post->post_title;
			} elseif ( 'draft' === $new_status ) {
				if ( 'auto-draft' === $old_status ) {
					$id          = 2005;
					$severity    = 'Informational';
					$action      = 'Created';
					$description = 'Created the post ' . $post->post_title;
				} else {
					$id          = 2003;
					$severity    = 'Medium';
					$action      = 'Modified';
					$description = 'Changed the status of the post ' . $post->post_title;
				}
			} elseif ( 'future' === $new_status ) {
				$id          = 2014;
				$severity    = 'Low';
				$action      = 'Scheduled';
				$description = 'Scheduled the post ' . $post->post_title . ' for publishing';
			} elseif ( 'pending' === $new_status ) {
				$id          = 2015;
				$severity    = 'Informational';
				$action      = 'Submitted';
				$description = 'Submitted the post ' . $post->post_title . ' for review';
			} else {
				$id          = 2003;
				$severity    = 'Medium';
				$action      = 'Modified';
				$description = 'Changed the status of the post ' . $post->post_title;
			}
		}

		if ( isset( $id ) && isset( $severity ) && isset( $object_type ) && isset( $action ) && isset( $post_id ) && isset( $description ) ) {
			wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
		}

		if ( 'auto-draft' !== $old_status ) {
			if ( 'trash' === $old_status ) {
				return;
			}

			$old_visibility = '';
			$new_visibility = '';

			if ( $old_post_data_detail->post_password ) {
				$old_visibility = 'Password Protected';
			} elseif ( 'private' === $old_status ) {
				$old_visibility = 'Private';
			} else {
				$old_visibility = 'Public';
			}

			if ( $post->post_password ) {
				$new_visibility = 'Password Protected';
			} elseif ( 'private' === $new_status ) {
				$new_visibility = 'Private';
			} else {
				$new_visibility = 'Public';
			}

			if ( $old_visibility && $new_visibility && ( $old_visibility !== $new_visibility ) ) {
				$id          = 2064;
				$severity    = 'Low';
				$action      = 'Modified';
				$description = 'Changed the visibility of the post ' . $post->post_title . ' to ' . $new_visibility;
				wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			}

			// if ( $old_post_data_detail->post_content !== $post->post_content ) {
			// $id             = 2074;
			// $severity       = 'Low';
			// $action         = 'Modified';
			// $description    = 'Modified the content of the post ' . $post->post_title;
			// wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			// }

			if ( $old_post_data_detail->comment_status !== $post->comment_status ) {
				$id          = 2084;
				$severity    = 'Low';
				$action      = 'Modified';
				$description = 'Enabled / disabled comments in the post ' . $post->post_title;
				wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			}

			if ( $old_post_data_detail->ping_status !== $post->ping_status ) {
				$id          = 2094;
				$severity    = 'Low';
				$action      = 'Modified';
				$description = 'Enabled / disabled trackbacks in the post ' . $post->post_title;
				wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			}

			$old_link = isset( $old_data['link'] ) ? $old_data['link'] : '';
			$new_link = get_permalink( $post_id );

			if ( in_array( $new_status, array( 'draft', 'pending' ), true ) ) {
				$old_link = $old_post_data_detail->post_name;
				$new_link = $post->post_name;
			}

			// if ( $old_link !== $new_link ) {
			// $id             = 2045;
			// $severity       = 'Informational';
			// $action         = 'Modified';
			// $description    = 'Changed the URL of the post ' . $post->post_title;
			// wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			// }

			if ( $old_post_data_detail->post_author !== $post->post_author ) {
				$id          = 2055;
				$severity    = 'Informational';
				$action      = 'Modified';
				$description = 'Changed the author of the post ' . $post->post_title;
				wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			}

			// $old_date	= strtotime( $old_post_data_detail->post_date );
			// $new_date	= strtotime( $post->post_date );

			// if ( $old_date !== $new_date ) {
			// if ( 'pending' === $old_status ) {
			// return;
			// }

			// if ( wpstack_check_draft_resave( $old_post_data_detail, $post ) ) {
			// return;
			// }

			// $id             = 2065;
			// $severity       = 'Informational';
			// $action         = 'Modified';
			// $description    = 'Changed the date of the post ' . $post->post_title;
			// wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			// }

			if ( $old_post_data_detail->post_title !== $post->post_title ) {
				$id          = 2095;
				$severity    = 'Informational';
				$action      = 'Modified';
				$description = 'Changed title of the post from ' . $old_post_data_detail->post_title . ' to ' . $post->post_title;
				wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			}

			$old_excerpt = $old_post_data_detail->post_excerpt;
			$new_excerpt = $post->post_excerpt;

			if ( $old_excerpt !== $new_excerpt ) {
				if ( empty( $old_excerpt ) && ! empty( $new_excerpt ) ) {
					$id          = 2105;
					$severity    = 'Informational';
					$action      = 'Modified';
					$description = 'Added the excerpt of the post ' . $post->post_title;
				} elseif ( ! empty( $old_excerpt ) && empty( $new_excerpt ) ) {
					$id          = 2125;
					$severity    = 'Informational';
					$action      = 'Modified';
					$description = 'Removed the excerpt of the post ' . $post->post_title;
				} else {
					$id          = 2115;
					$severity    = 'Informational';
					$action      = 'Modified';
					$description = 'Updated the excerpt of the post ' . $post->post_title;
				}

				wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			}
		}

		wpstack_api_content_calendar( $post_id );
	}
}

if ( ! function_exists( 'wpstack_check_draft_resave' ) ) {

	function wpstack_check_draft_resave( $oldpost, $newpost ) {
		if ( 'draft' === $oldpost->post_status
		&& $oldpost->post_status === $newpost->post_status
		&& $oldpost->post_date_gmt === $newpost->post_date_gmt
		&& preg_match( '/^[0\-\ \:]+$/', $oldpost->post_date_gmt ) ) {
			return true;
		}
	}
}

if ( ! function_exists( 'wpstack_delete_post' ) ) {

	function wpstack_delete_post( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( 'attachment' === get_post_type( $post ) ) {
			return;
		}
		$id       = 2023;
		$severity = 'Medium';
		$status   = $post->post_status;
		$action   = $status;
		if ( 'trash' === $status ) {
			$action      = 'Deleted';
			$object_type = ucwords( $post->post_type );
			$description = 'Permanently deleted the post ' . $post->post_title;
			wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			wpstack_api_content_calendar( $post_id );
		}
	}
}

if ( ! function_exists( 'wpstack_post_taxonomies_status' ) ) {
	function wpstack_post_taxonomies_status( $post_id, $terms, $tt_ids, $taxonomy ) {
		global $old_post_data;
		$post = get_post( $post_id );
		if ( is_wp_error( $post ) ) {
			return;
		}
		if ( 'auto-draft' === $post->post_status ) {
			return;
		}
		if ( 'post_tag' === $taxonomy ) {
			$old_tags   = isset( $old_post_data['post_tax']['post_tag'] ) ? $old_post_data['post_tax']['post_tag'] : array();
			$new_tags   = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
			$added_tags = array_diff( (array) $new_tags, (array) $old_tags );
			if ( ! empty( $added_tags ) ) {
				$post        = get_post( $post_id );
				$id          = 2025;
				$severity    = 'Informational';
				$object_type = 'Post';
				$action      = 'Modified';
				$tags        = array();
				foreach ( $added_tags as $tag ) {
					$termname = get_term_by( 'id', $tag, 'post_tag' );
					array_push( $tags, $termname->name );
				}
				$added       = implode( ', ', $tags );
				$description = 'Added tags ' . $added . ' to ' . $post->post_title;
				wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			}
			$removed_tags = array_diff( (array) $old_tags, (array) $new_tags );
			if ( ! empty( $removed_tags ) ) {
				$post        = get_post( $post_id );
				$id          = 2035;
				$severity    = 'Informational';
				$object_type = 'Post';
				$action      = 'Modified';
				$tags        = array();
				foreach ( $removed_tags as $tag ) {
					$termname = get_term_by( 'id', $tag, 'post_tag' );
					array_push( $tags, $termname->name );
				}
				$removed     = implode( ', ', $tags );
				$description = 'Removed tags ' . $removed . ' from ' . $post->post_title;
				wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			}
		} elseif ( 'category' === $taxonomy ) {
			$old_cats   = isset( $old_post_data['post_tax']['category'] ) ? $old_post_data['post_tax']['category'] : array();
			$new_cats   = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );
			$added_cats = array_diff( (array) $new_cats, (array) $old_cats );
			if ( ! empty( $added_cats ) ) {
				$post        = get_post( $post_id );
				$id          = 2044;
				$severity    = 'Low';
				$object_type = 'Post';
				$action      = 'Modified';
				$cats        = array();
				foreach ( $added_cats as $cat ) {
					$termname = get_term_by( 'id', $cat, 'category' );
					array_push( $cats, $termname->name );
				}
				$added       = implode( ', ', $cats );
				$description = 'Added categories ' . $added . ' to ' . $post->post_title;
				wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			}
			$removed_cats = array_diff( (array) $old_cats, (array) $new_cats );
			if ( ! empty( $removed_cats ) ) {
				$post        = get_post( $post_id );
				$id          = 2054;
				$severity    = 'Low';
				$object_type = 'Post';
				$action      = 'Modified';
				$cats        = array();
				foreach ( $removed_cats as $cat ) {
					$termname = get_term_by( 'id', $cat, 'category' );
					array_push( $cats, $termname->name );
				}
				$removed     = implode( ', ', $cats );
				$description = 'Removed categories ' . $removed . ' from ' . $post->post_title;
				wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
			}
		}
		wpstack_api_content_calendar( $post_id );
	}
}

if ( ! function_exists( 'wpstack_post_sticky_stuck' ) ) {

	function wpstack_post_sticky_stuck( $post_id ) {
		$post = get_post( $post_id );

		if ( is_wp_error( $post ) ) {
			return;
		}

		$id          = 2075;
		$severity    = 'Informational';
		$object_type = 'Post';
		$action      = 'Modified';
		$description = 'Set the post ' . $post->post_title . ' as sticky';
		wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
		wpstack_api_content_calendar( $post_id );
	}
}

if ( ! function_exists( 'wpstack_post_sticky_unstuck' ) ) {

	function wpstack_post_sticky_unstuck( $post_id ) {
		$post = get_post( $post_id );

		if ( is_wp_error( $post ) ) {
			return;
		}

		$id          = 2085;
		$severity    = 'Informational';
		$object_type = 'Post';
		$action      = 'Modified';
		$description = 'Removed the post ' . $post->post_title . ' from Sticky';
		wpstack_get_activity( $id, $severity, $object_type, $action, $post_id, $description );
		wpstack_api_content_calendar( $post_id );
	}
}

if ( ! function_exists( 'wpstack_create_tag' ) ) {

	function wpstack_create_tag( $term_id ) {
		$tag         = get_tag( $term_id );
		$id          = 3005;
		$severity    = 'Informational';
		$object_type = 'Tag';
		$action      = 'Created';
		$description = 'Created new tag ' . $tag->name;
		wpstack_get_activity( $id, $severity, $object_type, $action, $term_id, $description );
	}
}

if ( ! function_exists( 'wpstack_create_category' ) ) {

	function wpstack_create_category( $term_id ) {
		$category    = get_category( $term_id );
		$id          = 4003;
		$severity    = 'Medium';
		$object_type = 'Category';
		$action      = 'Created';
		$description = 'Created new category ' . $category->name;
		wpstack_get_activity( $id, $severity, $object_type, $action, $term_id, $description );
	}
}

if ( ! function_exists( 'wpstack_update_taxonomies' ) ) {

	function wpstack_update_taxonomies( $data, $term_id, $taxonomy, $args ) {
		$new_name   = isset( $data['name'] ) ? $data['name'] : false;
		$new_slug   = isset( $data['slug'] ) ? $data['slug'] : false;
		$new_desc   = isset( $args['description'] ) ? $args['description'] : false;
		$new_parent = isset( $args['parent'] ) ? $args['parent'] : false;

		$term       = get_term( $term_id, $taxonomy );
		$old_name   = $term->name;
		$old_slug   = $term->slug;
		$old_desc   = $term->description;
		$old_parent = $term->parent;

		if ( 'post_tag' === $taxonomy ) {
			$object_type = 'Tag';
			if ( $old_name !== $new_name ) {
				$id          = 3015;
				$severity    = 'Informational';
				$action      = 'Modified';
				$description = 'Renamed the tag from ' . $old_name . ' to ' . $new_name;
				wpstack_get_activity( $id, $severity, $object_type, $action, $term_id, $description );
			}

			if ( $old_slug !== $new_slug ) {
				$id          = 3025;
				$severity    = 'Informational';
				$action      = 'Modified';
				$description = 'Changed the slug of tag from ' . $old_slug . ' to ' . $new_slug;
				wpstack_get_activity( $id, $severity, $object_type, $action, $term_id, $description );
			}

			if ( $old_desc !== $new_desc ) {
				$id          = 3035;
				$severity    = 'Informational';
				$action      = 'Modified';
				$description = 'Changed the description of tag from ' . $old_desc . ' to ' . $new_desc;
				wpstack_get_activity( $id, $severity, $object_type, $action, $term_id, $description );
			}
		} elseif ( 'category' === $taxonomy ) {
			$object_type = 'Category';
			if ( $old_name !== $new_name ) {
				$id          = 4004;
				$severity    = 'Low';
				$action      = 'Modified';
				$description = 'Renamed the category from ' . $old_name . ' to ' . $new_name;
				wpstack_get_activity( $id, $severity, $object_type, $action, $term_id, $description );
			}

			if ( $old_slug !== $new_slug ) {
				$id          = 4014;
				$severity    = 'Low';
				$action      = 'Modified';
				$description = 'Changed the slug of category from ' . $old_slug . ' to ' . $new_slug;
				wpstack_get_activity( $id, $severity, $object_type, $action, $term_id, $description );
			}

			if ( 0 !== $old_parent ) {
				$old_parent_obj  = get_category( $old_parent );
				$old_parent_name = empty( $old_parent_obj ) ? 'no parent' : $old_parent_obj->name;
			} else {
				$old_parent_name = 'no parent';
			}
			if ( 0 !== $new_parent ) {
				$new_parent_obj  = get_category( $new_parent );
				$new_parent_name = empty( $new_parent_obj ) ? 'no parent' : $new_parent_obj->name;
			} else {
				$new_parent_name = 'no parent';
			}

			if ( $old_parent_name !== $new_parent_name ) {
				$id          = 4024;
				$severity    = 'Low';
				$action      = 'Modified';
				$description = 'Changed the parent of category from ' . $old_parent_name . ' to ' . $new_parent_name;
				wpstack_get_activity( $id, $severity, $object_type, $action, $term_id, $description );
			}
		}

		return $data;
	}
}

if ( ! function_exists( 'wpstack_delete_taxonomies' ) ) {

	function wpstack_delete_taxonomies( $term_id, $taxonomy ) {
		if ( 'post_tag' === $taxonomy ) {
			$tag         = get_tag( $term_id );
			$id          = 3004;
			$severity    = 'Low';
			$object_type = 'Tag';
			$action      = 'Deleted';
			$description = 'Deleted tag ' . $tag->name;
			wpstack_get_activity( $id, $severity, $object_type, $action, $term_id, $description );
		} elseif ( 'category' === $taxonomy ) {
			$category    = get_category( $term_id );
			$id          = 4013;
			$severity    = 'Medium';
			$object_type = 'Category';
			$action      = 'Deleted';
			$description = 'Deleted category ' . $category->name;
			wpstack_get_activity( $id, $severity, $object_type, $action, $term_id, $description );
		}
	}
}

if ( ! function_exists( 'wpstack_create_nav_menu' ) ) {

	function wpstack_create_nav_menu( $menu_id ) {
		$menu_object = wp_get_nav_menu_object( $menu_id );
		$id          = 5004;
		$severity    = 'Low';
		$object_type = 'Menu';
		$action      = 'Created';
		$description = 'Created menu' . $menu_object->name;
		wpstack_get_activity( $id, $severity, $object_type, $action, $menu_id, $description );
	}
}

if ( ! function_exists( 'wpstack_update_nav_menu' ) ) {

    function wpstack_update_nav_menu( $menu_id ) {
        if ( ! isset( $_REQUEST['menu'] ) || ! isset( $_REQUEST['action'] ) ) {
            return;
        }

        $action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) );

        if ( 'locations' !== $action && 'update' !== $action ) {
            return;
        }

        $menu_id = intval( $_REQUEST['menu'] );

        if ( ! is_nav_menu( $menu_id ) ) {
            return;
        }

        $menu_object = wp_get_nav_menu_object( $menu_id );
        $id          = 5014;
        $severity    = 'Low';
        $object_type = 'Menu';
        $action      = 'Modified';
        $description = sprintf(
            esc_html__( 'Modified menu %s' ),
            sanitize_text_field( $menu_object->name )
        );

        wpstack_get_activity( $id, $severity, $object_type, $action, $menu_id, $description );
    }
}

if ( ! function_exists( 'wpstack_delete_nav_menu' ) ) {

	function wpstack_delete_nav_menu( $term_id, $tt_id, $deleted_term ) {
		$id          = 5003;
		$severity    = 'Medium';
		$object_type = 'Menu';
		$action      = 'Deleted';
		$description = 'Deleted menu ' . $deleted_term->name;
		wpstack_get_activity( $id, $severity, $object_type, $action, $term_id, $description );
	}
}

if ( ! function_exists( 'wpstack_widget_status' ) ) {

	function wpstack_widget_status() {
		$post_array = [];
		$post['widget-id'] = isset($_POST['widget-id']) ? sanitize_text_field( $_POST['widget-id'] ) : null;
		$post['savewidgets'] = isset($_POST['savewidgets']) ? sanitize_text_field( $_POST['savewidgets'] ) : null;
		$post['add_new'] = isset($_POST['add_new']) ? sanitize_text_field( $_POST['add_new'] ) : null;
		$post['sidebar'] = isset($_POST['sidebar']) ? sanitize_text_field( $_POST['sidebar'] ) : null;
		$post['id_base'] = isset($_POST['id_base']) ? sanitize_text_field( $_POST['id_base'] ) : null;
		$post['delete_widget'] = isset($_POST['delete_widget']) ? filter_input( INPUT_POST, 'delete_widget', FILTER_VALIDATE_INT ) : null;
		$post['multi_number'] = isset($_POST['multi_number']) ? filter_input( INPUT_POST, 'multi_number', FILTER_VALIDATE_INT ) : null;
		$post['widget_number'] = isset($_POST['widget_number']) ? filter_input( INPUT_POST, 'widget_number', FILTER_VALIDATE_INT) : null;

		$get['widget-id'] = isset($_GET['widget-id']) ? sanitize_text_field( $_GET['widget-id'] ) : null;
		$get['savewidgets'] = isset($_GET['savewidgets']) ? sanitize_text_field( $_GET['savewidgets'] ) : null;
		$get['add_new'] = isset($_GET['add_new']) ? sanitize_text_field( $_GET['add_new'] ) : null;
		$get['sidebar'] = isset($_GET['sidebar']) ? sanitize_text_field( $_GET['sidebar'] ) : null;
		$get['id_base'] = isset($_GET['id_base']) ? sanitize_text_field( $_GET['id_base'] ) : null;
		$get['delete_widget'] = isset($_GET['delete_widget']) ? filter_input( INPUT_GET, 'delete_widget', FILTER_VALIDATE_INT ) : null;
		$get['multi_number'] = isset($_GET['multi_number']) ? filter_input( INPUT_GET, 'multi_number', FILTER_VALIDATE_INT ) : null;
		$get['widget_number'] = isset($_GET['widget_number']) ? filter_input( INPUT_GET, 'widget_number', FILTER_VALIDATE_INT) : null;

		$post_array = array_merge($post, $get);

		if ( ! isset( $post_array ) || ! isset( $post_array['widget-id']) || empty( $post_array['widget-id']) ) {
			return;
		}

		if ( ! isset( $post_array['savewidgets'] ) || false === check_ajax_referer( 'save-sidebar-widgets', 'savewidgets', false ) ) {
			return;
		}

		global $wp_registered_sidebars;
		$can_check_sidebar = ! empty( $wp_registered_sidebars );
		$object_type       = 'Widget';
		$object_id         = '';

		switch ( true ) {
			case isset( $post_array['add_new'] ) && 'multi' === $post_array['add_new']:
				$sidebar = isset( $post_array['sidebar'] ) ? $post_array['sidebar'] : null;

				if ( $can_check_sidebar && preg_match( '/^sidebar-/', $sidebar ) ) {
					$sidebar = $wp_registered_sidebars[ $sidebar ]['name'];
				}

				$id          = 6003;
				$severity    = 'Medium';
				$action      = 'Added';
				$description = 'Added new widget ' . $post_array['id_base'] . ' to ' . $sidebar;
				wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
				break;

			case isset( $post_array['delete_widget'] ) && 1 === intval( $post_array['delete_widget'] ):
				$sidebar = isset( $post_array['sidebar'] ) ? $post_array['sidebar'] : null;

				if ( $can_check_sidebar && preg_match( '/^sidebar-/', $sidebar ) ) {
					$sidebar = $wp_registered_sidebars[ $sidebar ]['name'];
				}

				$id          = 6013;
				$severity    = 'Medium';
				$action      = 'Deleted';
				$description = 'Deleted widget ' . $post_array['id_base'] . ' from ' . $sidebar;
				wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
				break;

			case isset( $post_array['id_base'] ) && ! empty( $post_array['id_base'] ):
				$widget_id = 0;

				if ( ! empty( $post_array['multi_number'] ) ) {
					$widget_id = intval( $post_array['multi_number'] );
				} elseif ( ! empty( $post_array['widget_number'] ) ) {
					$widget_id = intval( $post_array['widget_number'] );
				}

				if ( empty( $widget_id ) ) {
					return;
				}

				$widget_name = $post_array['id_base'];
				$sidebar     = $post_array['sidebar'];
				$widget_data = isset( $_POST[ "widget-$widget_name" ][ $widget_id ] )
					? filter_input($_POST[ "widget-$widget_name" ], $widget_id, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY)
					: null;

				if ( empty( $widget_data ) ) {
					return;
				}

				$widget_db_data = get_option( 'widget_' . $widget_name );
				if ( empty( $widget_db_data[ $widget_id ] ) ) {
					return;
				}

				foreach ( $widget_data as $k => $v ) {
					if ( 'on' === $v ) {
						$widget_data[ $k ] = 1;
					}
				}

				$diff  = array_diff_assoc( $widget_data, $widget_db_data[ $widget_id ] );
				$count = count( $diff );
				if ( $count > 0 ) {
					if ( $can_check_sidebar && preg_match( '/^sidebar-/', $sidebar ) ) {
						$sidebar = $wp_registered_sidebars[ $sidebar ]['name'];
					}

					$id          = 6002;
					$severity    = 'High';
					$object_type = 'Widget';
					$action      = 'Modified';
					$description = 'Modified widget ' . $widget_name;
					wpstack_get_activity( $id, $severity, $object_type, $action, $widget_id, $description );
				}
				break;

			default:
				break;
		}
	}
}

if ( ! function_exists( 'wpstack_delete_widget' ) ) {

	function wpstack_delete_widget( $widget_id, $sidebar_id, $id_base ) {
		$id          = 6013;
		$severity    = 'Medium';
		$object_type = 'Widget';
		$action      = 'Deleted';
		$description = 'Deleted widget ' . $id_base . ' from ' . $sidebar_id;
		wpstack_get_activity( $id, $severity, $object_type, $action, $widget_id, $description );
	}
}

if ( ! function_exists( 'wpstack_update_widget' ) ) {

    function wpstack_update_widget( $instance, $new_instance, $old_instance, WP_Widget $widget ) {
        if ( empty( $_REQUEST['sidebar'] ) ) {
            return $instance;
        }

        $widget_id   = '';
        $id          = 6002;
        $severity    = 'High';
        $object_type = 'Widget';
        $action      = 'Modified';
        $description = sprintf(
            esc_html__( 'Modified widget %s', ),
            sanitize_text_field( $widget->name )
        );

        wpstack_get_activity( $id, $severity, $object_type, $action, $widget_id, $description );

        return $instance;
    }
}

if ( ! function_exists( 'wpstack_user_register' ) ) {

	function wpstack_user_register( $user_id ) {
		$id           = 1001;
		$severity     = 'Critical';
		$object_type  = 'User';
		$action       = 'Created';
		$new_userdata = get_userdata( $user_id );
		$description  = 'A new user was created with username ' . $new_userdata->user_login;
		wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
	}
}

if ( ! function_exists( 'wpstack_delete_user' ) ) {

	function wpstack_delete_user( $user_id ) {
		$id           = 1022;
		$severity     = 'High';
		$object_type  = 'User';
		$action       = 'Deleted';
		$new_userdata = get_userdata( $user_id );
		$description  = 'Deleted a user with username ' . $new_userdata->user_login;
		wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
	}
}

if ( ! function_exists( 'wpstack_user_role_change' ) ) {

	function wpstack_user_role_change( $user_id ) {
		$id           = 1011;
		$severity     = 'Critical';
		$object_type  = 'User';
		$action       = 'Modified';
		$new_userdata = get_userdata( $user_id );
		$description  = 'Changed the role of user ' . $new_userdata->user_login;
		wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
	}
}

if ( ! function_exists( 'wpstack_user_profile_update' ) ) {

	function wpstack_user_profile_update( $user_id, $old_userdata ) {
		$object_type  = 'User';
		$action       = 'Modified';
		$new_userdata = get_userdata( $user_id );

		if ( $old_userdata->user_pass !== $new_userdata->user_pass ) {
			$user_now = get_current_user_id();
			$severity = 'High';

			if ( $user_now === $user_id ) {
				$id          = 1002;
				$description = 'Changed the password';
			} else {
				$id          = 1012;
				$description = 'Changed the password of user ' . $new_userdata->user_login;
			}

			wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
		}

		if ( $old_userdata->user_email !== $new_userdata->user_email ) {
			$user_now = get_current_user_id();
			$severity = 'Medium';

			if ( $user_now === $user_id ) {
				$id          = 1023;
				$description = 'Changed the email address';
			} else {
				$id          = 1033;
				$description = 'Changed the email address of user ' . $new_userdata->user_login;
			}

			wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
		}

		if ( $old_userdata->display_name !== $new_userdata->display_name ) {
			$id          = 1024;
			$severity    = 'Low';
			$description = 'Changed the display name of the user ' . $new_userdata->user_login . ' to ' . $new_userdata->display_name;

			wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
		}

		if ( $old_userdata->user_url !== $new_userdata->user_url ) {
			$id          = 1063;
			$severity    = 'Medium';
			$description = 'Changed the website URL of user ' . $new_userdata->user_login;

			wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
		}

	}
}

if ( ! function_exists( 'wpstack_user_log_in' ) ) {

	function wpstack_user_log_in( $user_login, $user ) {
		$id                = 1004;
		$severity          = 'Low';
		$modified_date     = time();
		$current_user_id   = $user->ID;
		$current_user_name = $user->user_login;
		global $wp_roles;
		$role_name = array();
		if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
			foreach ( $user->roles as $user_role ) {
				$role_name[] = $wp_roles->role_names[ $user_role ];
			}
			$current_user_role = implode( ', ', $role_name );
		}
		$current_user_email  = $user->user_email;
		$current_user_avatar = get_avatar_url( $user );
		$current_user_bio    = $user->description;
		$ip_address          = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$object_type         = 'User';
		$action              = 'Logged In';
		$description         = 'Successfully logged in';
		wpstack_add_login_success_log( $id, $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $current_user_id, $description );
		wpstack_send_client_web_data( true );
	}
}

if ( ! function_exists( 'wpstack_user_log_in_failed' ) ) {

	function wpstack_user_log_in_failed( $username ) {
		$subscription = get_option( 'wpstack_connect_website_subscription' );
		if ( isset( $subscription ) && 'premium' === $subscription ) {
			$id                  = 1003;
			$severity            = 'Medium';
			$modified_date       = time();
			$current_user_id     = 0;
			$current_user_name   = $username;
			$current_user_role   = '';
			$current_user_email  = '';
			$current_user_avatar = '';
			$current_user_bio    = '';
			$ip_address          = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
			$object_type         = 'Guest';
			$action              = 'Failed Login';
			$description         = 'Failed Login';
			$user                = get_user_by( 'login', $username );
			if ( empty( $user ) ) {
				$user = get_user_by( 'email', $username );
			}
	
			if ( $user ) {
				$current_user_id   = $user->ID;
				$current_user_name = $user->user_login;
				$object_type       = 'User';
				global $wp_roles;
				$role_name = array();
				if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
					foreach ( $user->roles as $user_role ) {
						$role_name[] = $wp_roles->role_names[ $user_role ];
					}
					$current_user_role = implode( ', ', $role_name );
				}
				$current_user_email  = $user->user_email;
				$current_user_avatar = get_avatar_url( $user );
				$current_user_bio    = $user->description;
			}
	
			wpstack_add_failed_login_log( $id, $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $current_user_id, $description );
	
			if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
				wpstack_track_failed_login( $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $current_user_id, $description );
			}
		}
	}
}

if ( ! function_exists( 'wpstack_track_failed_login' ) ) {
	
	function wpstack_track_failed_login( $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $object_id, $description ) {
		$login_attempts	= get_option( 'wpstack_connect_login_attempts', array() );
		
		if ( ! isset( $login_attempts[ $ip_address ] ) ) {
			$login_attempts[ $ip_address ] = array(
				'login_attemps'		=> array(
					'count' 		=> 0,
					'last_attempt' 	=> $modified_date,
				),
				'invalid_username' 	=> array(
					'count' 		=> 0,
					'last_attempt' 	=> $modified_date,
				)
			);
		}

		$login_last_attempt		 	= $login_attempts[ $ip_address ]['login_attemps']['last_attempt'];
		$invalid_last_attempt		= $login_attempts[ $ip_address ]['invalid_username']['last_attempt'];
		$login_since_last_attempt	= $modified_date - $login_last_attempt;
		$invalid_since_last_attempt	= $modified_date - $invalid_last_attempt;
		$blocked_settings			= get_option( 'wpstack_connect_blocked_settings', array() );
		$max_login_attemps    		= 6;
		$time_login_attemps   		= 5;
		$max_invalid_attemps  		= 5;
		$time_invalid_attemps 		= 5;
		
		if ( isset( $blocked_settings['login_attemps'] ) ) {
			$max_login_attemps  = $blocked_settings['login_attemps']['max'];
			$time_login_attemps = $blocked_settings['login_attemps']['time'];
		}

		if ( isset( $blocked_settings['invalid_username'] ) ) {
			$max_invalid_attemps  = $blocked_settings['invalid_username']['max'];
			$time_invalid_attemps = $blocked_settings['invalid_username']['time'];
		}

		$time_login_attemps	  = $time_login_attemps * 60;
		$time_invalid_attemps = $time_invalid_attemps * 60;

		if ( 'User' === $object_type ) {
			if ( $time_login_attemps >= $login_since_last_attempt ) {
				$login_attempts[ $ip_address ]['login_attemps']['count']++;
				if ( $login_attempts[ $ip_address ]['login_attemps']['count'] >= $max_login_attemps ) {
					$login_attempts[ $ip_address ]['login_attemps']['count'] 	    = 0;
					$login_attempts[ $ip_address ]['login_attemps']['last_attempt'] = $modified_date;
					wpstack_block_ip( $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, 'Blocked', $object_id, 'Login Blocked' );
				}
			} else {
				$login_attempts[ $ip_address ]['login_attemps']['count']        = 1;
				$login_attempts[ $ip_address ]['login_attemps']['last_attempt'] = $modified_date;
			}
		} else {
			if ( $time_invalid_attemps >= $invalid_since_last_attempt ) {
				$login_attempts[ $ip_address ]['invalid_username']['count']++;
				if ( $login_attempts[ $ip_address ]['invalid_username']['count'] >= $max_invalid_attemps ) {
					$login_attempts[ $ip_address ]['invalid_username']['count'] 	   = 0;
					$login_attempts[ $ip_address ]['invalid_username']['last_attempt'] = $modified_date;
					wpstack_block_ip( $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, 'Blocked', $object_id, 'Login Blocked' );
				}
			} else {
				$login_attempts[ $ip_address ]['invalid_username']['count']        = 1;
				$login_attempts[ $ip_address ]['invalid_username']['last_attempt'] = $modified_date;
			}
		}

		update_option( 'wpstack_connect_login_attempts', $login_attempts );
		
	}
}

if ( ! function_exists( 'wpstack_block_ip' ) ) {
	
	function wpstack_block_ip( $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $object_id, $description ) {
		$blocked_ips = get_option( 'wpstack_connect_blocked_ips', array() );
		if ( ! in_array($ip_address, $blocked_ips)) {
			$blocked_ips[] = $ip_address;
			update_option( 'wpstack_connect_blocked_ips' , $blocked_ips );
			$id 		= 1073;
			$log_data 	= wpstack_log_data_format( $id, $severity, $modified_date, $current_user_id, $current_user_name, $current_user_role, $current_user_email, $current_user_avatar, $current_user_bio, $ip_address, $object_type, $action, $object_id, $description );	
			wpstack_send_log( 'login-block', $log_data );
		} 
	}
}

if ( ! function_exists( 'wpstack_user_log_out' ) ) {

	function wpstack_user_log_out() {
		$user = wp_get_current_user();
		if ( empty( $user ) || ! $user->exists() ) {
			return;
		}
		$id          = 1014;
		$severity    = 'Low';
		$object_type = 'User';
		$action      = 'Logged Out';
		$user_id     = $user->ID;
		$description = 'Successfully logged out';
		wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
	}
}

if ( ! function_exists( 'wpstack_add_user_to_blog' ) ) {

	function wpstack_add_user_to_blog( $user_id, $role, $blog_id ) {
		global $wp_roles;
		$user        = get_userdata( $user_id );
		$id          = 1043;
		$severity    = 'Medium';
		$object_type = 'User';
		$action      = 'Added';
		$blog_name   = get_blog_option( $blog_id, 'blogname' );
		$role_name   = $wp_roles->role_names[ $role ];
		$description = 'Added a network user with username ' . $user->user_login . ' to ' . $blog_name;
		wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
	}
}

if ( ! function_exists( 'wpstack_remove_user_from_blog' ) ) {

	function wpstack_remove_user_from_blog( $user_id, $blog_id ) {
		$user        = get_userdata( $user_id );
		$id          = 1053;
		$severity    = 'Medium';
		$object_type = 'User';
		$action      = 'Removed';
		$blog_name   = get_blog_option( $blog_id, 'blogname' );
		$description = 'Removed a network user with username ' . $user->user_login . ' from ' . $blog_name;
		wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
	}
}

if ( ! function_exists( 'wpstack_user_updated_meta' ) ) {

	function wpstack_user_updated_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		$user        = get_userdata( $object_id );
		$severity    = 'Informational';
		$object_type = 'User';
		$action      = 'Modified';
		switch ( $meta_key ) {
			case 'first_name':
				$id          = 1035;
				$description = 'Changed the first name of the user ' . $user->user_login . ' to ' . $meta_value;
				wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
				break;

			case 'last_name':
				$id          = 1045;
				$description = 'Changed the last name of the user ' . $user->user_login . ' to ' . $meta_value;
				wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
				break;

			case 'nickname':
				$id          = 1055;
				$description = 'Changed the nickname of the user ' . $user->user_login . ' to ' . $meta_value;
				wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
				break;

			default:
				break;
		}
	}
}

if ( ! function_exists( 'wpstack_user_sent_password_reset' ) ) {

	function wpstack_user_sent_password_reset( $user_login ) {
		$user        = get_user_by( 'login', $user_login );
		$id          = 1005;
		$severity    = 'Informational';
		$object_type = 'User';
		$action      = 'Submitted';
		$user_id     = $user->ID;
		$description = 'Sent a password reset request to the user ' . $user_login;
		wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
	}
}

// if ( ! function_exists( 'wpstack_user_opened_profile') ) {

// function wpstack_user_opened_profile( $user ) {
// if ( ! $user ) {
// return;
// }

// $id             = 1025;
// $severity       = 'Informational';
// $object_type    = 'User';
// $action         = 'Opened';
// $user_id        = $user->ID;
// $description    = 'Opened the profile page of user ' . $user->user_login;
// wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description);
// }
// }

if ( ! function_exists( 'wpstack_user_request_password_reset' ) ) {

	function wpstack_user_request_password_reset( $errors, $user_data = null ) {
		if ( is_null( $user_data ) || ! isset( $user_data->roles ) ) {
			return;
		}

		$id          = 1015;
		$severity    = 'Informational';
		$object_type = 'User';
		$action      = 'Submitted';
		$user_id     = $user_data->ID;
		$description = 'User with username ' . $user_data->user_login . ' requested a password reset';
		wpstack_get_activity( $id, $severity, $object_type, $action, $user_id, $description );
	}
}



/*
 * Get wp_core and settings status
 */
if ( ! function_exists( 'wpstack_core_updated' ) ) {

	function wpstack_core_updated( $wp_version ) {
		$id          = 9983;
		$severity    = 'Medium';
		$object_type = 'System';
		$action      = 'Updated';
		$object_id   = '';
		$description = 'Updated WordPress to ' . $wp_version;
		wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
		wpstack_send_sys_info();
	}
}

if ( ! function_exists( 'wpstack_updated_option' ) ) {

	function wpstack_updated_option( $option, $old_value, $value ) {
		$object_type = 'Setting';
		$action      = 'Modified';
		$object_id   = '';

		if ( 'users_can_register' === $option ) {
			$id          = 9901;
			$severity    = 'Critical';
			$description = 'Changed the option anyone can register';
		} elseif ( 'default_role' === $option ) {
			$id          = 9911;
			$severity    = 'Critical';
			$description = 'Changed the new user default role';
		} elseif ( 'admin_email' === $option ) {
			$id          = 9921;
			$severity    = 'Critical';
			$description = 'Changed the WordPress administrator notification email address';
		} elseif ( 'siteurl' === $option ) {
			$id          = 9931;
			$severity    = 'Critical';
			$description = 'Changed the WordPress address (URL)';
		} elseif ( 'home' === $option ) {
			$id          = 9941;
			$severity    = 'Critical';
			$description = 'Changed the site address (URL)';
		} elseif ( 'permalink_structure' === $option || 'category_base' === $option || 'tag_base' === $option ) {
			$id          = 9902;
			$severity    = 'High';
			$description = 'Changed the WordPress permalinks';
		} elseif ( 'show_on_front' === $option ) {
			$id          = 9912;
			$severity    = 'High';
			$description = 'Changed the "Your homepage displays" WordPress setting';
		} elseif ( 'page_on_front' === $option ) {
			$id          = 9922;
			$severity    = 'High';
			$description = 'Changed the homepage in the WordPress setting';
		} elseif ( 'page_for_posts' === $option ) {
			$id          = 9932;
			$severity    = 'High';
			$description = 'Changed the posts page in the WordPress settings';
		} elseif ( 'default_comment_status' === $option ) {
			$id          = 9903;
			$severity    = 'Medium';
			$description = 'Enabled / disabled comments on the website';
		} elseif ( 'require_name_email' === $option ) {
			$id          = 9913;
			$severity    = 'Medium';
			$description = 'Changed the setting: Comment author must fill out name and email';
		} elseif ( 'comment_registration' === $option ) {
			$id          = 9923;
			$severity    = 'Medium';
			$description = 'Changed the setting: Users must be logged in and registered to comment';
		} elseif ( 'comment_moderation' === $option ) {
			$id          = 9933;
			$severity    = 'Medium';
			$description = 'Changed the setting: Comments must be manually approved';
		} elseif ( 'timezone_string' === $option || 'gmt_offset' === $option ) {
			$id          = 9943;
			$severity    = 'Medium';
			$description = 'Changed the timezone in the WordPress settings';
		} elseif ( 'date_format' === $option ) {
			$id          = 9953;
			$severity    = 'Medium';
			$description = 'Changed the date format in the WordPress settings';
		} elseif ( 'time_format' === $option ) {
			$id          = 9963;
			$severity    = 'Medium';
			$description = 'Changed the time format in the WordPress settings';
		} elseif ( 'blogname' === $option ) {
			$id          = 9973;
			$severity    = 'Medium';
			$description = 'Changed the site title';
		} elseif ( 'WPLANG' === $option ) {
			$id          = 9993;
			$severity    = 'Medium';
			$description = 'Changed the site language';
		} elseif ( 'comment_whitelist' === $option || 'comment_previously_approved' === $option ) {
			$id          = 9904;
			$severity    = 'Low';
			$description = 'Changed the setting: Author must have previously approved comments for the comments to appear';
		} elseif ( 'comment_max_links' === $option ) {
			$id          = 9914;
			$severity    = 'Low';
			$description = 'Changed the minimum number of links that a comment must have to be held in the queue';
		} elseif ( 'blog_public' === $option ) {
			$id          = 9905;
			$severity    = 'Informational';
			$description = 'Changed the setting: Discourage search engines from indexing this site';
		} elseif ( 'close_comments_for_old_posts' === $option ) {
			$id          = 9915;
			$severity    = 'Informational';
			$description = 'Changed the setting: Automatically close comments after a number of days';
		} elseif ( 'close_comments_days_old' === $option ) {
			$id          = 9925;
			$severity    = 'Informational';
			$description = 'Changed the value of the setting: Automatically close comments after a number of days.';
		} elseif ( 'moderation_keys' === $option ) {
			$id          = 9935;
			$severity    = 'Informational';
			$description = 'Modified the list of keywords for comments moderation';
		} elseif ( 'blacklist_keys' === $option || 'disallowed_keys' === $option ) {
			$id          = 9945;
			$severity    = 'Informational';
			$description = 'Modified the list of keywords for comments blacklisting';
		} else {
			return;
		}

		wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
	}
}

/*
 * Get plugin and theme status
 */
if ( ! function_exists( 'wpstack_activated_plugin' ) ) {

	function wpstack_activated_plugin( $plugin ) {
		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin, true, false );
		$id          = 8002;
		$severity    = 'High';
		$object_type = 'Plugin';
		$action      = 'Activated';
		$object_id   = '';
		$description = 'Activated plugin ' . $plugin_data['Name'];
		wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
		wpstack_send_client_web_data();
	}
}

if ( ! function_exists( 'wpstack_deactivated_plugin' ) ) {

	function wpstack_deactivated_plugin( $plugin ) {
		$plugin_data 	= get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin, true, false );
		$id          	= 8012;
		$severity    	= 'High';
		$object_type 	= 'Plugin';
		$action      	= 'Deactivated';
		$object_id   	= '';
		$description 	= 'Deactivated plugin ' . $plugin_data['Name'];
		$active_plugins	= get_option( 'active_plugins' );
		if ( in_array( $plugin, $active_plugins ) ) {
			$key = array_search( $plugin, $active_plugins );
			if ( false !== $key ) {
				unset( $active_plugins[$key] );
			}

			update_option( 'active_plugins', $active_plugins );
		}

		wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
		wpstack_send_client_web_data();
	}
}

if ( ! function_exists( 'wpstack_plugin_install_or_update' ) ) {

	function wpstack_plugin_install_or_update( $upgrader, $extra ) {
		$object_type = 'Plugin';
		$object_id   = '';

		if ( ! isset( $extra['type'] ) || 'plugin' !== $extra['type'] ) {
			return;
		}

		if ( 'install' === $extra['action'] ) {
			$path = $upgrader->plugin_info();
			if ( ! $path ) {
				return;
			}
			$data        = get_plugin_data( $upgrader->skin->result['local_destination'] . '/' . $path, true, false );
			$id          = 8001;
			$severity    = 'Critical';
			$action      = 'Installed';
			$description = 'Installed plugin ' . $data['Name'] . ' Version ' . $data['Version'];
			wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
		}

		if ( 'update' === $extra['action'] ) {
			if ( isset( $extra['bulk'] ) && true == $extra['bulk'] ) {
				$slugs = $extra['plugins'];
			} else {
				$plugin_slug = isset( $upgrader->skin->plugin ) ? $upgrader->skin->plugin : $extra['plugin'];

				if ( empty( $plugin_slug ) ) {
					return;
				}

				$slugs = array( $plugin_slug );
			}

			foreach ( $slugs as $slug ) {
				$data        = get_plugin_data( WP_PLUGIN_DIR . '/' . $slug, true, false );
				$id          = 8004;
				$severity    = 'Low';
				$action      = 'Upgraded';
				$description = 'Upgraded plugin ' . $data['Name'] . ' Version ' . $data['Version'];
				if ( 'wp-stack-connect/init.php' == $slug ) {
					if ( ! get_option( 'wpstack_connect_message_status' )) {
						$connection_status	= get_option( 'wpstack_connect_connected_status' );
						$message_status		= $connection_status == 'connected' ? 'public' : 'auto-connect';
						add_option( 'wpstack_connect_message_status', $message_status );
					}
				}

				wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
			}
		}

		wpstack_send_client_web_data();
	}
}

if ( ! function_exists( 'wpstack_plugin_uninstall' ) ) {

	function wpstack_plugin_uninstall( $plugin, $deleted ) {
		if ( $deleted ) {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin, true, false );
			$id          = 8032;
			$severity    = 'High';
			$object_type = 'Plugin';
			$action      = 'Uninstalled';
			$object_id   = '';
			$description = 'Uninstalled plugin ' . $plugin_data['Name'];
			wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
			wpstack_send_client_web_data();
		}
	}
}

if ( ! function_exists( 'wpstack_theme_install_or_update' ) ) {
	function wpstack_theme_install_or_update( $upgrader, $extra ) {
		$object_type = 'Theme';
		$object_id   = 0;

		if ( ! isset( $extra['type'] ) || 'theme' !== $extra['type'] ) {
			return;
		}

		if ( 'install' === $extra['action'] ) {
			$slug = $upgrader->theme_info();

			if ( ! $slug ) {
				return;
			}

			wp_clean_themes_cache();
			$theme       = wp_get_theme( $slug );
			$id          = 7001;
			$severity    = 'Critical';
			$action      = 'Installed';
			$description = 'Installed theme ' . $theme['Name'];
			wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
		}

		if ( 'update' === $extra['action'] ) {
			if ( isset( $extra['bulk'] ) && true == $extra['bulk'] ) {
				$slugs = $extra['themes'];
			} else {
				$slugs = array( $upgrader->skin->theme );
			}

			foreach ( $slugs as $slug ) {
				$theme       = wp_get_theme( $slug );
				$id          = 7004;
				$severity    = 'Low';
				$action      = 'Updated';
				$description = 'Updated theme ' . $theme['Name'];
				wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
			}
		}

		wpstack_send_client_web_data();
	}
}

if ( ! function_exists( 'wpstack_switch_theme' ) ) {

	function wpstack_switch_theme( $theme ) {
		$id          = 7002;
		$severity    = 'High';
		$action      = 'Activated';
		$object_type = 'Theme';
		$object_id   = '';
		$description = 'Activated theme ' . $theme;
		wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
		wpstack_send_client_web_data();
	}
}

if ( ! function_exists( 'wpstack_customize_theme' ) ) {

	function wpstack_customize_theme( WP_Customize_Manager $manager ) {
		$object_type = 'Customizer';
		$action      = 'Updated';
		$object_id   = '';
		$description = 'Theme : ' . $manager->theme()->display( 'Name' );
		wpstack_get_activity( $object_type, $action, $object_id, $description );
	}
}

if ( ! function_exists( 'wpstack_delete_theme' ) ) {

	function wpstack_delete_theme( $stylesheet, $deleted ) {
		if ($deleted) {
			$id          = 7012;
			$severity    = 'High';
			$object_type = 'Theme';
			$action      = 'Deleted';
			$object_id   = '';
			$description = 'Deleted theme ' . $stylesheet;
			wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
			wpstack_send_client_web_data();
		}
	}
}

if ( ! function_exists( 'wpstack_plugin_themes_file_editor' ) ) {
    function wpstack_plugin_themes_file_editor() {
        if ( isset( $_REQUEST['action'] ) && 'edit-theme-plugin-file' === sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) ) {
            if ( isset( $_REQUEST['plugin'] ) ) {
                $plugin_path = WP_PLUGIN_DIR . '/' . sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) );
                if ( file_exists( $plugin_path ) ) {
                    $plugin_data = get_plugin_data( $plugin_path, true, false );
                    $id          = 8022;
                    $severity    = 'High';
                    $object_type = 'Plugin';
                    $action      = 'Modified';
                    $object_id   = '';
                    $description = 'Modified file with the plugin editor wp-content/plugins/' . sanitize_text_field( wp_unslash( $_REQUEST['file'] ) );
                } else {
                    return;
                }
            } elseif ( isset( $_REQUEST['theme'] ) ) {
                $theme_path = get_theme_root() . '/' . sanitize_text_field( wp_unslash( $_REQUEST['theme'] ) );
                $file_path  = $theme_path . '/' . sanitize_text_field( wp_unslash( $_REQUEST['file'] ) );
                $theme 		= wp_get_theme( sanitize_text_field($_REQUEST['theme']) );
                if ( $theme->exists() && file_exists( $file_path ) ) {
                    $id          = 7022;
                    $severity    = 'High';
                    $object_type = 'Theme';
                    $action      = 'Modified';
                    $object_id   = '';
                    $description = 'Modified file with the theme editor wp-content/themes/' . sanitize_text_field( wp_unslash( $_REQUEST['theme'] ) ) . '/' . sanitize_text_field( wp_unslash( $_REQUEST['file'] ) );
                } else {
                    return;
                }
            }

            wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
        }
    }
}

/*
 * Get database status
 */
if ( ! function_exists( 'wpstack_check_if_table_exists' ) ) {
	function wpstack_check_if_table_exists( $table_name ) {
		global $wpdb;
		$table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );

    	return $table_exists !== null;
	}
}

if ( ! function_exists( 'wpstack_check_wordpress_table' ) ) {

	function wpstack_check_wordpress_table( $table_names ) {
		if ( ! empty( $table_names ) ) {
			global $wpdb;
			$prefix        = preg_quote( $wpdb->prefix );
			$site_regex    = '/\b' . $prefix . '(\d+_)?(commentmeta|comments|links|options|postmeta|posts|terms|termmeta|term_relationships|term_relationships|term_taxonomy|usermeta|users)\b/';
			$network_regex = '/\b' . $prefix . '(blogs|blog_versions|registration_log|signups|site|sitemeta|users|usermeta)\b/';

			foreach ( $table_names as $table ) {
				if ( preg_match( $site_regex, $table ) || preg_match( $network_regex, $table ) ) {
					return true;
				}
			}
		}

		return false;
	}
}


if ( ! function_exists( 'wpstack_get_db_actor' ) ) {

	function wpstack_get_db_actor( $table_names ) {
		$wpstack_script_basename = null;
		$result                   = false;

		if ( is_null( $wpstack_script_basename ) ) {
			$wpstack_script_basename = isset( $_SERVER['SCRIPT_NAME'] ) ? basename( sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_NAME'] ) ), '.php' ) : false;
		}

		$result = $wpstack_script_basename;

		if ( wpstack_check_wordpress_table( $table_names ) ) {
			$result = 'WordPress';
		}

		return $result;
	}
}


if ( ! function_exists( 'wpstack_db_trigger_event' ) ) {

	function wpstack_db_trigger_event( $query_type, $table_names ) {
		$wpstack_query_already_logged = array();

		if ( ! empty( $table_names ) ) {
			$action = $query_type;

			if ( ! empty( $query_type ) ) {
				if ( 'create' === $query_type ) {
					$action = 'Created';
				} elseif ( 'update' === $query_type ) {
					$action = 'Modified';
				} elseif ( 'delete' === $query_type ) {
					$action = 'Deleted';
				}
			}

			$actor = wpstack_get_db_actor( $table_names );

			$id       = 0000;
			$severity = 'Unknown';
			$desc     = '';

			if ( 'WordPress' === $actor ) {
				return;
			} elseif ( 'plugins' === $actor ) {
				if ( ! empty( $query_type ) ) {
					if ( 'create' === $query_type ) {
						$id       = 6904;
						$severity = 'Low';
						$desc     = 'Plugin created database table ';
					} elseif ( 'update' === $query_type ) {
						$id       = 6914;
						$severity = 'Low';
						$desc     = 'Plugin modified the structure of database table ';
					} elseif ( 'delete' === $query_type ) {
						$id       = 6903;
						$severity = 'Medium';
						$desc     = 'Plugin deleted database table ';
					}
				}
			} elseif ( 'themes' == $actor ) {
				if ( ! empty( $query_type ) ) {
					if ( 'create' === $query_type ) {
						$id       = 6924;
						$severity = 'Low';
						$desc     = 'Theme created database table ';
					} elseif ( 'update' === $query_type ) {
						$id       = 6934;
						$severity = 'Low';
						$desc     = 'Theme modified the structure of database table ';
					} elseif ( 'delete' === $query_type ) {
						$id       = 6913;
						$severity = 'Medium';
						$desc     = 'Theme deleted database table ';
					}
				}
			} else {
				if ( ! empty( $query_type ) ) {
					if ( 'create' === $query_type ) {
						$id       = 6902;
						$severity = 'High';
						$desc     = 'Unknown component created database table ';
					} elseif ( 'update' === $query_type ) {
						$id       = 6912;
						$severity = 'High';
						$desc     = 'Unknown component modified the structure of database table ';
					} elseif ( 'delete' === $query_type ) {
						$id       = 6922;
						$severity = 'High';
						$desc     = 'Unknown component deleted database table ';
					}
				}
			}

			foreach ( $table_names as $table_name ) {
				$db_op_key = $query_type . '_' . $table_name;

				if ( in_array( $db_op_key, $wpstack_query_already_logged ) ) {
					continue;
				}

				$object_type = 'Database';
				$object_id   = '';
				$description = $desc . $table_name;
				wpstack_get_activity( $id, $severity, $object_type, $action, $object_id, $description );
				array_push( $wpstack_query_already_logged, $db_op_key );
			}
		}
	}
}

if ( ! function_exists( 'wpstack_database_status' ) ) {

	function wpstack_database_status( $queries ) {
		$query_types = array(
			'create' => array(),
			'update' => array(),
			'delete' => array(),
		);
		foreach ( $queries as $query ) {
			$query = str_replace( '`', '', $query );
			$str   = explode( ' ', $query );
			if ( preg_match( '/CREATE TABLE( IF NOT EXISTS)? ([^ ]*)/i', $query, $matches ) || preg_match( '/CREATE TABLE ([^ ]*)/i', $query, $matches ) ) {
				$table_name = $matches[ count( $matches ) - 1 ];
				if ( ! wpstack_check_if_table_exists( $table_name ) ) {
					array_push( $query_types['create'], $table_name );
				}
			} elseif ( preg_match( '|ALTER TABLE ([^ ]*)|', $query ) ) {
				array_push( $query_types['update'], $str[2] );
			} elseif ( preg_match( '|DROP TABLE( IF EXISTS)? ([^ ]*)|', $query ) ) {
				$table_name = empty( $str[4] ) ? $str[2] : $str[4];
				if ( wpstack_check_if_table_exists( $table_name ) ) {
					array_push( $query_types['delete'], $table_name );
				}
			}
		}
		if ( ! empty( $query_types['create'] ) || ! empty( $query_types['update'] ) || ! empty( $query_types['delete'] ) ) {
			foreach ( $query_types as $query_type => $table_names ) {
				wpstack_db_trigger_event( $query_type, $table_names );
			}
		}

		return $queries;
	}
}

if ( ! function_exists( 'wpstack_database_query_status' ) ) {
	function wpstack_database_query_status( $query ) {
		$table_names = array();
		$str         = explode( ' ', $query );
		$query_type  = '';
		if ( preg_match( '|DROP TABLE( IF EXISTS)? ([^ ]*)|', $query ) ) {
			$table_name = empty( $str[4] ) ? $str[2] : $str[4];
			if ( wpstack_check_if_table_exists( $table_name ) ) {
				array_push( $table_names, $table_name );
				$query_type = 'delete';
			}
		} elseif ( preg_match( '/CREATE TABLE( IF NOT EXISTS)? ([^ ]*)/i', $query, $matches ) || preg_match( '/CREATE TABLE ([^ ]*)/i', $query, $matches ) ) {
			$table_name = $matches[ count( $matches ) - 1 ];
			if ( ! wpstack_check_if_table_exists( $table_name ) ) {
				array_push( $table_names, $table_name );
				$query_type = 'create';
			}
		}

		wpstack_db_trigger_event( $query_type, $table_names );

		return $query;
	}
}

/*
 * Get file status
 */
if ( ! function_exists( 'wpstack_add_attachment' ) ) {

	function wpstack_add_attachment( $attach ) {
		$id          = 9003;
		$severity    = 'Medium';
		$object_type = 'File';
		$action      = 'Uploaded';
		$description = 'Uploaded file ' . get_the_title( $attach );
		wpstack_get_activity( $id, $severity, $object_type, $action, $attach, $description );
	}
}

if ( ! function_exists( 'wpstack_edit_attachment' ) ) {

	function wpstack_edit_attachment( $attach ) {
		$id          = 9002;
		$severity    = 'High';
		$object_type = 'File';
		$action      = 'Modified';
		$description = 'Modified file ' . get_the_title( $attach );
		wpstack_get_activity( $id, $severity, $object_type, $action, $attach, $description );
	}
}

if ( ! function_exists( 'wpstack_delete_attachment' ) ) {

	function wpstack_delete_attachment( $attach ) {
		$id          = 9004;
		$severity    = 'Low';
		$object_type = 'File';
		$action      = 'Deleted';
		$description = 'Deleted file ' . get_the_title( $attach );
		wpstack_get_activity( $id, $severity, $object_type, $action, $attach, $description );
	}
}

if ( ! function_exists( 'wpstack_comment_transition_status' ) ) {

	function wpstack_comment_transition_status( $new_status, $old_status, $comment ) {
		if ( ! empty( $comment ) && $old_status !== $new_status ) {
			$post        = get_post( $comment->comment_post_ID );
			$comment_id  = $comment->comment_ID;
			$severity    = 'Informational';
			$object_type = 'Comment';

			if ( 'approved' === $new_status ) {
				$id          = 5915;
				$action      = 'Approved';
				$description = 'Approved comment on ' . $post->post_title;
				wpstack_get_activity( $id, $severity, $object_type, $action, $comment_id, $description );
			}

			if ( 'unapproved' === $new_status ) {
				$id          = 5925;
				$action      = 'Unapproved';
				$description = 'Unapproved comment on ' . $post->post_title;
				wpstack_get_activity( $id, $severity, $object_type, $action, $comment_id, $description );
			}
		}
	}
}

if ( ! function_exists( 'wpstack_post_reply_comment' ) ) {

	function wpstack_post_reply_comment( $comment_id, $comment_approved, $comment_data ) {
		$comment = get_comment( $comment_id );

		if ( ! $comment ) {
			return;
		}

		$post        = get_post( $comment->comment_post_ID );
		$severity    = 'Informational';
		$object_type = 'Comment';
		$action      = 'Created';

		if ( isset( $comment_data['comment_parent'] ) && $comment_data['comment_parent'] ) {
			$id          = 5935;
			$description = 'Replied to comment on ' . $post->post_title;
			wpstack_get_activity( $id, $severity, $object_type, $action, $comment_id, $description );
			return;
		}

		if ( 'spam' !== $comment->comment_approved ) {
			$id          = 5905;
			$description = 'Posted comment on ' . $post->post_title;
			wpstack_get_activity( $id, $severity, $object_type, $action, $comment_id, $description );
		}
	}
}

if ( ! function_exists( 'wpstack_comment_spam' ) ) {

	function wpstack_comment_spam( $comment_id ) {
		$comment = get_comment( $comment_id );
		if ( $comment ) {
			$post        = get_post( $comment->comment_post_ID );
			$id          = 5945;
			$severity    = 'Informational';
			$object_type = 'Comment';
			$action      = 'Unapproved';
			$description = 'Marked comment on ' . $post->post_title . ' as spam';
			wpstack_get_activity( $id, $severity, $object_type, $action, $comment_id, $description );
		}
	}
}

if ( ! function_exists( 'wpstack_comment_untrash' ) ) {

	function wpstack_comment_untrash( $comment_id ) {
		$comment = get_comment( $comment_id );
		if ( $comment ) {
			$post        = get_post( $comment->comment_post_ID );
			$id          = 5955;
			$severity    = 'Informational';
			$object_type = 'Comment';
			$action      = 'Restored';
			$description = 'Restored comment on ' . $post->post_title . ' from trash';
			wpstack_get_activity( $id, $severity, $object_type, $action, $comment_id, $description );
		}
	}
}

if ( ! function_exists( 'wpstack_edit_comment' ) ) {

	function wpstack_edit_comment( $comment_id ) {
		$comment = get_comment( $comment_id );
		if ( $comment ) {
			$post        = get_post( $comment->comment_post_ID );
			$id          = 5904;
			$severity    = 'Low';
			$object_type = 'Comment';
			$action      = 'Modified';
			$description = 'Edited comment on ' . $post->post_title;
			wpstack_get_activity( $id, $severity, $object_type, $action, $comment_id, $description );
		}
	}
}

if ( ! function_exists( 'wpstack_comment_unspam' ) ) {

	function wpstack_comment_unspam( $comment_id ) {
		$comment = get_comment( $comment_id );
		if ( $comment ) {
			$post        = get_post( $comment->comment_post_ID );
			$id          = 5914;
			$severity    = 'Low';
			$object_type = 'Comment';
			$action      = 'Approved';
			$description = 'Marked comment on ' . $post->post_title . ' as not spam';
			wpstack_get_activity( $id, $severity, $object_type, $action, $comment_id, $description );
		}
	}
}

if ( ! function_exists( 'wpstack_comment_trash' ) ) {

	function wpstack_comment_trash( $comment_id ) {
		$comment = get_comment( $comment_id );
		if ( $comment ) {
			$post        = get_post( $comment->comment_post_ID );
			$id          = 5924;
			$severity    = 'Low';
			$object_type = 'Comment';
			$action      = 'Trashed';
			$description = 'Moved comment on ' . $post->post_title . ' to trash';
			wpstack_get_activity( $id, $severity, $object_type, $action, $comment_id, $description );
		}
	}
}

if ( ! function_exists( 'wpstack_comment_deleted' ) ) {

	function wpstack_comment_deleted( $comment_id ) {
		$comment = get_comment( $comment_id );
		if ( $comment ) {
			$post        = get_post( $comment->comment_post_ID );
			$id          = 5934;
			$severity    = 'Low';
			$object_type = 'Comment';
			$action      = 'Deleted';
			$description = 'Permanently deleted comment on ' . $post->post_title;
			wpstack_get_activity( $id, $severity, $object_type, $action, $comment_id, $description );
		}
	}
}

if ( ! function_exists( 'wpstack_api_content_calendar' ) ) {

	function wpstack_api_content_calendar( $post_id ) {
		if ( $post_id ) {
			$site_info       = new WPStack_Connect_Site_Info();
			$site_url        = $site_info->siteurl();
			$post_parameters = array(
				'post_id'  => $post_id,
				'site_url' => $site_url,
			);
			$connection      = new WPStack_Connect_Connection();
			$request         = new WPStack_Connect_Request_Transfer();
			$request->post( $connection->appurl() . $connection->endpoint( 'content-calendar' ), $post_parameters );
		}
	}
}

if ( ! function_exists( 'wpstack_send_log' ) ) {

	function wpstack_send_log( $endpoint, $log_data, $multiple = false, $sys_info = false ) {
		$site_info       = new WPStack_Connect_Site_Info();
		$site_url        = $site_info->siteurl();
		$post_parameters = array(
			'log_data'	=> $log_data,
			'site_url' 	=> $site_url,
			'multiple'	=> $multiple,
			'sys_info'	=> $sys_info,
		);
		$connection      = new WPStack_Connect_Connection();
		$request         = new WPStack_Connect_Request_Transfer();
		$request->post( $connection->appurl() . $connection->endpoint( $endpoint ), $post_parameters );
	}
}


if ( ! function_exists( 'wpstack_send_client_web_data' ) ) {

	function wpstack_send_client_web_data( $delay = null )
	{
		if ( $delay ) {
			$last = get_option( 'wpstack_connect_last_sent_wp_info', 0 );
			if ( 300 > time() - $last ) {
				return;
			}
		}

		wpstack_delay_send_client_web_data();
	}
}

if ( ! function_exists( 'wpstack_delay_send_client_web_data' ) ) {

	function wpstack_delay_send_client_web_data() {
		$filesystem 		= new WPStack_Connect_Filesystem();
		$plugin_theme_data	= $filesystem->plugin_theme();
		update_option( 'wpstack_connect_last_sent_wp_info', time() );
		wpstack_send_log( 'wp-info', $plugin_theme_data );
	}
}

if ( ! function_exists( 'wpstack_auto_updates_complete_check' ) ) {

	function wpstack_auto_updates_complete_check() {
		wpstack_send_client_web_data();
	}
}


add_action( 'automatic_updates_complete', 'wpstack_auto_updates_complete_check' );
add_action( 'pre_post_update', 'wpstack_before_edit_post', 10 );
add_action( 'save_post', 'wpstack_get_post_transition', 10, 2 );
add_action( 'delete_post', 'wpstack_delete_post', 10, 2 );
add_action( 'set_object_terms', 'wpstack_post_taxonomies_status', 10, 4 );
add_action( 'post_stuck', 'wpstack_post_sticky_stuck', 10, 1 );
add_action( 'post_unstuck', 'wpstack_post_sticky_unstuck', 10, 1 );

add_action( 'create_post_tag', 'wpstack_create_tag', 10, 1 );
add_action( 'create_category', 'wpstack_create_category', 10, 1 );
add_filter( 'wp_update_term_data', 'wpstack_update_taxonomies', 10, 4 );
add_action( 'pre_delete_term', 'wpstack_delete_taxonomies', 10, 2 );

add_action( 'wp_create_nav_menu', 'wpstack_create_nav_menu', 10 );
add_action( 'load-nav-menus.php', 'wpstack_update_nav_menu', 10 );
add_action( 'delete_nav_menu', 'wpstack_delete_nav_menu', 10, 3 );
add_action( 'sidebar_admin_setup', 'wpstack_widget_status' );
add_filter( 'widget_update_callback', 'wpstack_update_widget', 10, 4 );
add_action( 'delete_widget', 'wpstack_delete_widget', 10, 3 );

add_action( 'user_register', 'wpstack_user_register' );
add_action( 'delete_user', 'wpstack_delete_user' );
add_action( 'wpmu_delete_user', 'wpstack_delete_user' );
add_action( 'set_user_role', 'wpstack_user_role_change', 10, 1 );
add_action( 'profile_update', 'wpstack_user_profile_update', 10, 2 );
add_action( 'wp_login', 'wpstack_user_log_in', 10, 2 );
add_action( 'wp_login_failed', 'wpstack_user_log_in_failed' );
add_action( 'clear_auth_cookie', 'wpstack_user_log_out' );
add_action( 'add_user_to_blog', 'wpstack_add_user_to_blog', 10, 3 );
add_action( 'remove_user_from_blog', 'wpstack_remove_user_from_blog', 10, 2 );
add_action( 'updated_user_meta', 'wpstack_user_updated_meta', 10, 4 );
add_action( 'retrieve_password', 'wpstack_user_sent_password_reset', 10, 1 );
// add_action( 'edit_user_profile', 'wpstack_user_opened_profile', 10, 1 );
add_action( 'lostpassword_post', 'wpstack_user_request_password_reset', 10, 2 );

add_action( '_core_updated_successfully', 'wpstack_core_updated' );
add_action( 'updated_option', 'wpstack_updated_option', 10, 3 );

add_action( 'activated_plugin', 'wpstack_activated_plugin' );
add_action( 'deactivated_plugin', 'wpstack_deactivated_plugin' );
add_action( 'upgrader_process_complete', 'wpstack_plugin_install_or_update', 10, 2 );
add_action( 'deleted_plugin', 'wpstack_plugin_uninstall', 10, 2 );
add_action( 'upgrader_process_complete', 'wpstack_theme_install_or_update', 11, 2 );
add_action( 'switch_theme', 'wpstack_switch_theme' );

add_action( 'deleted_theme', 'wpstack_delete_theme', 10, 2 );
add_action( 'admin_init', 'wpstack_plugin_themes_file_editor' );

add_action( 'dbdelta_queries', 'wpstack_database_status' );
add_filter( 'query', 'wpstack_database_query_status' );

add_action( 'add_attachment', 'wpstack_add_attachment' );
add_action( 'edit_attachment', 'wpstack_edit_attachment' );
add_action( 'delete_attachment', 'wpstack_delete_attachment' );

add_action( 'transition_comment_status', 'wpstack_comment_transition_status', 10, 3 );
add_action( 'comment_post', 'wpstack_post_reply_comment', 10, 3 );
add_action( 'spammed_comment', 'wpstack_comment_spam', 10, 1 );
add_action( 'untrashed_comment', 'wpstack_comment_untrash', 10, 1 );
add_action( 'edit_comment', 'wpstack_edit_comment', 10, 1 );
add_action( 'unspammed_comment', 'wpstack_comment_unspam', 10, 1 );
add_action( 'trashed_comment', 'wpstack_comment_trash', 10, 1 );
add_action( 'deleted_comment', 'wpstack_comment_deleted', 10, 1 );
