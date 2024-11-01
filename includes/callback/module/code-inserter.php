<?php

class WPStack_Connect_Code_Inserter {

	public $account;

	function __construct( $callback_handler ) {
		$this->account = $callback_handler->account;
	}

	public function get() {
		$result = $this->get_option_data();
		wp_send_json( $result );
	}

	public function update( $parameters ) {
		$header_scripts = isset( $parameters['header_scripts'] ) ? sanitize_textarea_field( $parameters['header_scripts'] ) : '';
		$footer_scripts = isset( $parameters['footer_scripts'] ) ? sanitize_textarea_field( $parameters['footer_scripts'] ) : '';
		$body_scripts   = isset( $parameters['body_scripts'] ) ? sanitize_textarea_field( $parameters['body_scripts'] ) : '';
	
		update_option( 'wpstack_connect_header_code', $header_scripts );
		update_option( 'wpstack_connect_footer_code', $footer_scripts );
	
		if ( function_exists( 'wp_body_open' ) && version_compare( get_bloginfo( 'version' ), '5.2', '>=' ) ) {
			update_option( 'wpstack_connect_body_code', $body_scripts );
		} else {
			wp_send_json_error( __( 'Sorry, your website does not support putting code in the <body> tag' ) );
		}

		$result = array(
			'success' => true,
			'code'    => $this->get_option_data(),
		);

		wp_send_json( $result );
	}

	private function get_option_data() {
		$wpstack_connect_header_code = wp_unslash( get_option( 'wpstack_connect_header_code' ) );
		$wpstack_connect_body_code   = wp_unslash( get_option( 'wpstack_connect_body_code' ) );
		$wpstack_connect_footer_code = wp_unslash( get_option( 'wpstack_connect_footer_code' ) );

		$result = array(
			'wpstack_connect_header_code' => $wpstack_connect_header_code,
			'wpstack_connect_body_code'   => $wpstack_connect_body_code,
			'wpstack_connect_footer_code' => $wpstack_connect_footer_code,
		);

		return $result;
	}

	public function process( $request ) {
		switch ( $request->method ) {
			case 'get':
				$this->get();
				break;

			case 'update':
				$this->update( $request->params );
				break;

			default:
				break;
		}
	}
}
