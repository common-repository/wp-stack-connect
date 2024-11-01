<?php

class WPStack_Connect_Core {

	protected static $rest_api_controller = '/wp-json/wp/v2/';

	protected static $rest_api_ms_fetch_file = '/api/filemanager/fetch_file';

	protected static $rest_api_ms_extract_file = '/api/filemanager/extract_file';

	protected static $rest_api_ms_scan = '/api/scan';

	public static function get_request_api( $end_point = '', $token = '' ) {
		$url        = site_url() . self::$rest_api_controller . $end_point;
		$get_result = self::get( $url, $token );

		if ( ! empty( $get_result ) ) {
			return $get_result;
		}

		return false;
	}

	protected static function get( $url = '', $token = '' ) {
		$headers = array();
		if ( $token !== '' ) {
			$headers = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
				),
			);
		}
		$response = wp_remote_get( $url, $headers );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response = wp_remote_retrieve_body( $response );

		return $response;
	}

	protected static function post( $url = '', $args = array() ) {

		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response = wp_remote_retrieve_body( $response );

		return $response;
	}

	public static function fetch_file_ms_server( $ms_url = '', $args = array() ) {
		$url        = $ms_url . self::$rest_api_ms_fetch_file;
		$get_result = self::post( $url, $args );

		if ( ! empty( $get_result ) ) {
			return json_decode( $get_result );
		}

		return false;
	}

	public static function extract_file_ms_server( $ms_url = '', $args = array() ) {
		$url        = $ms_url . self::$rest_api_ms_extract_file;
		$get_result = self::post( $url, $args );

		if ( ! empty( $get_result ) ) {
			return json_decode( $get_result );
		}

		return false;
	}

	public static function do_scan( $ms_url = '', $args = array() ) {
		$url        = $ms_url . self::$rest_api_ms_scan;
		$get_result = self::post( $url, $args );

		if ( ! empty( $get_result ) ) {
			return json_decode( $get_result );
		}

		return false;
	}
}
