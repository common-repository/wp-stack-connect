<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPStack_Connect_Manage_Posts extends WPStack_Connect_Manage_Abstract {


	public $end_point = 'posts';

	public $result;

	protected $meta_key_seo_title;

	protected $meta_key_seo_description;

	public function get_posts() {
		$this->result[ $this->prefix_sync . $this->end_point ] = WPStack_Connect_Core::get_request_api( $this->end_point );
		return $this->result;
	}

	public function create_seo_post_meta( $seo_plugin, $params ) {
		$post_id         = $params['post_id'];
		$seo_title       = $params['seo_title'];
		$seo_description = $params['seo_description'];
		$this->process_seo_post_meta( $post_id, $seo_title, $seo_description, $seo_plugin );
		return true;
	}

	public function update_seo_post_meta( $seo_plugin, $request = array() ) {
		$post_id         = $request->get_param( 'post_id' );
		$seo_title       = $request->get_param( 'seo_title' );
		$seo_description = $request->get_param( 'seo_description' );
		$this->process_seo_post_meta( $post_id, $seo_title, $seo_description, $seo_plugin );
		return true;
	}

	private function process_seo_post_meta( $post_id, $seo_title, $seo_description, $seo_plugin ) {
		$plugin_prefix = null;
		foreach ( $seo_plugin as $plugin ) {
			$this->meta_key_seo_title       = $plugin['prefix'] . $plugin['meta_key'][0];
			$this->meta_key_seo_description = $plugin['prefix'] . $plugin['meta_key'][1];
			$plugin_prefix                  = $plugin['prefix'];
		}

		if ( $this->meta_key_seo_title ) {
			update_post_meta( $post_id, $this->meta_key_seo_title, $seo_title );
		}

		if ( $this->meta_key_seo_description ) {
			update_post_meta( $post_id, $this->meta_key_seo_description, $seo_description );
		}

		if ( $plugin_prefix ) {
			$this->update_custom_table( $post_id, $plugin_prefix, $seo_title, $seo_description );
		}
	}

	public function get_seo_post_meta( $seo_plugin, $post_id ) {
		foreach ( $seo_plugin as $plugin ) {
			$this->meta_key_seo_title       = $plugin['prefix'] . $plugin['meta_key'][0];
			$this->meta_key_seo_description = $plugin['prefix'] . $plugin['meta_key'][1];
		}

		$data = array(
			'seo_title'       => ( ! is_null( $this->meta_key_seo_title ) ) ? get_post_meta( $post_id, $this->meta_key_seo_title, true ) : '',
			'seo_description' => ( ! is_null( $this->meta_key_seo_description ) ) ? get_post_meta( $post_id, $this->meta_key_seo_description, true ) : '',
		);

		return $data;
	}

	private function update_custom_table( $post_id, $plugin_prefix, $seo_title, $seo_description ) {
		switch ( $plugin_prefix ) {
			case '_aioseo_':
				$this->update_aioseo_table( $post_id, $seo_title, $seo_description );
				break;
			default:
				break;
		}
	}

	private function update_aioseo_table( $post_id, $seo_title, $seo_description ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'aioseo_posts';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
			$data_exist = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT `id` FROM %i
						WHERE `post_id` = %s
					',
					$table_name,
					$post_id
				)
			);

			if ( $data_exist ) {
				$data  = array(
					'title'       => $seo_title,
					'description' => $seo_description,
				);
				$where = array( 'post_id' => $post_id );
				$wpdb->update( $table_name, $data, $where );
			} else {
				$data = array(
					'post_id'     => $post_id,
					'title'       => $seo_title,
					'description' => $seo_description,
				);
				$wpdb->insert( $table_name, $data );
			}
		}
	}
}
