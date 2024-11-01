<?php

class WPStack_Connect_Callback_Base {

	public function object_to_array( $obj ) {
		return json_decode( json_encode( $obj ), true );
	}

	public function base64_encode( $data, $chunk_size ) {
		if ( $chunk_size ) {
			$out = '';
			$len = strlen( $data );
			for ( $i = 0; $i < $len; $i += $chunk_size ) {
				$out .= base64_encode( substr( $data, $i, $chunk_size ) );
			}
		} else {
			$out = base64_encode( $data );
		}
		return $out;
	}
}
