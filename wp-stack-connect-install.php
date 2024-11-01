<?php
/**
 * WPStack plugin install process
 *
 * @package WPStack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wpstack_env = dirname( __FILE__ ) . '/.env';
if ( ! defined( 'WPSTACK_ENV' ) ) {
	if ( file_exists( $wpstack_env ) ) {
		$env = parse_ini_file( $wpstack_env );
		define( 'WPSTACK_ENV', $env['VERSION'] );
	} else {
		define( 'WPSTACK_ENV', 'prod' );
	}
}

/**
 * Activate WP Stack Connect
 */
function wpstack_activate_plugin() {
	wpstack_update_configuration();
	wpstack_create_table();
	wpstack_backup_dir();
	$connection = new WPStack_Connect_Connection();
	$connection->activate();
	$wpstack_debug_log = new WPStack_Connect_Debug_Log_Core(null, null);
	$wpstack_debug_log->activate();
}

/**
 * Deactivate WP Stack Connect
 */
function wpstack_deactivate_plugin() {
	wpstack_remove_configuration();
	$connection = new WPStack_Connect_Connection();
	$connection->deactivate();
	$wp_config_manager = new WP_Config_Manager;
	$wpstack_debug_log = new WPStack_Connect_Debug_Log_Core(null, $wp_config_manager);
	$wpstack_debug_log->deactivate();
	wpstack_uninstall_backup_components();
	wpstack_remove_cron_job();
}

/**
 * Default configuration WP Stack Connect
 */
function wpstack_update_configuration() {
	wpstack_cache_flush();

	add_option( 'wpstack_connect_client_id', '' );
	add_option( 'wpstack_connect_website_id', '' );
	add_option( 'wpstack_connect_site_url', '' );
	add_option( 'wpstack_connect_return_url', '' );
	add_option( 'wpstack_connect_connected_status', 'disconnect' );
	add_option( 'wpstack_connect_enable_track_log', 0 );
	if ( ! get_option( 'wpstack_connect_message_status' )) {
        add_option( 'wpstack_connect_message_status', 'auto-connect' );
    }

}

/**
 * Update secret key WP Stack Connect
 */
function wpstack_update_secret_key() {
	 $secret_key = get_option( 'wpstack_connect_secret_key' );
	if ( ! $secret_key ) {
		add_option( 'wpstack_connect_secret_key', wpstack_generate_secret_key() );
	} else {
		update_option( 'wpstack_connect_secret_key', wpstack_generate_secret_key() );
	}
}

/**
 * Schedule post status WP Stack Connect
 */
