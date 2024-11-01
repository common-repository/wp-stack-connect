<?php

class WPStack_Connect_Cache_Handle {

	public function clear_cache() {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;

		$cache_enabler_path   = ABSPATH . 'wp-content/cache/cache-enabler/';
		$cache_breeze_path    = ABSPATH . 'wp-content/cache/breeze/';
		$cache_wp_rocket_path = ABSPATH . 'wp-content/cache/wp-rocket/';
		$breeze_plugin_path   = WP_PLUGIN_DIR . '/breeze';

		if ( file_exists( $cache_enabler_path ) ) {
			$wp_filesystem->rmdir( untrailingslashit( $cache_enabler_path ), true );
		}

		if ( file_exists( $cache_breeze_path ) ) {
			$wp_filesystem->rmdir( untrailingslashit( $cache_breeze_path ), true );
		}

		if ( file_exists( $cache_wp_rocket_path ) ) {
			$wp_filesystem->rmdir( untrailingslashit( $cache_wp_rocket_path ), true );
			if ( ! is_dir( $cache_wp_rocket_path ) ) {
				wp_mkdir_p( $cache_wp_rocket_path );
			}
		}

		if ( is_dir( $breeze_plugin_path ) ) {
			if ( is_plugin_active( 'breeze/breeze.php' ) ) {
				$main     = new Breeze_PurgeVarnish();
				$homepage = home_url() . '/?breeze';
				$main->purge_cache( $homepage );
			}
		}

		wpstack_cache_flush();
	}
}
