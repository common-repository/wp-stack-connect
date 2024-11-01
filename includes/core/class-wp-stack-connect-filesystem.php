<?php

class WPStack_Connect_Filesystem {


	protected $backup_exclude_table = array();

	protected $default_ignore_directories = array(
		'wp-content/managewp/backups',
		'wp-content/iwp_backups',
		'wp-content/infinitewp',
		'wp-content/mwp_backups',
		'wp-content/backupwordpress',
		'wp-content/contents/cache',
		'wp-content/content/cache',
		'wp-content/cache',
		'wp-content/logs',
		'wp-content/old-cache',
		'wp-content/w3tc',
		'wp-content/cmscommander/backups',
		'wp-content/gt-cache',
		'wp-content/wfcache',
		'wp-content/widget_cache',
		'wp-content/bps-backup',
		'wp-content/old-cache',
		'wp-content/updraft',
		'wp-content/nfwlog',
		'wp-content/upgrade',
		'wp-content/wflogs',
		'wp-content/tmp',
		'wp-content/backups',
		'wp-content/updraftplus',
		'wp-content/wishlist-backup',
		'wp-content/wptouch-data/infinity-cache/',
		'wp-content/Dropbox_Backup',
		'wp-content/backup-db',
		'wp-content/updraft',
		'wp-content/uploads/wp-clone',
		'wp-content/uploads/db-backup',
		'wp-content/uploads/ithemes-security/backups',
		'wp-content/uploads/mainwp/backup',
		'wp-content/uploads/backupbuddy_backups',
		'wp-content/uploads/vcf',
		'wp-content/uploads/pb_backupbuddy',
		'wp-content/uploads/sucuri',
		'wp-content/uploads/aiowps_backups',
		'wp-content/uploads/gravity_forms',
		'wp-content/uploads/mainwp',
		'wp-content/uploads/snapshots',
		'wp-content/uploads/wp-clone',
		'wp-content/uploads/wp_system',
		'wp-content/uploads/wpcf7_captcha',
		'wp-content/uploads/wc-logs',
		'wp-content/uploads/siteorigin-widgets',
		'wp-content/uploads/wp-hummingbird-cache',
		'wp-content/uploads/wp-security-audit-log',
		'wp-content/uploads/freshizer',
		'wp-content/uploads/report-cache',
		'wp-admin/error_log',
		'wp-admin/php_errorlog',
		'dbcache',
		'pgcache',
		'objectcache',
		'cgi-bin',
		'/backup/',
		'/backups/',
		'/upgrade/',
		'/logs/',
		'/wpstack_backup/',
		'.wp-cli',
		'.tmb',
		'rankmath',
		'.well-known',
		'.git',
		'/wp-content/swift-ai/'
	);

	protected $default_ignore_files = array(
		"backups_",
		"backup_",
		"logs_",
		"log_",
		"Log_",
		"Logs_",
		'wp-content/mysql.sql',
		'wp-content/DE_clTimeTaken.php',
		'wp-content/DE_cl.php',
		'wp-content/DE_clMemoryPeak.php',
		'wp-content/DE_clMemoryUsage.php',
		'wp-content/DE_clCalledTime.php',
		'wp-content/DE_cl_func_mem.php',
		'wp-content/DE_cl_func.php',
		'wp-content/DE_cl_server_call_log_wptc.php',
		'wp-content/DE_cl_dev_log_auto_update.php',
		'wp-content/DE_cl_dev_log_auto_update.txt',
		'wp-tcapsule-bridge.zip',
	);

	protected $default_wordpress_file_path = array(
		'wp-admin',
		'wp-content',
		'wp-includes',
		'index.php',
		'readme.html',
		'license.txt',
		'wp-activate.php',
		'wp-blog-header.php',
		'wp-comments-post.php',
		'wp-config-sample.php',
		'wp-config.php',
		'wp-cron.php',
		'wp-links-opml.php',
		'wp-load.php',
		'wp-login.php',
		'wp-mail.php',
		'wp-settings.php',
		'wp-signup.php',
		'wp-trackback.php',
		'xmlrpc.php',
		'.htaccess',
		'wp-salt.php',
		'wordfence-waf.php',
		'malcare-waf.php',
	);

	protected $core_info, $plugin_info, $theme_info;

	public function stream_chunk( $origin_path, $destination_path, $origin_filesize ) {
		$chunk_size   = 5 * 1024;
		$upload_start = 0;

		$handle = fopen( $origin_path, 'rb' );
		$fp     = fopen( $destination_path, 'w' );

		try {

			while ( $upload_start < $origin_filesize ) {

				$contents = fread( $handle, $chunk_size );
				fwrite( $fp, $contents );

				$upload_start += strlen( $contents );
				fseek( $handle, $upload_start );
			}

			fclose( $handle );
			fclose( $fp );

			return 200;

		} catch ( \Exception $e ) {
			return 400;
		}
	}

	public function validate_db_backup( $db_backup_dir ) {
		$list_db_backup_files = scandir( $db_backup_dir );
		$sum_size             = 0;
		$status               = false;
		foreach ( $list_db_backup_files as $list_db_backup_file ) {
			if ( $list_db_backup_file != '.' && $list_db_backup_file != '..' ) {
				$sum_size = $sum_size + filesize( $db_backup_dir . $list_db_backup_file );
				if ( ( $sum_size < $this->get_maximum_memory_limit() ) && ( $sum_size > ( $this->get_maximum_memory_limit() - ( 60 * 1024 * 1024 ) ) ) ) {
					$status = true;
				}
			}
		}

		return $status;
	}

