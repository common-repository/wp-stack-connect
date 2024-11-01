<?php

class WPStack_Connect_publisher {

	public $account;

	function __construct( $callback_handler ) {
		$this->account = $callback_handler->account;
	}

	public function include_files() {
		require_once ABSPATH . WPINC . '/rewrite.php';
		$GLOBALS['wp_rewrite'] = new WP_Rewrite();
		require_once ABSPATH . 'wp-includes/capabilities.php';
		require_once ABSPATH . 'wp-includes/pluggable.php';
		require_once ABSPATH . 'wp-includes/rest-api.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
	}

	public function import() {
		$user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );

		$categories = array(
			'status' => 200,
			'data'	 => wpstack_get_categories(),
		);

		$tags = array(
			'status' => 200,
			'data'	 => wpstack_get_tags(),
		);

		$users = array(
			'status' => 200,
			'data'	 => wpstack_get_publisher_users(),
		);

		$post_type = array(
			'status' => 200,
			'data'	 => wpstack_get_custom_post_type(),
		);

		$response = array(
			'success'    => true,
			'categories' => $categories,
			'tags'       => $tags,
			'users'      => $users,
			'post_type'	 => $post_type,
		);

		wp_send_json( $response );
	}

	public function process( $request ) {
		$this->include_files();
		$resp = array();
		switch ( $request->method ) {
			case 'import':
				$resp = $this->import();
				break;

			default:
				break;
		}

		if ( is_array( $resp ) ) {
			$resp = $resp;
		}
		return $resp;
	}
}