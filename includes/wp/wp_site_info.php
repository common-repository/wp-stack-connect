<?php

class WPStack_Connect_Site_Info {

	public function basic( &$info ) {
		$info['wpurl']   	= esc_url_raw( $this->wpurl() );
		$info['siteurl'] 	= esc_url_raw( $this->siteurl() );
		$info['homeurl'] 	= esc_url_raw( $this->homeurl() );
		$info['serverip']	= isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( $_SERVER['SERVER_ADDR'] ) : '';
		$info['abspath'] 	= trailingslashit( ABSPATH );
	}

	public function wpurl() {
		if ( function_exists( 'network_site_url' ) ) {
			return network_site_url();
		} else {
			return get_bloginfo( 'wpurl' );
		}
	}

	public function siteurl() {
		if ( function_exists( 'site_url' ) ) {
			return site_url();
		} else {
			return get_bloginfo( 'wpurl' );
		}
	}

	public function homeurl() {
		if ( function_exists( 'home_url' ) ) {
			return home_url();
		} else {
			return get_bloginfo( 'url' );
		}
	}
}