	public function generate_backup_filename( $filename ) {
		$last_chunk = substr( $filename, -2 );
		$last_chunk++;
		$new_name = substr( $filename, 0, -2 );
		return $new_name . sprintf( '%02s', $last_chunk );
	}

	public function wordpress_stats() {
		global $wp_version, $wpdb;
		$this->reload();

		$scan_files            = $this->scan_files( realpath( ABSPATH ), true );
		$scan_file_array_split = $this->get_path_arr( $scan_files );
		$path_size             = $this->get_path_size( $scan_files );
		$table_array           = $this->get_list_tables();

		$file_sync['count'] = array(
			'original' => count( $scan_file_array_split['exclude'] ) + count( $scan_file_array_split['include'] ),
			'filter'   => count( $scan_file_array_split['include'] ),
		);

		$file_sync['size'] = array(
			'original' => $path_size['include'] + $path_size['exclude'],
			'filter'   => $path_size['include'],
		);

		$table_sync['count'] = array(
			'original' => count( $table_array['include'] ) + count( $table_array['exclude'] ),
			'filter'   => count( $table_array['include'] ),
		);

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$count_users = count_users();

		$statistics['wp_version']      = $wp_version;
		$current_theme                 = $this->get_current_theme( $wp_version );
		$statistics['active_theme']    = $current_theme['Name'] . ' v' . $current_theme['Version'];
		$statistics['active_plugins']  = count( get_option( 'active_plugins' ) );
		$statistics['total_plugins']   = sizeof( get_plugins() );
		$statistics['active_users']    = isset( $count_users['total_users'] ) ? $count_users['total_users'] : 0;
		$statistics['total_users']     = isset( $count_users['total_users'] ) ? $count_users['total_users'] : 0;
		$statistics['published_posts'] = (int) $wpdb->get_var( 
			$wpdb->prepare("SELECT COUNT(ID) FROM %i WHERE post_type='post' AND post_status='publish'", $wpdb->posts)
		);
		$statistics['phpinfo']         = array(
			'phpversion'          => phpversion(),
			'memory_limit'        => ini_get( 'memory_limit' ),
			'max_execution_time'  => ini_get( 'max_execution_time' ),
			'max_file_uploads'    => ini_get( 'max_file_uploads' ),
			'max_input_time'      => ini_get( 'max_input_time' ),
			'max_input_vars'      => ini_get( 'max_input_vars' ),
			'output_buffering'    => ini_get( 'output_buffering' ),
			'upload_max_filesize' => ini_get( 'upload_max_filesize' ),
		);
		$statistics['synced']          = array(
			'files'   => $file_sync,
			'tables'  => $table_sync,
			// 'plugins' => $this->get_plugin_informations(),
			// 'themes'  => $this->get_theme_informations(),
		);
		$statistics['db_info']         = array(
			'db_host' => DB_HOST,
			'db_user' => DB_USER,
			'db_pass' => DB_PASSWORD,
			'db_name' => DB_NAME,
		);

		$statistics['count'] = array(
			'posts'        => array_sum( (array) wp_count_posts( $type = 'post' ) ),
			'comments'     => $this->get_count_comments( get_posts() ),
			'files'        => array_sum( (array) wp_count_attachments() ),
			'themes'       => sizeof( wp_get_themes() ),
			'plugins'      => sizeof( get_plugins() ),
			'user_checked' => isset( $count_users['total_users'] ) ? $count_users['total_users'] : 0,
		);

		return $statistics;
	}

	protected function get_memory_limit() {
		 $memory_limit = ini_get( 'memory_limit' );
		if ( preg_match( '/^(\d+)(.)$/', $memory_limit, $matches ) ) {
			if ( $matches[2] == 'G' ) {
				$memory_limit = $matches[1] * 1024 * 1024 * 1024;
			} elseif ( $matches[2] == 'M' ) {
				$memory_limit = $matches[1] * 1024 * 1024;
			} elseif ( $matches[2] == 'K' ) {
				$memory_limit = $matches[1] * 1024;
			}
		}

		return $memory_limit;
	}

	protected function get_maximum_memory_limit() {
		$max_memory_limit = $this->parse_size("400M");
		$memory_limit     = $this->get_memory_limit();

		if ( ini_get( 'max_execution_time' ) >= 121 ) {
			if ( $memory_limit < $max_memory_limit ) {
				$max_memory_limit = $memory_limit - $this->parse_size('90M');
			}
		} elseif ( ini_get( 'max_execution_time' ) >= 90 ) {
			$max_memory_limit = $max_memory_limit - $this->parse_size('200M');
			if ( $memory_limit < $max_memory_limit ) {
				$max_memory_limit = $memory_limit - $this->parse_size('60M');
			}
		} elseif ( ini_get( 'max_execution_time' ) >= 60 ) {
			$max_memory_limit = $max_memory_limit - $this->parse_size('300M');
			if ( $memory_limit < $max_memory_limit ) {
				$max_memory_limit = $memory_limit - $this->parse_size('40M');
			}
		} elseif ( ini_get( 'max_execution_time' ) >= 30 ) {
			$max_memory_limit = $max_memory_limit - $this->parse_size('350M');
			if ( $memory_limit < $max_memory_limit ) {
				$max_memory_limit = $memory_limit - $this->parse_size('10M');
			}
		}

		return $max_memory_limit;
	}

	protected function split_dir_item_count() {
		$split_dir_item_count = 1500;
		if ( ini_get( 'max_execution_time' ) >= 120 ) {
			$split_dir_item_count = 15000;
		} elseif ( ini_get( 'max_execution_time' ) >= 90 ) {
			$split_dir_item_count = 10000;
		} elseif ( ini_get( 'max_execution_time' ) >= 60 ) {
			$split_dir_item_count = 5000;
		} elseif ( ini_get( 'max_execution_time' ) >= 30 ) {
			$split_dir_item_count = 2500;
		}

		return $split_dir_item_count;
	}

