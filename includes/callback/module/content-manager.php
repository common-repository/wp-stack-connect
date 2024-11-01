<?php

class WPStack_Connect_Content_Manager {

	public $account;

	function __construct( $callback_handler ) {
		$this->account = $callback_handler->account;
	}

	public function include_files() {
		require_once ABSPATH . WPINC . '/rewrite.php';
		$GLOBALS['wp_rewrite'] = new WP_Rewrite();
		require_once ABSPATH . 'wp-includes/capabilities.php';
		require_once ABSPATH . 'wp-includes/pluggable.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
	}

	public function post() {
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
			'data'	 => wpstack_get_all_users(),
		);

		$authors = array(
			'status' => 200,
			'data'	 => wpstack_get_publisher_users(),
		);

		$post_type = array(
			'status' => 200,
			'data'	 => wpstack_get_custom_post_type(),
		);

		$statuses = array(
			'status' => 200,
			'data'	 => wpstack_get_statuses(),
		);

		$date = array(
			'status' => 200,
			'data'	 => wpstack_get_posts_date(),
		);

		$response = array(
			'success'    => true,
			'categories' => $categories,
			'tags'       => $tags,
			'users'      => $users,
			'authors'	 => $authors,
			'post_type'  => $post_type,
			'statuses'   => $statuses,
			'date'       => $date,
		);

		wp_send_json( $response );
	}

	public function page() {
		$user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );

		$users = array(
			'status' => 200,
			'data'	 => wpstack_get_all_users(),
		);

		$statuses = array(
			'status' => 200,
			'data'	 => wpstack_get_statuses(),
		);

		$date = array(
			'status' => 200,
			'data'	 => wpstack_get_pages_date(),
		);

		$response = array(
			'success'  => true,
			'users'    => $users,
			'statuses' => $statuses,
			'date'     => $date,
		);

		wp_send_json( $response );
	}

	public function process( $request ) {
		$this->include_files();
		switch ( $request->method ) {
			case 'post':
				$this->post();
				break;

			case 'page':
				$this->page();
				break;

			default:
				break;
		}
	}
}
