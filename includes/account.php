<?php

class WPStack_Connect_Account {

	public $settings;
	public $secret;
	public static $accounts_list = 'WPStack_Connect_Account';
	public $sig_match;
	public $public;
	public $user_id;

	function __construct( $settings, $public, $secret, $user_id ) {
		$this->settings = $settings;
		$this->public   = $public;
		$this->secret   = $secret;
		$this->user_id  = $user_id;
	}

	public static function find( $settings, $public ) {
		$accounts = self::get_account( $settings );
		if ( array_key_exists( $public, $accounts ) && isset( $accounts[ $public ]['secret'] ) ) {
			$secret  = $accounts[ $public ]['secret'];
			$user_id = $accounts[ $public ]['user_id'];
		}
		if ( empty( $secret ) || ( strlen( $secret ) < 32 ) ) {
			return null;
		}
		return new self( $settings, $public, $secret, $user_id );
	}

	public static function rand_string( $length ) {
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$str  = '';
		$size = strlen( $chars );
		for ( $i = 0; $i < $length; $i++ ) {
			$str .= $chars[ rand( 0, $size - 1 ) ];
		}
		return $str;
	}

	public static function sanitize_key( $key ) {
		return preg_replace( '/[^a-zA-Z0-9_\-]/', '', $key );
	}

	public static function get_account( $settings ) {
		$accounts = $settings->get_option( self::$accounts_list );
		if ( ! is_array( $accounts ) ) {
			$accounts = array();
		}
		return $accounts;
	}

	public static function remove( $settings ) {
		self::update( $settings, array() );
		return 1;
	}

	public function authenticate( $request ) {
		$this->sig_match = self::get_sig_match( $request, $this->secret );
		if ( $this->sig_match !== $request->sig ) {
			return false;
		}
		return 1;
	}

	public static function get_sig_match( $request, $secret ) {
		$module = $request->module;
		$method = $request->method;
		if ( $request->is_sha1 ) {
			$sig_match = sha1( $module . $method . $secret );
		} else {
			$sig_match = md5( $module . $method . $secret );
		}
		return $sig_match;
	}

	public static function add_account( $settings, $public, $secret ) {
		$accounts = self::get_account( $settings );
		$user     = self::get_user();
		if ( count( $accounts ) == 0 ) {
			$accounts[ $public ] = array(
				'secret'  => $secret,
				'user_id' => str_replace( '=', '', base64_encode( $user->data->ID ) ),
			);
		}
		self::update( $settings, $accounts );
	}

	protected static function get_user() {
		if ( is_user_logged_in() ) {
			return wp_get_current_user();
		}
	}

	public static function update( $settings, $get_account ) {
		$settings->update_option( self::$accounts_list, $get_account );
	}

	public function info() {
		return array(
			'public'   => substr( $this->public, 0, 6 ),
			'sigmatch' => substr( $this->sig_match, 0, 6 ),
		);
	}

	public static function exists( $settings, $pubkey ) {
		$accounts = self::get_account( $settings );
		return array_key_exists( $pubkey, $accounts );
	}
}