	public function extract_file_ms_server( $params ) {
		$args = array(
			'timeout' => 45,
			'body'    => array(
				'download_url' => esc_url_raw( $params['download_url'] ),
				'domain'       => sanitize_text_field( $_SERVER['SERVER_NAME'] ),
			),
		);

		$result = WPStack_Connect_Core::extract_file_ms_server( $params['ms_server'], $args );
		return $result;
	}

	public function fetch_file_ms_server( $params ) {
		$args = array(
			'timeout' => 45,
			'body'    => array(
				'download_url' => esc_url_raw( $params['download_url'] ),
				'domain'       => sanitize_text_field( $_SERVER['SERVER_NAME'] ),
			),
		);

		$result = WPStack_Connect_Core::fetch_file_ms_server( $params['ms_server'], $args );
		return $result;
	}

	public function generate_filepath_details( $path_details, $prefix ) {
		$file_path = wpstack_backup_dir() . '/json_file/' . $prefix . '_path_details.json';

		try {
			if ( file_exists( $file_path ) ) {
				$this->remove_files( array( $file_path ) );
			}

			$handle = fopen( wpstack_backup_dir() . '/json_file/' . $prefix . '_path_details.json', 'w' );

			if ( is_resource( $handle ) ) {
				fwrite( $handle, json_encode( $path_details ) );
			}

			fclose( $handle );
		} catch ( \Exception $e ) {
			$error_message = 'Generate filepath details error => ' . $e->getMessage() . ' at file : ' . $e->getFile() .
			' at line : ' . $e->getLine();
			wpstack_log( 'Error generate filepath details => ' . json_encode( $error_message ) );

			return new WP_Error(
				'Generate filepath details error',
				array(
					'error'  => $error_message,
					'status' => 403,
				)
			);
		}

		return array(
			'message'   => 'Generate file path detail successful',
			'status'    => true,
			'file_path' => $file_path,
		);
	}

	public function generate_tables_split( $prefix ) {
		global $wpdb;

		$split_tables = array();
		$inc          = 0;
		$sum_record   = 0;
		$tables       = $wpdb->get_col( 'SHOW TABLES' );
		$file_path    = wpstack_backup_dir() . '/json_file/' . $prefix . '_database_split.json';

		foreach ( $tables as $table ) {
			$record_count = $wpdb->get_var( $wpdb->prepare("SELECT count(*) FROM %i", $table) );
			$table_size = $wpdb->get_var( $wpdb->prepare("SELECT (DATA_LENGTH + INDEX_LENGTH) FROM `information_schema`.TABLES WHERE TABLE_NAME = %s", $table) );
			if ( $record_count < $this->get_max_record_per_session() && $sum_record < $this->get_max_record_per_session() ) {
				$sum_record             = $sum_record + $record_count;
				$split_tables[ $inc ][] = array(
					'table_name' 	=> $table,
					'records'    	=> $record_count,
					'size' 			=> $table_size
				);
			} else {
				if ( array_key_last( $split_tables ) == $inc ) {
					$inc = $inc + 1;
				}
				$split_tables[ $inc ][] = array(
					'table_name'	=> $table,
					'records'    	=> $record_count,
					'size' 			=> $table_size
				);
				$sum_record             = 0;
				$inc++;
			}
		}

		try {
			if ( file_exists( $file_path ) ) {
				$this->remove_files( array( $file_path ) );
			}
			$handle = fopen( $file_path, 'w' );

			if ( is_resource( $handle ) ) {
				fwrite( $handle, json_encode( $split_tables ) );
			}
			fclose( $handle );
		} catch ( \Exception $e ) {
			$error_message = 'Generate table split error => ' . $e->getMessage() . ' at file : ' . $e->getFile() .
			' at line : ' . $e->getLine();
			wpstack_log( 'Error generate table split => ' . json_encode( $error_message ) );

			return new WP_Error(
				'Generate table split error',
				array(
					'error'  => $error_message,
					'status' => 403,
				)
			);
		}

		return array(
			'message'   => 'Generate database split detail successful',
			'status'    => true,
			'file_path' => $file_path,
		);
	}

	protected function get_max_record_per_session() {
		$max_record_per_session = 5000;
		if ( ini_get( 'max_execution_time' ) >= 90 ) {
			$max_record_per_session = 15000;
		} elseif ( ini_get( 'max_execution_time' ) >= 60 ) {
			$max_record_per_session = 12000;
		} elseif ( ini_get( 'max_execution_time' ) >= 30 ) {
			$max_record_per_session = 9000;
		}

		return $max_record_per_session;
	}

	public function get_sub_dir() {
		 $sub_dir = date( 'Y_m_d_H' );
		return $sub_dir;
	}

	public function split_file_path( $file_data ) {
		$arr_count       = count( $file_data );
		$size_over_array = array();
		$size_less_array = array();
		$new_path        = array();
		$sum_size        = 0;
		if ( $arr_count > 0 ) {
			foreach ( $file_data as $key => $value ) {
				if ( file_exists( $value ) ) {
					if ( filesize( $value ) > $this->get_maximum_memory_limit() ) {
						$size_over_array[] = $value;
					} else {

						$sum_size = $sum_size + filesize( $value );
						if ( $sum_size < $this->get_maximum_memory_limit() && count( $new_path ) < $this->split_dir_item_count() ) {
							$new_path[] = $value;
							unset( $file_data[ $key ] );
						} else {
							$size_less_array[] = $new_path;
							unset( $new_path, $sum_size );
							$new_path[] = $value;
							$sum_size   = filesize( $value );
						}
					}
				}
				if ( $key + 1 >= $arr_count ) {
					$size_less_array[] = $new_path;
				}
			}
		}

		return $size_less_array;
	}

