<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPStack_Connect_Response extends WPStack_Connect_Callback_Base {
	public $status;

	public function __construct() {
		$this->status = array();
	}

	public function add_status( $key, $value ) {
		$this->status[ $key ] = $value;
	}

	public function add_array_to_status( $key, $value ) {
		if ( ! isset( $this->status[ $key ] ) ) {
			$this->status[ $key ] = array();
		}
		$this->status[ $key ][] = $value;
	}

	public function terminate( $resp = array() ) {
		$resp     = array_merge( $this->status, $resp );
		$response = json_encode( $resp );
		die( $response );

		exit;
	}
}
