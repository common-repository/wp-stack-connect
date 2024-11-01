<?php

class WPStack_Connect_Wp_Settings {

	public function get_option( $key ) {
		$res = false;
		if ( function_exists( 'get_site_option' ) ) {
			$res = get_site_option( $key, false );
		}
		if ( $res === false ) {
			$res = get_option( $key, false );
		}
		return $res;
	}

	public function delete_option( $key ) {
		if ( function_exists( 'delete_site_option' ) ) {
			return delete_site_option( $key );
		} else {
			return delete_option( $key );
		}
	}

	public function update_option( $key, $value ) {
		if ( function_exists( 'update_site_option' ) ) {
			return update_site_option( $key, $value );
		} else {
			return update_option( $key, $value );
		}
	}

	public function get_options( $options = array() ) {
		$result = array();

		foreach ( $options as $option ) {
			$result[ $option ] = $this->get_option( $option );
		}

		return $result;
	}

	public function update_options( $args ) {
		$result = array();

		foreach ( $args as $option => $value ) {
			$this->update_option( $option, $value );
			$result[ $option ] = $this->get_option( $option );
		}

		return $result;
	}

	public function delete_options( $options ) {
		$result = array();

		foreach ( $options as $option ) {
			$this->delete_option( $option );
			$result[ $option ] = ! $this->get_option( $option );
		}

		return $result;
	}

	public function set_transient( $name, $value, $time ) {
		if ( function_exists( 'set_site_transient' ) ) {
			return set_site_transient( $name, $value, $time );
		}
		return false;
	}

	public function delete_transient( $name ) {
		if ( function_exists( 'delete_site_transient' ) ) {
			return delete_site_transient( $name );
		}
		return false;
	}

	public function get_transient( $name ) {
		if ( function_exists( 'get_site_transient' ) ) {
			return get_site_transient( $name );
		}
		return false;
	}
}