	public function scan_files_glob( $params = array() ) {
		$init_dir = isset( $params['init_dir'] ) ? urldecode( $params['init_dir'] ) : './';
		$offset   = isset( $params['offset'] ) ? intval( urldecode( $params['offset'] ) ) : 0;
		$limit    = isset( $params['limit'] ) ? intval( urldecode( $params['limit'] ) ) : 0;
		$bsize    = isset( $params['bsize'] ) ? intval( urldecode( $params['bsize'] ) ) : 1024 * 1024;
		$regex    = isset( $params['regex'] ) ? urldecode( $params['regex'] ) : '{.??,}*';
		$recurse  = isset( $params['recurse'] ) ? urldecode( $params['recurse'] ) : false;
		if ( array_key_exists( 'recurse', $params ) && $params['recurse'] == 'false' ) {
			$recurse = false;
		}
		$resp = $this->scan_files_using_glob( $init_dir, $offset, $limit, $bsize, $recurse, $regex );
		return $resp;
	}

	public function tables_info($params)
	{
		global $wpdb;
		$table_info = [];
		$tables = isset($params['tables']) ? $params['tables'] : null;
		if ($tables) {
			foreach ($tables as $table) {
				$table_info[] = array_shift(
					$wpdb->get_results( $wpdb->prepare("SELECT * FROM `information_schema`.TABLES WHERE TABLE_NAME = %s", $table) )
				);
			}
		}

		return $table_info;
	}

	public function scan_tables()
	{
		global $wpdb;
		$tables       = $wpdb->get_col( 'SHOW TABLES' );
		return $tables;
	}

	/**
	 * Deprecated
	 */
	public function scan_table_lists()
	{
		global $wpdb;

		$split_tables = array();
		$tables       = $wpdb->get_col( 'SHOW TABLES' );

		foreach ( $tables as $table ) {
			$record_count = $wpdb->get_var( 
				$wpdb->prepare("SELECT count(*) FROM %i", $table)
			);
			$table_size = $wpdb->get_var( 
				$wpdb->prepare("SELECT (DATA_LENGTH + INDEX_LENGTH) FROM `information_schema`.TABLES WHERE TABLE_NAME = %s", $table)
			);
			$split_tables[] = array(
				'table_name' 	=> $table,
				'records'    	=> $record_count,
				'size' 			=> $table_size
			);
		}

		return $split_tables;
	}

	public function dump_database( $database_splits, $offset, $backup_dir, $start_record, $part ) {
		try {
			$mysql  = new WPStack_Connect_Backup_Mysql_Db();
			$result = $mysql::dump_file( $database_splits, $backup_dir, $offset, $start_record, $part );
		} catch ( \Exception $e ) {
			$error_message = 'Dump Database error => ' . $e->getMessage() . ' at file : ' . $e->getFile() .
			' at line : ' . $e->getLine();
			wpstack_log( 'WPStack_Connect_Filesystem dump_database => ' . json_encode( $error_message ) );

			return new WP_Error(
				'Dump database error',
				array(
					'error'  => $error_message,
					'status' => 403,
				)
			);
		}

		return $result;
	}

	public function archiving_db( $backup_filename, $database_backup_filepath ) {
		set_time_limit( -1 );
		ini_set( 'max_execution_time', -1 );
		ini_set( 'memory_limit', '512M' );

		try {
			$zip = new ZipArchive();
			$zip->open( $backup_filename, ZipArchive::CREATE );
			$file_folder_lists = scandir( $database_backup_filepath );
			foreach ( $file_folder_lists as $file_folder ) {
				if ( $file_folder != '.' && $file_folder != '..' ) {
					$zip->addFile( $database_backup_filepath . $file_folder, 'databases/' . $file_folder );
				}
			}
			$zip->close();
		} catch ( \Exception $e ) {
			$error_message = 'Archiving database error => ' . $e->getMessage() . ' at file : ' . $e->getFile() .
			' at line : ' . $e->getLine();
			wpstack_log( 'WPStack_Connect_Filesystem archiving_db => ' . json_encode( $error_message ) );

			return new WP_Error(
				'Archiving database error',
				array(
					'error'  => $error_message,
					'status' => 403,
				)
			);
		}

		return true;
	}

	public function archiving_files( $path_arr, $offset, $filename ) {
		try {
			$root_path = realpath( ABSPATH );
			$zip       = new ZipArchive();
			ob_start();
			$zip->open( $filename, ZipArchive::CREATE );

			$path_lists = json_decode(file_get_contents($path_arr[ $offset ]));
			
			foreach ( $path_lists as $path ) {
				$full_path = $root_path.substr($path, 1);
				if ( ! is_dir( $full_path ) ) {
					$relative_path = substr( $full_path, strlen( $root_path ) + 1 );
					$zip->addFile( $full_path, 'files/' . $relative_path );
				}
			}
			$zip->close();
			ob_clean();
		} catch ( \Exception $e ) {
			$error_message = 'Archiving files error => ' . $e->getMessage() . ' at file : ' . $e->getFile() .
			' at line : ' . $e->getLine();
			wpstack_log( 'WPStack_Connect_Filesystem archiving_files => ' . json_encode( $error_message ) );

			return new WP_Error(
				'Archiving files error',
				array(
					'error'  => $error_message,
					'status' => 403,
				)
			);
		}

		return true;
	}

