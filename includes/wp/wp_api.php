<?php

class WPStack_Connect_Wp_Api {


	public $settings;

	function __construct( $settings ) {
		$this->settings = $settings;
	}

	public function ping_ws() {

	}

	public function do_request( $method, $body, $pubkey ) {
		$account = Ms_Account::find( $this->settings, $pubkey );
		if ( isset( $account ) ) {
			$url = $account->authenticatedUrl( $method );
			return $this->http_request( $url, $body );
		}
	}

	/**
	 *
	 * @param  string $url             - url.
	 * @param  array  $post_parameters - post parameters.
	 * @return boolean|string
	 */
	public static function http_request( $url, $post_parameters ) {
		$parameters = array(
			'status' => 200,
			'data'   => $post_parameters,
		);

		$response = wp_remote_post(
			$url,
			array(
				'headers'     => array(
					'content-type' => 'application/json',
				),
				'data_format' => 'body',
				'body'        => wp_json_encode( $parameters ),
			)
		);

		if ( is_wp_error( $response ) ) {
			wpstack_log( $response );
			return false;
		}

		$response = wp_remote_retrieve_body( $response );

		return $response;
	}
}
