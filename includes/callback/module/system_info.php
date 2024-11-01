<?php

class WPStack_Connect_SystemInfo {

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
		require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
		require_once ABSPATH . 'wp-admin/includes/update.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
	}

	public function get() {
		$user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );		
		$info = wpstack_get_system_info();
		wpstack_send_client_web_data();
		wp_send_json( $info ?? array() );
	}

	public function process( $request ) {
		$this->include_files();
		switch ( $request->method ) {
			case 'get':
				$this->get();
				break;

			default:
				break;
		}
	}
}