	public function get_path_arr( $files_path_info ) {
		$file_name = array(
			'include' => array(),
			'exclude' => array(),
		);
		if ( sizeof( $files_path_info ) > 0 ) {
			foreach ( $files_path_info as $file_info ) {
				if ( ! $this->validate_ignore_path( $file_info, $this->default_ignore_directories ) ) {
					$file_name['include'][] = $file_info;
				} else {
					$file_name['exclude'][] = $file_info;
				}
			}
		}
		return $file_name;
	}

	public function get_path_size( $files_path_info ) {
		$path_size = array(
			'include' => 0,
			'exclude' => 0,
		);
		foreach ( $files_path_info as $file_info ) {
			if ( ! $this->validate_ignore_path( $file_info, $this->default_ignore_directories ) ) {
				$path_size['include'] += filesize( $file_info );
			} else {
				$path_size['exclude'] += filesize( $file_info );
			}
		}

		return $path_size;
	}

	public function clean_backup_directory( $dir ) {
		// $files = glob();
		// foreach ($files as $file) {
			// is_dir($file) ? @rmdir($file) : @unlink($file);
		// }

		$file_folder_lists = scandir( $dir );

		foreach ( $file_folder_lists as $file_folder ) {
			if ( $file_folder != '.' && $file_folder != '..' ) {
				if ( ! is_dir( $dir . DIRECTORY_SEPARATOR . $file_folder ) ) {
					unlink( $dir . DIRECTORY_SEPARATOR . $file_folder );
				}

				if ( is_dir( $dir . DIRECTORY_SEPARATOR . $file_folder ) ) {
					$this->clean_backup_directory( $dir . DIRECTORY_SEPARATOR . $file_folder );
				}
			}
		}
	}

	public function generate_tree_view( $content_type, $content ) {
		 $treeview_file_path = wpstack_backup_dir() . 'json_file/' . $content_type . '.json';

		$handle = fopen( $treeview_file_path, 'w' );

		if ( is_resource( $handle ) ) {
			fwrite( $handle, $content );
		}
		fclose( $handle );

		return $treeview_file_path;
	}

	public function get_treeview_content( $params ) {
		if ( ! is_null( $params['is_backup'] ) && $params['is_backup'] ) {
			switch ( $params['filter'] ) {
				case 'table':
					$return  = '<ul role="tree" aria-labelledby="tree_label">';
					$return .= '<li role="treeitem" aria-expanded="false" aria-selected="false"><span>Databases</span>';
					$return .= $this->list_tables( 'group' );
					$return .= '</li>';
					$return .= '</ul>';
					break;

				default:
					$return  = '<ul role="tree" aria-labelledby="tree_label">';
					$return .= '<li role="treeitem" aria-expanded="false" aria-selected="false"><span>Files</span>';
					$return .= $this->list_folder_file( realpath( ABSPATH ), 'group' );
					$return .= '</li>';
					$return .= '</ul>';
					break;
			}
		} else {
			switch ( $params['filter'] ) {
				case 'table':
					$return = $this->list_tables( 'tree' );
					break;

				default:
					$return = $this->list_folder_file( realpath( ABSPATH ), 'tree' );
					break;
			}
		}

		return $return;
	}

	public function scan_files( $dir, $is_fullbackup = false, $params = [], $is_sub = false, &$lists_arr = array() ) {
		$file_folder_lists = scandir( $dir );
		$ignore_files = isset($params['ignore_files']) ? $params['ignore_files'] : array_merge( $this->default_ignore_directories, $this->default_ignore_files );
		$ignore_extensions = isset($params['ignore_extensions']) ? $params['ignore_extensions'] : ['.log'];

		foreach ( $file_folder_lists as $file_folder ) {
			if ( $file_folder != '.' && $file_folder != '..' ) {

				$file_folder_path = $dir . DIRECTORY_SEPARATOR . $file_folder;

				if ( ! $is_fullbackup ) {
					$file_datetime = date( 'Y-m-d', filemtime( $file_folder_path ) );
					$current_date  = date( 'Y-m-d' );
					if ( $file_datetime <> $current_date ) {
						continue;
					}
				}

				// validate file path or directory path
				if ( $this->validate_ignore_path( $file_folder_path, $ignore_files ) ) {
					continue;
				}

				//validate file extension
				if ( ! is_dir( $file_folder_path ) ) {
					if ($this->validate_ignore_file_extension( $file_folder_path, $ignore_extensions)) {
						continue;
					}	
				}

				if ( is_dir( $file_folder_path ) ) {
					$this->scan_files( $file_folder_path, $is_fullbackup, $params, true, $lists_arr );
				}

				$lists_arr[] = $file_folder_path;
			}
		}

		return $lists_arr;
	}

	public function get_list_tables() {
		 global $wpdb;
		$list_tables = array(
			'include' => array(),
			'exclude' => array(),
		);
		$tables      = $wpdb->get_col( 'SHOW TABLES' );
		foreach ( $tables as $table ) {
			if ( $this->validate_ignore_path( $table, $this->backup_exclude_table ) ) {
				$list_tables['exclude'][] = $table;
			} else {
				$list_tables['include'][] = $table;
			}
		}
		return $list_tables;
	}

