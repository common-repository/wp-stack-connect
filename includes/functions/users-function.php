<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpstack_login_with_token' ) ) {

	function wpstack_login_with_token() {
		if ( ! empty( $_GET['wpstack_login_token'] ) ) {
			$token 			= sanitize_key( wp_unslash( $_GET['wpstack_login_token'] ) );
			$users 			= wpstack_get_user_with_token( $token );
			$temporary_user = ! empty( $users ) ? $users[0] : '';
	
			if ( ! empty( $temporary_user ) ) {
				$temporary_user_id = $temporary_user->ID;
				$do_login          = true;
	
				if ( is_user_logged_in() ) {
					$current_user_id = get_current_user_id();
	
					if ( $temporary_user_id !== $current_user_id ) {
						wp_logout();
					} else {
						$do_login = false;
					}
				}
	
				if ( $do_login ) {
					$temporary_user_login = $temporary_user->login;
					wp_set_current_user( $temporary_user_id, $temporary_user_login );
					wp_set_auth_cookie( $temporary_user_id );
					do_action( 'wp_login', $temporary_user_login, $temporary_user );
				}
	
				update_user_meta( $temporary_user_id, 'wpstack_login_token', '' );
				$redirect_to_url = esc_url( admin_url() );
	
			} else {
				$redirect_to_url = esc_url( home_url() );
			}
	
			wp_safe_redirect( $redirect_to_url );
			exit();
		}
	}	
}

if ( ! function_exists( 'wpstack_get_user_with_token' ) ) {

	function wpstack_get_user_with_token( $token ) {
		if ( empty( $token ) ) {
			return false;
		}

		$args = array(
			'meta_key'   => 'wpstack_login_token',
			'meta_value' => sanitize_text_field( $token ),
		);

		$user_query = new WP_User_Query( $args );
		$users      = $user_query->get_results();

		if ( empty( $users ) ) {
			return false;
		}

		$user_id = array_column( $users, 'ID' );

		if( is_wpstack_connect_login_expired( $user_id ) ) {
			return false;
		}

		return $users;
	}
}

if ( ! function_exists( 'is_wpstack_connect_login_expired' ) ) {

	function is_wpstack_connect_login_expired( $user_id ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		if ( empty( $user_id ) ) {
			return false;
		}

		$expire = get_user_meta( $user_id, '_wpstack_connect_token_expire', true );

		return ! empty( $expire ) && is_numeric( $expire ) && wpstack_connect_current_gmt_timestamp() >= floatval( $expire ) ? true : false;
	}
}

if ( ! function_exists( 'wpstack_connect_current_gmt_timestamp' ) ) {

	function wpstack_connect_current_gmt_timestamp() {
		return strtotime( gmdate( 'Y-m-d H:i:s', time() ) );
	}
}

if ( ! function_exists( 'wpstack_connect_user_expire_time' ) ) {

	function wpstack_connect_user_expire_time() {
		return wpstack_connect_current_gmt_timestamp() + floatval(HOUR_IN_SECONDS);
	}
}

if ( ! function_exists( 'wpstack_user_active_session' ) ) {

	function wpstack_user_active_session() {
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			update_user_meta( $user->ID, 'wpstack_last_activity', time() );
		}
	}
}

if ( ! function_exists( 'wpstack_deactive_session' ) ) {

	function wpstack_deactive_session() {
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			delete_user_meta( $user->ID, 'wpstack_last_activity' );
		}
	}
}

add_action( 'init', 'wpstack_login_with_token' );
add_action( 'init', 'wpstack_user_active_session', 10, 2 );
add_action( 'clear_auth_cookie', 'wpstack_deactive_session' );
add_action( 'init', 'wpstack_blocker_redirect' );

if ( ! function_exists( 'wpstack_blocker_is_ip_blocked' ) ) {

	function wpstack_blocker_is_ip_blocked( $ip_address ) {
		$blocked_ips = get_option( 'wpstack_connect_blocked_ips', array() );
		return in_array( $ip_address, $blocked_ips );
	}
}

if ( ! function_exists( 'wpstack_blocker_redirect' ) ) {

	function wpstack_blocker_redirect() {
		if ( is_admin() || in_array( $GLOBALS['pagenow'], array( 'wp-login.php' ) ) ) {
			$current_ip = !is_null(sanitize_text_field($_SERVER['REMOTE_ADDR'])) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '';
		
			if ( filter_var( $current_ip, FILTER_VALIDATE_IP ) && wpstack_blocker_is_ip_blocked( $current_ip ) ) {
				include plugin_dir_path( dirname( __FILE__ ) ) . '/configuration/template/blocked-page.php';
				exit;
			}
		}
	}
}

if ( ! function_exists( 'wpstack_get_publisher_users' ) ) {

	function wpstack_get_publisher_users() {
		$allowed_roles	= array( 'administrator', 'author', 'editor' );
		$user_args 		= array(
			'role__in'	=> $allowed_roles,
			'number'	=> -1,
		);
		$users 			= new WP_User_Query( $user_args );
	
		if ( empty( $users->results ) ) {
			return array();
		}
	
		return $users->results;
	}
}

if ( ! function_exists( 'wpstack_get_all_users' ) ) {

	function wpstack_get_all_users() {
		$allowed_roles	= array( 'administrator', 'editor', 'author', 'subscriber' );
		$user_args 		= array(
			'role__in'	=> $allowed_roles,
			'number'	=> -1,
		);
		$users 			= new WP_User_Query( $user_args );
		$user_list_data	= array();
	
		if ( empty( $users->results ) ) {
			return array();
		}
	
		foreach ( $users->results as $user ) {
			$user_list_data[] = wpstack_return_user_data( $user );
		}
	
		return $user_list_data;
	}
}

if ( ! function_exists( 'wpstack_return_user_data' ) ) {

	function wpstack_return_user_data( $user ) {
		return array(
			'id'				=> $user->ID,
			'username'			=> $user->user_login,
			'name'				=> $user->display_name,
			'first_name'		=> $user->first_name,
			'last_name' 		=> $user->last_name,
			'email' 			=> $user->user_email,
			'description' 		=> $user->description,
			'nickname' 			=> $user->nickname,
			'slug'				=> $user->user_nicename,
			'roles'				=> $user->roles,
			'registered_date'	=> $user->user_registered,
			'avatar_urls'		=> array(
				'24' => get_avatar_url( $user->ID, array( 'size' => 24 ) ),
				'48' => get_avatar_url( $user->ID, array( 'size' => 48 ) ),
				'96' => get_avatar_url( $user->ID, array( 'size' => 96 ) ),
			),
		);
	}
}