function wpstack_scheduled_post_status() {
	register_post_status(
		'scheduled_wp_stack',
		array(
			'label'                     => _x( 'Scheduled by WPStack', 'post' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'publicly_queryable'        => false,
			'label_count'               => _n_noop( 'Scheduled by WPStack <span class="count">(%s)</span>', 'Scheduled by WPStack <span class="count">(%s)</span>' ),
		)
	);

	register_post_meta(
		'post',
		'_wpstack_schedule',
		array(
			'single'        => true,
			'type'          => 'boolean',
			'show_in_rest'  => true,
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	add_filter( 'display_post_states', 'wpstack_add_schedule_states', 10, 2 );
}

if ( ! function_exists( 'wpstack_add_schedule_states' ) ) {
	/**
	 * Add schedule status WP Stack Connect
	 *
	 * @param array $states states.
	 * @param array $post posts.
	 *
	 * @return array $states
	 */
	function wpstack_add_schedule_states( $states, $post ) {

		if ( ( 'future' === get_post_status( $post->ID ) ) && ( get_post_meta( $post->ID, '_wpstack_schedule', true ) ) ) {
			$states['scheduled'] = __( 'Scheduled by WPStack' );
		}

		return $states;
	}
}

if ( ! function_exists( 'wpstack_cache_flush' ) ) {
	/**
	 * Cache plus WP Stack Connect
	 */
	function wpstack_cache_flush() {
		global $wp_object_cache;

		return $wp_object_cache->flush();
	}
}

if ( ! function_exists( 'wpstack_override_htaccess_bps' ) ) {
	/**
	 * Remove overide htaccess BPS WP Stack Connect
	 */
	function wpstack_override_htaccess_bps() {
		wp_clear_scheduled_hook( 'wpstack_cron_override_htaccess_bps' ); 
	}
}

/**
 * Status into inline edit WP Stack Connect
 */
function wpstack_status_into_inline_edit() {
	echo "<script>
    jQuery(document).ready( function() {
        jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"scheduled_wp_stack\">Scheduled by WPStack</option>' );
    });
    </script>";
}

/**
 * Remove configuration WP Stack Connect
 */
function wpstack_remove_configuration() {
	delete_option( 'wpstack_connect_client_id' );
	delete_option( 'wpstack_connect_website_id' );
	delete_option( 'wpstack_connect_site_url' );
	delete_option( 'wpstack_connect_return_url' );
	delete_option( 'wpstack_connect_connected_status' );
	delete_option( 'wpstack_connect_enable_track_log' );
	delete_option( 'wpstack_connect_message_status' );
}

/**
 * Generate secret key WP Stack Connect
 *
 * @param int $length Length.
 */
function wpstack_generate_secret_key( $length = 32 ) {
	$characters        = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$characters_length = strlen( $characters );
	$random_string     = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$random_string .= $characters[ wp_rand( 0, $characters_length - 1 ) ];
	}
	return $random_string;
}

if ( ! function_exists( 'wpstack_log' ) ) {
	/**
	 * Logger WP Stack Connect
	 *
	 * @param array $log debuging value.
	 */
	function wpstack_log( $logs ) {
		$time_log = '[wpstack-' . gmdate( 'd-m-y h:i:s' ) . '] - [' . get_site_url() . '] ';
		if ( is_array( $logs ) || is_object( $logs ) ) {
			$contents = (array) $logs;
			if (is_array( $contents )) {
				$ignore_requests = ["account", "sig"];
				foreach ( $ignore_requests as $ignore_request ) {
					if (array_key_exists($ignore_request, $contents)) {
						$contents[$ignore_request] = "******************";
					}
				}

				if (isset($contents['params'])) {
					$ignore_custom_params = ["credentials", "bucket", "region", "version", "is_multiregion", "http_url", "ms_server"];
					foreach ($ignore_custom_params as $ignore_custom_param) {
						if (array_key_exists($ignore_custom_param, $contents['params'])) {
							$contents['params'][$ignore_custom_param] = "******************";
						}
					}
				}
			}
			$log_message = $time_log . print_r( $contents, true ) . PHP_EOL;
		} else {
			$log_message = $time_log . $logs . PHP_EOL;
		}

		// if ( get_option( 'wpstack_connect_enable_track_log' ) ) {
		// 	$site_info       = new WPStack_Connect_Site_Info();
		// 	$site_url        = $site_info->siteurl();
		// 	$post_parameters = array(
		// 		'message'	=> $log_message,
		// 		'site_url' 	=> $site_url,
		// 	);
		// 	$connection      = new WPStack_Connect_Connection();
		// 	$request         = new WPStack_Connect_Request_Transfer();
		// 	$request->post( $connection->appurl() . '/plugin/log', $post_parameters );
		// } else {
		// }

		$uploads = wp_upload_dir();
		$wpstack_log_path = $uploads['basedir'] . '/wpstack_debug_log/wpstack_' . gmdate( 'd-m-y' ) . '.log';

		file_put_contents( $wpstack_log_path , $log_message, FILE_APPEND );
	}
}

if ( ! function_exists( 'wpstack_format_size' ) ) {
	/**
	 * Formatsize WP Stack Connect
	 *
	 * @param float $bytes Bytes.
	 */
	function wpstack_format_size( $bytes ) {
		if ( $bytes >= 1073741824 ) {
			$bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
		} elseif ( $bytes >= 1048576 ) {
			$bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
		} elseif ( $bytes >= 1024 ) {
			$bytes = number_format( $bytes / 1024, 2 ) . ' KB';
		} elseif ( $bytes > 1 ) {
			$bytes = $bytes . ' bytes';
		} elseif ( 1 === $bytes ) {
			$bytes = $bytes . ' byte';
		} else {
			$bytes = '0 bytes';
		}

		return $bytes;
	}
}

if ( ! function_exists( 'wpstack_dir' ) ) {
	/**
	 * Default dir WP Stack Connect
	 *
	 * @param string $string_path String Path.
	 */
	function wpstack_dir( $string_path = null ) {
		$plugin_path = plugin_dir_path( __FILE__ );
		if ( $string_path ) {
			$plugin_path = $plugin_path . $string_path;
		}
		return $plugin_path;
	}
}

if ( ! function_exists( 'wpstack_backup_dir' ) ) {
	/**
	 * Backup dir WP Stack Connect
	 */
	function wpstack_backup_dir() {
		$uploads = wp_upload_dir();
		$wpstack_backup_dir = $uploads['basedir'] . '/wpstack_backup/';
		if ( file_exists( $wpstack_backup_dir ) ) {
			wpstack_install_backup_components( $wpstack_backup_dir );
			return $wpstack_backup_dir;
		} else {
			mkdir( $wpstack_backup_dir, 0777 | 0755 );
			wpstack_install_backup_components( $wpstack_backup_dir );
		}
	}
}

if ( ! function_exists( 'wpstack_install_backup_components' ) ) {
	/**
	 * Install backup components WP Stack Connect
	 *
	 * @param string $wpstack_backup_dir Backup dir.
	 */
	function wpstack_install_backup_components( $wpstack_backup_dir ) {
		$wpstack_backup_archive   = $wpstack_backup_dir . 'archive';
		$wpstack_backup_databases = $wpstack_backup_dir . 'databases';
		if ( ! file_exists( $wpstack_backup_archive ) ) {
			mkdir( $wpstack_backup_archive, 0777 | 0755 );
		}

		if ( ! file_exists( $wpstack_backup_databases ) ) {
			mkdir( $wpstack_backup_databases, 0777 | 0755 );
		}
	}
}

if ( ! function_exists( 'wpstack_uninstall_backup_components' ) ) {
	/**
	 * Uninstall backup components WP Stack Connect
	 */
	function wpstack_uninstall_backup_components() {
		$uploads = wp_upload_dir();
		if ( file_exists( $uploads['basedir'] . '/wpstack_backup/' ) ) {
			wpstack_rmdir( $uploads['basedir'] . '/wpstack_backup/' );
		}
	}
}

if ( ! function_exists("wpstack_rmdir") ) {
	/**
	 * Recursive remove dir WP Stack Connect
	 *
	 * @param string $dir Directory path.
	 */
	function wpstack_rmdir( $dir ) {
		if ( is_dir( $dir ) ) {
			$objects = scandir( $dir );
			foreach ( $objects as $object ) {
				if ( '.' !== $object && '..' !== $object ) {
					if ( is_dir( $dir . DIRECTORY_SEPARATOR . $object ) && ! is_link( $dir . '/' . $object ) ) {
						wpstack_rmdir( $dir . DIRECTORY_SEPARATOR . $object );
					} else {
						unlink( $dir . DIRECTORY_SEPARATOR . $object );
					}
				}
			}
			rmdir( $dir );
		}
	}
}

if ( ! function_exists( 'wpstack_create_table' ) ) {
	/**
	 * Create table WP Stack Connect
	 */
	function wpstack_create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpstack_connect_activity_log';
		if (
			$wpdb->get_var( 
				$wpdb->prepare("SHOW TABLES LIKE %s", $table_name)
			) !== $table_name
		) {
			$create_table_query = "CREATE TABLE $table_name (
                `alid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `id` int(4) NOT NULL DEFAULT '0000',
                `severity` varchar(50) NOT NULL,
                `modified_date` int(11) NOT NULL DEFAULT '0',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `user_name` varchar(50) NOT NULL,
                `user_role` varchar(50) NOT NULL,
                `user_email` varchar(50) NOT NULL,
                `user_avatar` varchar(250) NOT NULL,
                `user_bio` text NULL,
                `ip_address` varchar(50) NOT NULL DEFAULT '127.0.0.1',
                `object_type` varchar(50) NOT NULL DEFAULT 'post',
                `action` varchar(50) NOT NULL,
                `object_id` int(20) unsigned NOT NULL DEFAULT '0',
                `description` text NOT NULL,
                PRIMARY KEY (alid)
            )";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $create_table_query );
		} else {
			$delete_old	= 'DELETE FROM `' . $table_name . '`';
			$wpdb->get_results( $delete_old );
		}

		$table_name2 = $wpdb->prefix . 'wpstack_connect_auto_links';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name2'" ) !== $table_name2 ) {
			$create_table2_query = "CREATE TABLE $table_name2 (
                `alid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `link` text NOT NULL,
                `keyword` text NOT NULL,
                `added` longtext,
                `psid` longtext,
                `titlink` longtext,
                `status` int DEFAULT 1,
                PRIMARY KEY (alid)
            )";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $create_table2_query );
		}
	}
}

