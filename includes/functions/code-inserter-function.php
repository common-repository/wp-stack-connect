<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpstack_get_option' ) ) {

	function wpstack_get_option( $option ) {
		$value = get_option( $option );
		if ( empty( $value ) ) {
			return;
		}
		if ( '' === trim( $value ) ) {
			return;
		}

		return wp_unslash( $value );
	}
}

if ( ! function_exists( 'wpstack_put_header_code' ) ) {

	function wpstack_put_header_code() {
		wpstack_get_option( 'wpstack_connect_header_code' );
	}
}

if ( ! function_exists( 'wpstack_put_body_code' ) ) {
	function wpstack_put_body_code() {
		wpstack_get_option( 'wpstack_connect_body_code' );
	}
}

if ( ! function_exists( 'wpstack_put_footer_code' ) ) {

	function wpstack_put_footer_code() {
		wpstack_get_option( 'wpstack_connect_footer_code' );
	}
}


add_action( 'wp_head', 'wpstack_put_header_code' );
add_action( 'wp_footer', 'wpstack_put_footer_code' );
if ( function_exists( 'wp_body_open' ) && version_compare( get_bloginfo( 'version' ), '5.2', '>=' ) ) {
	add_action( 'wp_body_open', 'wpstack_put_body_code' );
}