	public function scan_files_info( $dir, $is_sub = false, &$lists_arr = array() ) {
		$file_folder_lists = scandir( $dir );

		foreach ( $file_folder_lists as $file_folder ) {
			if ( $file_folder != '.' && $file_folder != '..' ) {

				$file_folder_path = $dir . DIRECTORY_SEPARATOR . $file_folder;

				if ( ! $is_sub && ! in_array( $file_folder, $this->default_wordpress_file_path ) ) {
					continue;
				}

				if ( is_dir( $file_folder_path ) ) {
					$this->scan_files_info( $file_folder_path, true, $lists_arr );
				}

				$lists_arr[] = $this->file_state( $file_folder_path );
			}
		}

		return $lists_arr;
	}

	protected function scan_files_using_glob( $init_dir = './', $offset = 0, $limit = 0, $bsize = 1024, $recurse = false, $regex = '{.??,}*' ) {
		$i           = 0;
		$dirs        = array();
		$dirs[]      = $init_dir;
		$bfile_count = 0;
		$bfile_array = array();
		$current     = 0;
		$abspath     = realpath( ABSPATH ) . '/';
		$abslen      = strlen( $abspath );
		// XNOTE: $recurse cannot be used directly here
		while ( $i < count( $dirs ) ) {
			$dir = $dirs[ $i ];

			foreach ( glob( $abspath . $dir . $regex, GLOB_NOSORT | GLOB_BRACE ) as $absfile ) {
				$real_file = substr( $absfile, $abslen );
				if ( is_dir( $absfile ) && ! is_link( $absfile ) ) {
					$dirs[] = $real_file . '/';
				}
				$current++;
				if ( $offset >= $current ) {
					continue;
				}
				if ( ( $limit != 0 ) && ( ( $current - $offset ) > $limit ) ) {
					$i = count( $dirs );
					break;
				}
				$bfile_array[] = $this->file_state( $real_file );
				$bfile_count++;
				if ( $bfile_count == $bsize ) {
					$str_path    = serialize( $bfile_array );
					$bfile_count = 0;
					$bfile_array = array();
				}
			}
			$regex = '{.??,}*';
			$i++;
			if ( $recurse == false ) {
				break;
			}
		}
		if ( $bfile_count != 0 ) {
			$str_path = serialize( $bfile_array );
			return $str_path;
		}
		return array( 'status' => 'done' );
	}

	protected function file_state( $filepath ) {
		$abslen                = strlen( ABSPATH );
		$file_data             = array();
		$file_data['filename'] = $filepath;
		$stats                 = @stat( $filepath );
		if ( $stats ) {
			foreach ( preg_grep( '#size|uid|gid|mode|mtime#i', array_keys( $stats ) ) as $key ) {
				$file_data[ $key ] = $stats[ $key ];
			}
			if ( is_link( $filepath ) ) {
				$file_data['link'] = @readlink( $filepath );
			}
		} else {
			$file_data['failed'] = true;
		}
		$file_data['is_dir']    = is_dir( $filepath );
		$file_data['datetime']  = date( 'Y-m-d H:i:s', filemtime( $filepath ) );
		$file_data['filemtime'] = filemtime( $filepath );
		return $file_data;
	}

	protected function validate_ignore_path( $haystack, $needles = array() ) {
		foreach ( $needles as $needle ) {
			if ( strpos( $haystack, $needle ) > 0 ) {
				return true;
			}
		}

		return false;
	}

	protected function validate_ignore_file_extension( $haystack, $needles = array() ) {
		foreach ( $needles as $needle ) {
			$ext = pathinfo($haystack, PATHINFO_EXTENSION);
			if ( strpos( $ext, $needle ) > 0 ) {
				return true;
			}
		}

		return false;
	}

	protected function list_folder_file( $dir, $ul_role, &$html_result = '' ) {
		$file_folder_lists = scandir( $dir );
		if ( $ul_role === 'tree' ) {
			$html_result .= '<ul role="' . $ul_role . '" aria-labelledby="tree_label">';
		} else {
			$html_result .= '<ul role="' . $ul_role . '">';
		}

		foreach ( $file_folder_lists as $file_folder ) {
			if ( $file_folder != '.' && $file_folder != '..' ) {

				$file_folder_path = $dir . DIRECTORY_SEPARATOR . $file_folder;

				if ( is_dir( $file_folder_path ) ) {
					if ( $this->validate_ignore_path( $file_folder_path, array_merge( $this->default_ignore_directories, $this->default_ignore_files ) ) ) {
						continue;
					}
				}

				if ( ! is_dir( $file_folder_path ) ) {
					$html_result .= '<li role="treeitem" aria-selected="false" class="' . $this->get_style_file_extension( $file_folder_path ) . '" realpath="' . $file_folder_path . '">' . $file_folder;
				} else {
					$html_result .= '<li role="treeitem" aria-expanded="false" aria-selected="false" realpath="' . $file_folder_path . '"><span>' . $file_folder . '</span>';
				}
				if ( is_dir( $file_folder_path ) ) {
					$this->list_folder_file( $file_folder_path, 'group', $html_result );
				}
				$html_result .= '</li>';
			}
		}
		$html_result .= '</ul>';

		return $html_result;
	}

	protected function list_tables( $ul_role, &$html_result = '' ) {
		global $wpdb;

		$wp_db_exclude_table = array();
		$tables              = $wpdb->get_col( 'SHOW TABLES' );
		if ( $ul_role === 'tree' ) {
			$html_result .= '<ul role="' . $ul_role . '" aria-labelledby="tree_label">';
		} else {
			$html_result .= '<ul role="' . $ul_role . '">';
		}

		foreach ( $tables as $table ) {
			if ( empty( $wp_db_exclude_table ) || ( ! ( in_array( $table, $wp_db_exclude_table ) ) ) ) {
				$html_result .= '<li role="treeitem" aria-selected="false"><i class="fa-solid fa-table"></i> ' . $table;
			} else {
				$html_result .= '<li role="treeitem" aria-selected="false"><i class="fa-solid fa-table"></i> ' . $table;
			}
			$html_result .= '</li>';
		}
		$wpdb->flush();

		$html_result .= '</ul>';

		return $html_result;
	}