if (!function_exists('wpstack_zip_archive_checking')) {
	function wpstack_zip_archive_checking()
	{
		if (class_exists('ZipArchive')) {
			return true;
		}

		return false;
	}
}

if (!function_exists('wpstack_explode_serv')) {
	function wpstack_explode_serv($ws_serv)
	{
		foreach ( explode(PHP_EOL, $ws_serv) as $item )  {
			$itemData = explode(":",$item);
			if ( count($itemData) == 2 )    {
				$out[trim($itemData[0])] = trim($itemData[1]);
			}
		}

		return $out;
	}
}

if ( ! function_exists( 'wpstack_compare_version' ) ) {

	function wpstack_compare_version( $local_ver, $update_ver ) {
		if ( $local_ver && $update_ver ) {
			if ( $update_ver > $local_ver ) {
				return true;
			}
		}
		
		return false;
	}
}

if ( ! function_exists( 'wpstack_remove_cron_job' ) ) {

	function wpstack_remove_cron_job() {
		wp_clear_scheduled_hook( 'wpstack_cron_added_links' );
		wp_clear_scheduled_hook( 'wpstack_cron_delete_links' );
		wp_clear_scheduled_hook( 'wpstack_cron_override_htaccess_bps' );
		wp_clear_scheduled_hook( 'wpstack_cron_send_sys_info' );
		wp_clear_scheduled_hook( 'wpstack_delay_send_client_web_data' );
	}
}