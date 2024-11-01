<?php
/**
 * WP-Stack plugin uninstall process
 *
 * @package WP-Stack-Connect
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'wpstack_connect_secret_key' );
delete_option( 'wpstack_connect_client_id' );
delete_option( 'wpstack_connect_website_id' );
delete_option( 'wpstack_connect_site_url' );
delete_option( 'wpstack_connect_return_url' );
delete_option( 'wpstack_connect_connected_status' );
delete_option( 'wpstack_connect_header_code' );
delete_option( 'wpstack_connect_body_code' );
delete_option( 'wpstack_connect_footer_code' );
delete_option( 'wpstack_connect_delay_request' );
delete_option( 'wpstack_connect_enable_track_log' );
delete_option( 'wpstack_connect_website_subscription' );
delete_option( 'wpstack_connect_login_attempts' );
delete_option( 'wpstack_connect_blocked_ips' );
delete_option( 'wpstack_connect_blocked_settings' );
delete_option( 'wpstack_connect_last_sent_wp_info' );
delete_option( 'wpstack_connect_message_status' );

wp_clear_scheduled_hook( 'wpstack_cron_added_links' );
wp_clear_scheduled_hook( 'wpstack_cron_delete_links' );
wp_clear_scheduled_hook( 'wpstack_cron_override_htaccess_bps' );
wp_clear_scheduled_hook( 'wpstack_cron_send_sys_info' );
wp_clear_scheduled_hook( 'wpstack_delay_send_client_web_data' );

global $wpdb;
$wpdb->query( $wpdb->prepare("DROP TABLE IF EXISTS %s", "{$wpdb->prefix}wpstack_connect_activity_log") );
$wpdb->query( $wpdb->prepare("DROP TABLE IF EXISTS %s", "{$wpdb->prefix}wpstack_connect_auto_links"));
