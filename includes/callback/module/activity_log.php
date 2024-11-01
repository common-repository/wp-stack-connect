<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once ABSPATH . 'wp-includes/capabilities.php';
require_once ABSPATH . 'wp-includes/pluggable.php';

class WPStack_Connect_activity_log extends WPStack_Connect_Callback_Base {

	function __construct( $callback_handler ) {
		global $wpdb;
		$this->wpdb    = $wpdb;
		$this->account = $callback_handler->account;
	}

	public function process( $request ) {
		$resp = array();
		switch ( $request->method ) {
			case 'get':
				$resp = $this->get( $request->params );
				break;
			case 'users':
				$resp = $this->users( $request->params );
				break;
			case 'objects':
				$resp = $this->objects( $request->params );
				break;
			case 'actions':
				$resp = $this->actions( $request->params );
				break;

			default:
				break;
		}

		if ( is_array( $resp ) ) {
			$resp = $resp;
		}
		return $resp;
	}

	public function get( $param ) {
		$table_name = $this->wpdb->prefix . 'wpstack_connect_activity_log';
		$select     = $this->wpdb->prepare('SELECT * FROM %i WHERE `alid` IS NOT NULL ', $table_name);

		if ( $param['user'] ) {
			$select .= $this->wpdb->prepare(' AND `user_id` = %s', $param['user']);
		}

		if ( $param['action'] ) {
			$select .= $this->wpdb->prepare(' AND `action` = %s', $param['action']);
		}

		if ( $param['object'] ) {
			$select .= $this->wpdb->prepare(' AND `object_type` = %s', $param['object']);
		}

		$select .= $this->wpdb->prepare(' ORDER BY modified_date desc ');

		if ( $param['limit'] ) {
			$select .= $this->wpdb->prepare(' LIMIT %d ', $param['limit']);
		}

		$sql_query = $this->wpdb->prepare( $select );

		$get_data = $this->wpdb->get_results( $sql_query );

		wp_send_json( $get_data ?? array() );

		// if ($get_data) {
		// wp_send_json($get_data);
		// } else {
		// return new WP_Error(
		// 'Data not found',
		// array(
		// 'error'     => 'Activity log not found (empty)',
		// 'status'    => 403,
		// )
		// );
		// }
	}

	public function users( $request ) {
		$table_name = $this->wpdb->prefix . 'wpstack_connect_activity_log';
		$get_data   = $this->wpdb->get_results( 
			$this->wpdb->prepare('SELECT DISTINCT user_id, user_name FROM %i WHERE user_id != 0', $table_name)
		);
		if ( $get_data ) {
			wp_send_json( $get_data );
		} else {
			return new WP_Error(
				'Data not found',
				array(
					'error'  => 'Users list activity log not found (empty)',
					'status' => 403,
				)
			);
		}
	}

	public function objects( $request ) {
		$table_name = $this->wpdb->prefix . 'wpstack_connect_activity_log';
		$get_data   = $this->wpdb->get_results( 
			$this->wpdb->prepare('SELECT DISTINCT object_type FROM %i', $table_name)
		);
		if ( $get_data ) {
			wp_send_json( $get_data );
		} else {
			return new WP_Error(
				'Data not found',
				array(
					'error'  => 'Object list activity log not found (empty)',
					'status' => 403,
				)
			);
		}
	}

	public function actions( $request ) {
		$table_name = $this->wpdb->prefix . 'wpstack_connect_activity_log';
		$get_data   = $this->wpdb->get_results( 
			$this->wpdb->prepare('SELECT DISTINCT action FROM %i', $table_name)
		);
		if ( $get_data ) {
			wp_send_json( $get_data );
		} else {
			return new WP_Error(
				'Data not found',
				array(
					'error'  => 'Action list activity log not found (empty)',
					'status' => 403,
				)
			);
		}
	}
}