	protected function get_style_file_extension( $file_folder ) {
		$extension = 'file';
		if ( isset( pathinfo( $file_folder )['extension'] ) ) {
			$extension = pathinfo( $file_folder )['extension'];
		}
		return $extension;
	}

	protected function get_count_comments( $posts ) {
		$count_comments = 0;
		foreach ( $posts as $post ) {
			$comments        = wp_count_comments( $post->ID );
			$count_comments += $comments->total_comments;
		}

		return $count_comments;
	}

	protected function get_active_users() {
		 $blog_users  = get_users();
		$active_users = 0;
		foreach ( $blog_users as $blog_user ) {
			if ( $blog_user->data->user_status == 0 ) {
				$active_users += 1;
			}
		}

		return $active_users;
	}

	protected function get_current_theme( $wp_version ) {
		if ( version_compare( $wp_version, '3.4', '>' ) ) {
			return wp_get_theme();
		}
		// return get_current_theme();
	}

	protected function get_plugin_informations() {
		$plugin_informations = array();
		$plugin_data         = get_site_transient( 'update_plugins' );
		$plugins             = get_plugins();

		if ( isset( $plugins )
			&& is_array( $plugins )
			&& sizeof( $plugins ) > 0
		) {
			foreach ( $plugins as $plugin_id => $plugin ) {

				if ( isset( $plugin_data->response ) && isset( $plugin_data->response[ $plugin_id ] ) ) {
					if ( sizeof( (array) $plugin_data->response[ $plugin_id ] ) > 0 ) {
						$plugin_update	= (array) $plugin_data->response[ $plugin_id ];
						$thumbnail		= null;
						if ( isset( $plugin_update['icons'] ) ) {
							$array_thumbnail = (array) $plugin_update['icons'];
							$thumbnail = isset( $array_thumbnail['2x'] ) ? $array_thumbnail['2x'] : ( isset( $array_thumbnail['1x'] ) ? $array_thumbnail['1x'] : ( isset( $array_thumbnail['svg'] ) ? $array_thumbnail['svg'] : null ) );
						}	

						$plugin_informations[] = array(
							'plugin_id'   => $plugin_id,
							'name'        => $plugin['Name'],
							'thumbnail'   => $thumbnail,
							'version'     => $plugin['Version'],
							'new_version' => $plugin_update['new_version'],
							'status'      => in_array( $plugin_id, get_option( 'active_plugins' ) ) ? 'activate' : 'deactivated',
							'description' => $plugin['Description'],
							'slug' 		  => $plugin_update['slug'],
						);
					}
				} elseif ( isset( $plugin_data->no_update ) && isset( $plugin_data->no_update[ $plugin_id ] ) ) {
					if ( sizeof( (array) $plugin_data->no_update[ $plugin_id ] ) > 0 ) {
						$plugin_update	= (array) $plugin_data->no_update[ $plugin_id ];
						$thumbnail		= null;
						if ( isset( $plugin_update['icons'] ) ) {
							$array_thumbnail = (array) $plugin_update['icons'];
							$thumbnail = isset( $array_thumbnail['2x'] ) ? $array_thumbnail['2x'] : ( isset( $array_thumbnail['1x'] ) ? $array_thumbnail['1x'] : ( isset( $array_thumbnail['svg'] ) ? $array_thumbnail['svg'] : null ) );
						}			
						
						$plugin_informations[] = array(
							'plugin_id'   => $plugin_id,
							'name'        => $plugin['Name'],
							'thumbnail'   => $thumbnail,
							'version'     => $plugin['Version'],
							'new_version' => null,
							'status'      => in_array( $plugin_id, get_option( 'active_plugins' ) ) ? 'activate' : 'deactivated',
							'description' => $plugin['Description'],
							'slug' 		  => $plugin_update['slug'],
						);
					}
				} else {
					$plugin_informations[] = array(
						'plugin_id'   => $plugin_id,
						'name'        => $plugin['Name'],
						'thumbnail'   => null,
						'version'     => $plugin['Version'],
						'new_version' => null,
						'status'      => in_array( $plugin_id, get_option( 'active_plugins' ) ) ? 'activate' : 'deactivated',
						'description' => $plugin['Description'],
						'slug' 		  => $plugin['TextDomain'],
					);
				}
			}

			return $plugin_informations;

		}

		return false;
	}

