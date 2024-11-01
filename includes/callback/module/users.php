<?php

class WPStack_Connect_Users {

	public $account;

	function __construct( $callback_handler ) {
		$this->account = $callback_handler->account;
	}

	public function include_files() {
		require_once ABSPATH . WPINC . '/rewrite.php';
		$GLOBALS['wp_rewrite'] = new WP_Rewrite();
		require_once ABSPATH . 'wp-includes/capabilities.php';
		require_once ABSPATH . 'wp-includes/pluggable.php';
		require_once ABSPATH . 'wp-includes/user.php';
		require_once ABSPATH . 'wp-includes/class-wp-session-tokens.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
		require_once ABSPATH . 'wp-admin/includes/user.php';
	}

	public function get( $parameters ) {
		$user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );
		if ( $parameters ) {
			if ( array_key_exists( 'user_id', $parameters ) ) {
				$user_id 		= sanitize_text_field( $parameters['user_id'] );
				$user			= array(
					'data'		=> $this->get_wp_user_data_by_id( $user_id ),
					'status'	=> 200
				);
				
				wp_send_json( $user );
			}

			wp_send_json( $this->insufficient_data() );
		} else {
			$user_list		= array(
				'data'		=> $this->get_wp_user_list_data(),
				'status'	=> 200
			);
			
			wp_send_json( $user_list );
		}
	}

	private function get_wp_user_list_data() {
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
			$user_list_data[] = $this->array_user_data( $user );
		}
	
		return $user_list_data;
	}

	private function get_wp_user_data_by_id( $user_id ) {
		$user = get_user_by( 'ID', $user_id );
	
		if ( ! $user ) {
			return array();
		}
	
		return $this->array_user_data( $user );
	}

	private function array_user_data( $user ) {
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

	public function create( $parameters ) {
		$user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );
		
		if ( array_key_exists( 'username', $parameters ) && array_key_exists( 'email', $parameters ) && array_key_exists( 'password', $parameters ) && array_key_exists( 'roles', $parameters ) ) {
			$userdata = array(
				'user_login' 	=> sanitize_user( $parameters['username'] ),
				'user_email' 	=> sanitize_email( $parameters['email'] ),
				'user_pass'  	=> wp_hash_password( $parameters['password'] ),
				'role'  	 	=> sanitize_text_field( $parameters['roles'] ),
				'first_name' 	=> ( array_key_exists( 'first_name', $parameters ) ) ? sanitize_text_field( $parameters['first_name'] ) : '',
				'last_name'  	=> ( array_key_exists( 'last_name', $parameters ) ) ? sanitize_text_field( $parameters['last_name'] ) : '',
				'description'	=> ( array_key_exists( 'description', $parameters ) ) ? sanitize_textarea_field( $parameters['description'] ) : '',
				'user_url'		=> ( array_key_exists( 'user_url', $parameters ) ) ? esc_url( $parameters['user_url'] ) : '',
			);
			
			$user_id = wp_insert_user( wp_slash( $userdata ) );
			
			if ( is_wp_error( $user_id ) ) {
				$error_response = array(
					'data' 			=> array(
						'code'		=> $user_id->get_error_code(),
						'message'	=> $user_id->get_error_message(),
						'data' 		=> NULL,
					),
					'status'		=> 500,
				);

				wp_send_json( $error_response );
			}

			$user        = array(
				'data'   => $this->get_wp_user_data_by_id( $user_id ),
				'status' => 200
			);
			
			wp_send_json( $user );
		}
		
		wp_send_json( $this->insufficient_data() );
	}

	private function insufficient_data() {
		return array(
			'status' => 500,
			'data'	 => array(
				'code'	  => 'insufficient_data',
				'message' => 'Sorry, cannot process request due to insufficient data',
				'data'	  => NULL
			)
		);
	}

	public function update( $parameters ) {
		$user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );
		if ( $parameters['users'] ) {
			$errors	= array();
			$users  = $parameters['users'];
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					$wp_user = get_user_by( 'ID', $user );
					if ( ! $wp_user ) {
						$errors[] = $user;
						continue;
					}
					
					$userdata = array(
						'ID'			=> (int) $user,
						'user_email' 	=> ( array_key_exists( 'email', $parameters ) ) ? sanitize_email( $parameters['email'] ) : $wp_user->user_email,
						'role'  	 	=> ( array_key_exists( 'roles', $parameters ) ) ? sanitize_text_field( $parameters['roles'] ) : $wp_user->roles[0], 
						'first_name' 	=> ( array_key_exists( 'first_name', $parameters ) ) ? sanitize_text_field( $parameters['first_name'] ) : $wp_user->first_name,
						'last_name'  	=> ( array_key_exists( 'last_name', $parameters ) ) ? sanitize_text_field( $parameters['last_name'] ) : $wp_user->last_name,
						'description'	=> ( array_key_exists( 'description', $parameters ) ) ? sanitize_textarea_field( $parameters['description'] ) : $wp_user->description,
						'user_url'		=> ( array_key_exists( 'user_url', $parameters ) ) ? esc_url( $parameters['user_url'] ) : $wp_user->user_url,
					);

					if ( array_key_exists( 'password', $parameters ) ) {
						$userdata['user_pass'] = wp_hash_password( $parameters['password'] );
					}

					if ( is_wp_error( wp_update_user( $userdata ) ) ) {
						$errors[] = $user;
					}
				}

				if ( empty( $errors ) ) {
					$success = array( 'success' => true );
					wp_send_json( $success );
				} else {
					$error_message = 'Error updating user(s) with ID(s): ' . implode( ', ', $errors );
					wp_send_json_error( $error_message );
				}
			}
		}

		wp_send_json_error( esc_html__( 'Cannot process request due to insufficient data' ) );
	}

	public function delete( $parameters ) {
		$user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );
		if ( $parameters['users'] ) {
			$errors   = array();
			$users    = $parameters['users'];
			$reassign = ( array_key_exists( 'reassign', $parameters ) && 'delete_all' !== $parameters['reassign'] ) ? sanitize_text_field( $parameters['reassign'] ) : NULL;
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					if ( is_wp_error( wp_delete_user( $user, $reassign ) ) ) {
						$errors[] = $user;
					}
				}

				if ( empty( $errors ) ) {
					$success = array( 'success' => true );
					wp_send_json( $success );
				} else {
					$error_message = 'Error deleting user(s) with ID(s): ' . implode( ', ', $errors );
					wp_send_json_error( $error_message );
				}
			}
		}

		wp_send_json_error( esc_html__( 'Cannot process request due to insufficient data' ) );
	}

	public function login( $parameters ) {
		if ( $parameters['user_id'] ) {
			$user_id = base64_decode( $this->account->user_id );
			wp_set_current_user( $user_id );
			$user = get_user_by( 'ID', sanitize_text_field( $parameters['user_id'] ) );
			if ( ! is_wp_error( $user ) ) {
				if ( $user ) {
					$token = $this->generate_login_token( $user->ID );
					update_user_meta( $user->ID, 'wpstack_login_token', $token );
					$url 	 = admin_url() . '?wpstack_login_token=' . $token;
					$success = array(
						'success' => true,
						'link'    => esc_url( $url ),
					);
		
					wp_send_json( $success );
				} else {
					wp_send_json_error( esc_html__( 'Sorry, user not found' ) );
				}
			} else {
				wp_send_json_error( esc_html__( 'Something wrong with your WordPress' ) );
			}
		}

		wp_send_json_error( esc_html__( 'Cannot process request due to insufficient data' ) );
	}

	private function generate_login_token( $user_id ) {
		$byte_length = 64;

		if ( function_exists( 'random_bytes' ) ) {
			return bin2hex( random_bytes( $byte_length ) );
		}

		$str  = $user_id . microtime() . uniqid( '', true );
		$salt = substr( md5( $str ), 0, 32 );

		return hash( 'sha256', $str . $salt );
	}

	public function login_attemps() {
		$user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );

		global $wpdb;
		$table_name   = $wpdb->prefix . 'wpstack_connect_activity_log';
		$get_data     = $wpdb->get_results( 
			$wpdb->prepare('SELECT * from %i WHERE user_id != 0 AND action = "Logged In" ORDER BY modified_date desc', $table_name)
		);
		$login_logs   = array();
		$active_user  = array();
		$active       = array();
		$temp         = array();

		if ( $get_data && is_array( $get_data ) ) {
			$login_logs	= $get_data;
		}

		$args = array(
			'meta_key'     => 'wpstack_last_activity',
			'meta_value'   => time() - 3600,
			'meta_compare' => '>=',
		);

		$user_query = new WP_User_Query( $args );
		$users      = $user_query->get_results();
		if ( ! empty( $users ) ) {
			$active_user = $users;
		}
		
		$active_user_ids = array_column( $active_user, 'ID' );
		
		foreach ( $login_logs as $log ) {
			$id = (int) $log->user_id;
			if ( $log->id == '1004' && in_array( $id, $active_user_ids ) && ! in_array( $id, $temp ) ) {
				array_push( $active, $log );
				array_push( $temp, $id );
			}
		}

		wp_send_json( $active );
	}

	public function terminate( $parameters ) {
		if ( $parameters['user_id'] ) {
			$user_id = base64_decode( $this->account->user_id );
			wp_set_current_user( $user_id );
			$id        = sanitize_text_field( $parameters['user_id'] );
			$sessions  = WP_Session_Tokens::get_instance( $id );
			$user_data = get_userdata( $id );
			if ( $sessions ) {
				$sessions->destroy_all();
				delete_user_meta( $id, 'wpstack_last_activity' );
				$idLog       = 1013;
				$severity    = 'Medium';
				$object_type = 'User';
				$action      = 'Terminated';
				$user_id     = $user_data->ID;
				$description = 'Terminated the session of the user ' . $user_data->user_login;
				wpstack_get_activity( $idLog, $severity, $object_type, $action, $user_id, $description );
				$success 	 = array( 'success' => true );
				wp_send_json( $success );
			} else {
				wp_send_json_error( esc_html__( 'This user does not have session' ) );
			}
		}

		wp_send_json_error( esc_html__( 'Cannot process request due to insufficient data' ) );
	}

	public function autologin( ) {
		$user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );
		$user = get_user_by( 'ID', $user_id );
		if ( ! is_wp_error( $user ) ) {
			if ( $user ) {
				$token = $this->generate_login_token( $user->ID );
				update_user_meta( $user->ID, 'wpstack_login_token', $token );
				update_user_meta( $user->ID, '_wpstack_connect_token_expire', wpstack_connect_user_expire_time() );
				$url 	 = admin_url() . '?wpstack_login_token=' . $token;
				$success = array(
					'success' => true,
					'link'    => esc_url( $url ),
				);
	
				wp_send_json( $success );
			} else {
				wp_send_json_error( esc_html__( 'Sorry, user not found' ) );
			}
		} else {
			wp_send_json_error( esc_html__( 'Something wrong with your WordPress' ) );
		}

		wp_send_json_error( esc_html__( 'Cannot process request due to insufficient data' ) );
	}

	public function process( $request ) {
		$this->include_files();
		switch ( $request->method ) {
			case 'get':
				$this->get( $request->params );
				break;

			case 'create':
				$this->create( $request->params );
				break;

			case 'update':
				$this->update( $request->params );
				break;

			case 'delete':
				$this->delete( $request->params );
				break;

			case 'login':
				$this->login( $request->params );
				break;
			
			case 'autologin':
				$this->autologin( $request->params );
				break;

			case 'login_attemps':
				$this->login_attemps();
				break;

			case 'terminate':
				$this->terminate( $request->params );
				break;

			default:
				break;
		}
	}
}
