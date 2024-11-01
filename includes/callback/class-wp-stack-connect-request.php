<?php

class WPStack_Connect_Request_Transfer {
	/**
	 *
	 * @param  string $url             - url.
	 * @param  array  $post_parameters - post parameters.
	 * @return boolean|string
	 */
	public static function post( $url, $post_parameters ) {
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
				'timeout' 	  => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response = wp_remote_retrieve_body( $response );

		return $response;
	}
}