	protected function get_theme_informations() {
		global $wp_version;

		$theme_data         = $this->theme_info;
		$themes             = wp_get_themes();
		$theme_informations = array();
		$theme_active       = $this->get_current_theme( $wp_version );

		if ( isset( $theme_data->checked )
			&& is_array( $theme_data->checked )
			&& sizeof( $theme_data->checked ) > 0
		) {

			foreach ( $theme_data->checked as $theme_id => $theme_version ) {
				if ( ! is_null( $themes[ $theme_id ] ) ) {
					if ( isset( $theme_data->response )
						&& isset( $theme_data->response[ $theme_id ] )
					) {
						if ( ! is_null( (array) $theme_data->response[ $theme_id ] ) ) {
							$theme                = ! is_null( (array) $theme_data->response[ $theme_id ] ) ? (array) $theme_data->response[ $theme_id ] : null;
							$theme_informations[] = array(
								'theme_id'    => $theme_id,
								'name'        => $themes[ $theme_id ]['Name'],
								'thumbnail'   => isset( $theme['icons']['2x'] ) ? $theme['icons']['2x'] : null,
								'version'     => $theme_version,
								'new_version' => isset( $theme['new_version'] ) ? $theme['new_version'] : null,
								'status'      => ( $theme_active->get( 'TextDomain' ) == $theme_id ) ? 'activate' : 'deactivated',
								'description' => $themes[ $theme_id ]['Description'],
								'slug' 		  => $theme_id,
							);
						}
					} elseif ( isset( $theme_data->no_update ) && isset( $theme_data->no_update[ $theme_id ] ) ) {
						if ( sizeof( (array) $theme_data->no_update[ $theme_id ] ) > 0 ) {
							$theme                = (array) $theme_data->no_update[ $theme_id ];
							$theme_informations[] = array(
								'theme_id'    => $theme_id,
								'name'        => $themes[ $theme_id ]['Name'],
								'thumbnail'   => isset( $theme['icons']['2x'] ) ? $theme['icons']['2x'] : null,
								'version'     => $theme_version,
								'new_version' => null,
								'status'      => ( $theme_active->get( 'TextDomain' ) == $theme_id ) ? 'activate' : 'deactivated',
								'description' => $themes[ $theme_id ]['Description'],
								'slug' 		  => $theme_id,
							);
						}
					} else {
						$theme_informations[] = array(
							'theme_id'    => $theme_id,
							'name'        => $themes[ $theme_id ]['Name'],
							'thumbnail'   => null,
							'version'     => $theme_version,
							'new_version' => null,
							'status'      => ( $theme_active->get( 'TextDomain' ) == $theme_id ) ? 'activate' : 'deactivated',
							'description' => $themes[ $theme_id ]['Description'],
							'slug' 		  => $theme_id,
						);
					}
				}
			}

			return $theme_informations;
		}
		return false;
	}

	protected function reload() {
		global $wp_current_filter;
		$wp_current_filter[] = 'load-update-core.php';

		if ( function_exists( 'wp_clean_update_cache' ) ) {
			wp_clean_update_cache();
		}

		wp_update_plugins();

		wp_update_themes();

		array_pop( $wp_current_filter );

		wp_version_check();

		wp_version_check( array(), true );

		$this->core_info   = get_site_transient( 'update_core' );
		$this->plugin_info = get_site_transient( 'update_plugins' );
		$this->theme_info  = get_site_transient( 'update_themes' );
	}

	public function extract_ziparchive( $ofile, $destination_path, $auto_remove = false ) {
		$zip = new ZipArchive();
		if ( $zip->open( $ofile ) === true ) {
			$zip->extractTo( $destination_path );
			$zip->close();
			if ( $auto_remove ) {
				$this->remove_files( array( $ofile ) );
			}
			return $destination_path;
		}

		return false;
	}

	public function remove_files( $files ) {
		$result = array();

		foreach ( $files as $file ) {
			$file_result = array();

			if ( file_exists( $file ) ) {

				$file_result['status'] = unlink( $file );
				if ( $file_result['status'] === false ) {
					$file_result['error'] = 'UNLINK_FAILED';
				}
			} else {
				$file_result['status'] = true;
				$file_result['error']  = 'NOT_PRESENT';
			}

			$result[ $file ] = $file_result;
		}

		$result['status'] = true;
		return $result;
	}

	public function remove_dirs( $dirs ) {
		$result = array();

		foreach ( $dirs as $dir ) {
			$dir_result = array();

			if ( is_dir( $dir ) && ! is_link( $dir ) ) {

				if ( $this->is_empty_dir( $dir ) ) {

					$dir_result['status'] = rmdir( $dir );
					if ( $dir_result['status'] === false ) {
						$dir_result['error'] = 'RMDIR_FAILED';
					}
				} else {
					$dir_result['status'] = false;
					$dir_result['error']  = 'NOT_EMPTY';
				}
			} else {
				$dir_result['status'] = false;
				$dir_result['error']  = 'NOT_DIR';
			}

			$result[ $dir ] = $dir_result;
		}

		$result['status'] = true;
		return $result;
	}

	public function cleanup( $dir ) {

		if ( ! file_exists( $dir ) ) {
			return true;
		}

		if ( ! is_dir( $dir ) ) {
			return unlink( $dir );
		}

		foreach ( scandir( $dir ) as $item ) {
			if ( $item == '.' || $item == '..' ) {
				continue;
			}

			if ( ! $this->cleanup( $dir . DIRECTORY_SEPARATOR . $item ) ) {
				return false;
			}
		}

		return rmdir( $dir );
	}

	public function is_empty_dir( $dir ) {
		$handle = opendir( $dir );

		while ( false !== ( $entry = readdir( $handle ) ) ) {
			if ( $entry != '.' && $entry != '..' ) {
				closedir( $handle );
				return false;
			}
		}
		closedir( $handle );

		return true;
	}

	public function parse_size($size)
	{
		if ( preg_match( '/^(\d+)(.)$/', $size, $matches ) ) {
			if ( $matches[2] == 'G' ) {
				$size = $matches[1] * 1024 * 1024 * 1024;
			} elseif ( $matches[2] == 'M' ) {
				$size = $matches[1] * 1024 * 1024;
			} elseif ( $matches[2] == 'K' ) {
				$size = $matches[1] * 1024;
			}
		}

		return $size;
	}

	public function plugin_theme() {
		global $wp_version, $wpdb;
		$this->reload();
		$statistics['synced'] = array(
			'plugins' => $this->get_plugin_informations(),
			'themes'  => $this->get_theme_informations(),
			'cores'	  => $this->core_info
		);

		return $statistics;
	}

}
