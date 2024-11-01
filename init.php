<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Plugin Name: WP Stack Connect
 * Plugin URI: https://my.wp-stack.co/
 * Description: WP-Stack is an all-in-one solution for managing your website. With this powerful dashboard, you can easily publish content from google docs or Docx files, manage your social media accounts, and perform daily malware scans, backups, and site audits. Additionally, WP-Stack provides valuable insights into your website's SEO, analytics, uptime, performance, and activity logs, allowing you to keep track of your site's health.
 * Version: 1.0.1
 * Author: wp-stack.co
 * Author URI: https://wp-stack.co/
 * License: GPL2
 * Text Domain: wp-stack-connect
 * Network: true
 *
 * @package WP-Stack-Connect
 */

require_once dirname( __FILE__ ) . '/includes/account.php';
require_once dirname( __FILE__ ) . '/includes/wp/wp_settings.php';
// require_once dirname( __FILE__ ) . '/includes/wp/wpstack_connect_puc.php';
require_once dirname( __FILE__ ) . '/includes/wp/wp_site_info.php';
require_once dirname( __FILE__ ) . '/includes/wp/wp_config_manager.php';
require_once dirname( __FILE__ ) . '/includes/callback/class-wp-stack-connect-request.php';

if(!function_exists('wp_get_current_user'))
{
	require_once ABSPATH . "wp-includes/pluggable.php"; 
}

// $wpstack_puc = new WPStack_Connect_Puc();
// $wpstack_puc->checking( __FILE__ );

foreach ( glob( dirname( __FILE__ ) . '/includes/core/*.php' ) as $filename ) {
	include_once $filename;
}

foreach ( glob( dirname( __FILE__ ) . '/includes/functions/*.php' ) as $filename ) {
	include_once $filename;
}

require_once dirname( __FILE__ ) . '/wp-stack-connect-install.php';
require_once dirname( __FILE__ ) . '/includes/configuration/class-wp-stack-connect-configuration.php';
register_activation_hook( __FILE__, 'wpstack_activate_plugin' );
register_deactivation_hook( __FILE__, 'wpstack_deactivate_plugin' );
add_action( 'plugins_loaded', 'wpstack_init', 0 );


foreach ( glob( dirname( __FILE__ ) . '/includes/manage/*.php' ) as $filename ) {
	include_once $filename;
}

if ( ! function_exists( 'wpstack_service' ) ) {

	function wpstack_service() {

        $settings   = new WPStack_Connect_Wp_Settings();
        $site_info  = new WPStack_Connect_Site_Info();
        $_request   = [];

        if ( ( array_key_exists( 'wsparamsmerge', $_POST ) ) || ( array_key_exists( 'wsparamsmerge', $_GET ) ) ) {

            // $_request['params']         = isset( $_POST['params'] ) ? filter_input(INPUT_POST, 'params', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY) : filter_input(INPUT_GET, 'params', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
            $_request['params']         = isset( $_POST['params'] ) ? $_POST['params'] : $_GET['params'];
            $_request['sig']            = isset( $_POST['sig'] ) ? sanitize_text_field( $_POST['sig'] ) : sanitize_text_field( $_GET['sig'] );
            $_request['method']         = isset( $_POST['method'] ) ? sanitize_text_field( $_POST['method'] ) : sanitize_text_field( $_GET['method'] );
            $_request['is_sha1']        = isset( $_POST['is_sha1'] ) ? (bool) $_POST['is_sha1'] : (bool) $_GET['is_sha1'];
            $_request['module']         = isset( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : sanitize_text_field( $_GET['module'] );
            $_request['subs']           = isset( $_POST['subs'] ) ? sanitize_text_field( $_POST['subs'] ) : sanitize_text_field( $_GET['subs'] );
            $_request['pluginname']     = isset( $_POST['pluginname'] ) ? sanitize_text_field( $_POST['pluginname'] ) : sanitize_text_field( $_GET['pluginname'] );
            $_request['pubkey']         = isset( $_POST['pubkey'] ) ? sanitize_text_field( $_POST['pubkey'] ) : sanitize_text_field( $_GET['pubkey'] );
        }

        if ( ( array_key_exists( 'pluginname', $_request ) ) && ( 'wpstack' === sanitize_text_field( wp_unslash( $_request['pluginname'] ) ) ) ) {
            require_once dirname( __FILE__ ) . '/includes/callback/base.php';
            require_once dirname( __FILE__ ) . '/includes/callback/response.php';
            require_once dirname( __FILE__ ) . '/includes/callback/request.php';
            require_once dirname( __FILE__ ) . '/includes/callback/module/connection.php';

            $public_key = WPStack_Connect_Account::sanitize_key( sanitize_text_field( wp_unslash( $_request['pubkey'] ) ) );
            $account    = WPStack_Connect_Account::find( $settings, $public_key );
            $request    = new WPStack_Connect_Request( $account, $_request );
            $response   = new WPStack_Connect_Response();

            if ( 'connection' === $request->module && isset( $account->public, $account->secret ) ) {
                $connection = new WPStack_Connect_Manage_Connection( $account, $settings, $response, $request );
                $connection->execute();
            } elseif ( 'connection' === $request->module && 'disconnect' === $request->method ) {
                $resp = array(
                    $request->module => array(
                        $request->method => array(
                            'message' => esc_html__( 'Disconnect success' ),
                            'code'    => 200,
                        ),
                    ),
                );
                $response->add_status( 'callbackresponse', $resp );
                $response->terminate();
            }

            if ( $account && ( 1 === $account->authenticate( $request ) ) ) {
                require_once dirname( __FILE__ ) . '/includes/callback/handler.php';

                $callback_handler = new WPStack_Connect_Callback_Handler( $settings, $site_info, $request, $account, $response );
                $callback_handler->execute();

            } else {
                $resp = array(
                    $request->module => array(
                        $request->method => array(
                            'message' => esc_html__( 'Validate connection failed' ),
                            'code'    => 403,
                        ),
                    ),
                );
                $response->add_status( 'callbackresponse', $resp );
                $response->terminate();
            }
        }
    }
}

/**
 * Initial WP-Stack Plugin
 */
function wpstack_init() {
	$config = new WPStack_Connect_Configuration();
	add_action( 'init', 'wpstack_scheduled_post_status' );
	add_action( 'init', 'wpstack_override_htaccess_bps' );
    add_action( 'init', 'wpstack_service' );
	add_action( 'admin_footer-edit.php', 'wpstack_status_into_inline_edit' );
}
