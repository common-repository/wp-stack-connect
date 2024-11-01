<?php

class WPStack_Connect_Auto_Link {

	public $wpdb;
	public $account;
	public $table_name;

	function __construct( $callback_handler ) {
		global $wpdb;
		$this->wpdb       = $wpdb;
		$this->account    = $callback_handler->account;
		$this->table_name = $wpdb->prefix . 'wpstack_connect_auto_links';
	}

	public function get() {
		$result = $this->get_auto_links_data();
		wp_send_json( $result );
	}

	public function update( $parameters ) {
		$link    = sanitize_text_field( $parameters['link'] );
		$keyword = sanitize_text_field( $parameters['keyword'] );
		if ( ! $link || ! $keyword ) {
			wp_send_json_error( 'Sorry, link or keyword does not exist' );
		}

		$check_duplicate = $this->wpdb->get_row(
			$this->wpdb->prepare(
				'SELECT `alid` FROM %i
					WHERE `link` = %s
						AND `keyword` = %s
				',
				$this->table_name,
				$link,
				$keyword
			)
		);
		if ( $check_duplicate ) {
			wp_send_json_error( 'Sorry, link and keyword already exist' );
		}

		$insert_query = $this->wpdb->insert(
			$this->table_name,
			array(
				'link'    => $link,
				'keyword' => $keyword,
			)
		);

		if ( is_wp_error( $insert_query ) ) {
			wp_send_json_error( 'Error adding link' );
		}

		$result = array(
			'success'    => true,
			'auto_links' => $this->get_auto_links_data(),
		);

		wp_send_json( $result );
	}

	public function bulk( $parameters ) {
		if ( ! empty( $parameters ) ) {
			foreach ( $parameters as $data ) {
				$link    = sanitize_text_field( $data['link'] );
				$keyword = sanitize_text_field( $data['keyword'] );
				
				$check_duplicate = $this->wpdb->get_row(
					$this->wpdb->prepare(
						'SELECT `alid` FROM %i
							WHERE `link` = %s
								AND `keyword` = %s
						',
						$this->table_name,
						$link,
						$keyword
					)
				);

				if ( $check_duplicate ) {
					continue;
				}

				$insert_query = $this->wpdb->insert(
					$this->table_name,
					array(
						'link'    => $link,
						'keyword' => $keyword,
					)
				);

				if ( is_wp_error( $insert_query ) ) {
					continue;
				}
			}
			
			$result = array(
				'success'    => true,
				'auto_links' => $this->get_auto_links_data(),
			);

			wp_send_json( $result );
		} else {
			wp_send_json_error( 'Error, due to empty data' );
		}
	}

	public function delete( $parameters ) {
		if ( $parameters['alid'] ) {
			$alid   = sanitize_text_field( $parameters['alid'] );
			$delete = $this->wpdb->update( $this->table_name, array( 'status' => 0 ), array( 'alid' => $alid ) );
			if ( is_wp_error( $delete ) ) {
				wp_send_json_error( 'Error deleting link' );
			}

			$result = array(
				'success'    => true,
				'auto_links' => $this->get_auto_links_data(),
			);

			wp_send_json( $result );
		}

		wp_send_json_error( 'Cannot process request due to insufficient data' );

	}

	private function get_auto_links_data() {
		$get_data     = $this->wpdb->get_results( $this->wpdb->prepare(
				'SELECT * from %i WHERE `status` = 1',
				$this->table_name
			)
		);
		return $get_data;
	}

	public function process( $request ) {
		switch ( $request->method ) {
			case 'get':
				$this->get();
				break;

			case 'update':
				$this->update( $request->params );
				break;
			
			case 'bulk':
				$this->bulk( $request->params );
				break;

			case 'delete':
				$this->delete( $request->params );
				break;

			default:
				break;
		}
	}
}
