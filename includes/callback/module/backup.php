<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once dirname( __FILE__ ) . '/../multipart_upload.php';

class WPStack_Connect_backup extends WPStack_Connect_Callback_Base {

	public $parameters = array();

	public $params;

	protected $client;

	protected $socket;

	protected $result;

	protected $stream;

	protected $backup_exclude_table = array();

	protected $action_lists = [];

	public function do_backup( $params ) {
		$action = isset($params['action']) ? $params['action'] : '';
		$action_lists = isset($params['action_lists']) ? $params['action_lists'] : array(
			'start_backup',
			'export_db',
			'archiving_files',
			'transferring_files',
			'do_scan',
		);

		$out = array(
			'status'          	=> false,
			'sub_message'     	=> '',
			'step_finished'   	=> 0,
			'finished'        	=> 0,
			'step'            	=> $action,
			'sub_percent'     	=> 0,
			'backup_filename' 	=> isset($params['backup_filename']) ? $params['backup_filename'] : null,
			'prefix_name'     	=> '',
			'requested_by'    	=> isset($params['requested_by']) ? $params['requested_by'] : '',
			'start_record'    	=> 0,
			'part'            	=> isset($params['part']) ? $params['part'] : 0,
			'http_url'        	=> isset($params['http_url']) ? $params['http_url'] : '',
			'bucket'          	=> isset($params['bucket']) ? $params['bucket'] : '',
			'credentials'     	=> isset($params['credentials']) ? $params['credentials'] : '',
			'version'         	=> isset($params['version']) ? $params['version'] : '',
			'region'          	=> isset($params['region']) ? $params['region'] : '',
			'is_multiregion'  	=> isset($params['is_multiregion']) ? $params['is_multiregion'] : '',
			'dirpath'         	=> isset($params['dirpath']) ? $params['dirpath'] : '',
			'chunk_files'		=> isset($params['chunk_files']) ? $params['chunk_files'] : [],
			'chunk_tables'		=> isset($params['chunk_tables']) ? $params['chunk_tables'] : [],
			'clean_domain'		=> isset($params['clean_domain']) ? $params['clean_domain'] : '',
			'action_lists'		=> $action_lists
		);

		$this->action_lists = $action_lists;
		
		/**
		 * Log process
		*/
		$start_time = microtime( true );
		$logger     = "\r\n====================== START PROCESS " . $action . " ======================\r\n";
		$logger    .= 'Max execution time : ' . ini_get( 'max_execution_time' ) . "\r\n";
		$logger    .= 'Memory limit : ' . ini_get( 'memory_limit' ) . "\r\n";
		$logger    .= 'Start record : ' . $params['start_record'] . "\r\n";
		$logger    .= 'Part : ' . $params['part'] . "\r\n";

		if ( in_array( $action, $this->action_lists ) && method_exists( $this, $action ) ) {
			$out = $this->{$action}( $params, $out );
		} else {
			$out['sub_message'] = 'Backup failed';
		}

		$time_elapse_secs = microtime( true ) - $start_time;
		$logger          .= 'Execution time : ' . $time_elapse_secs . "\r\n";
		$logger          .= '====================== END PROCESS ' . $action . " ======================\r\n";
		wpstack_log( $logger );

		if ( $out['step_finished'] == 1 ) {
			$step_array_key = array_search( $out['step'], $this->action_lists );
			if ( isset( $this->action_lists[ $step_array_key + 1 ] ) ) {
				$out['step']   = $this->action_lists[ $step_array_key + 1 ];
				$out['offset'] = 0;
				$out['limit']  = 1;
			} else {
				$out['finished']    = 1;
				$out['percent']     = 100;
				$out['sub_percent'] = 100;
			}
		}

		return $out;
	}

	protected function start_backup( $params, $out ) {
		$out['step_finished'] = 0;
		
		$clean_domain = isset($params['clean_domain']) ? $params['clean_domain'] : null;
		if (!$clean_domain) {
			$out['step_finished'] = 0;
			$out['status']        = 'error';
			$out['sub_message']   = "Clean domain can't be null";
			$out['sub_percent']   = 0;
			return $out;
		}

		if (wpstack_zip_archive_checking()) {
				$prefix = '_backup_' . md5( date( 'Y-m-d H:i:s' ) );
				
				if ( ! file_exists( wpstack_backup_dir() . 'archive/' ) ) {
					mkdir( wpstack_backup_dir() . 'archive/', 0775 );
				}
	
				$filesystem = new WPStack_Connect_Filesystem();
	
				$filesystem->cleanup( wpstack_backup_dir() . 'archive/' );
				$filesystem->cleanup( wpstack_backup_dir() . 'databases/' );
				$out['step_finished'] = 1;
				$out['status']        = 'success';
				$out['sub_message']   = 'Export database processing...';
				$out['sub_percent']   = 100;
		} else {
			$out['requirements'] = array(
				'ZipArchive' => wpstack_zip_archive_checking()
			);
		}

		$out['prefix_name'] = $prefix;
		$out['dirpath']     = $clean_domain . DIRECTORY_SEPARATOR . str_ireplace( '_backup_', '', $prefix ) . DIRECTORY_SEPARATOR;
		$out['step']        = 'start_backup';
		return $out;
	}

	protected function export_db( $params, $out ) {
		set_time_limit( -1 );
		ini_set( 'max_execution_time', -1 );
		ini_set( 'memory_limit', '1024M' );

		$filesystem = new WPStack_Connect_Filesystem();

		$chunk_tables = isset($params['chunk_tables']) ? $params['chunk_tables'] : null;
		$database_splits = json_decode(file_get_contents($chunk_tables));

		$offset = intval( $params['offset'] );
		$limit  = intval( $params['limit'] );

		$database_backup_filepath = wpstack_backup_dir() . 'databases/';
		$total_items              = count( $database_splits );

		$total_steps        = ceil( $total_items / $limit );
		$sub_percent        = round( ( 100 / $total_steps ) * ( ( $offset / $limit ) + 1 ) );
		$out['sub_percent'] = $sub_percent;

		if ( $filesystem->validate_db_backup( $database_backup_filepath ) ) {
			$filename               = $this->archiving_db( $params, $database_backup_filepath );
			$out['status']          = 'success';
			$out['step_finished']   = 0;
			$out['sub_message']     = $params['sub_message'];
			$out['start_record']    = $params['start_record'];
			$out['backup_filename'] = $filename;
			$out['prefix_name']     = $params['prefix_name'];
			$out['offset']          = $params['offset'];
			$out['limit']           = $params['limit'];
			$out['step']            = 'export_db';
			return $out;
		}

		if ( count( $database_splits ) > 0 ) {
			if ( isset( $database_splits[ $offset ] ) ) {
				$result = $filesystem->dump_database( $database_splits, $offset, $database_backup_filepath, $params['start_record'], $params['part'] );
				if ( $result['export_finish'] ) {
					$new_offset = $offset + $limit;
					if ( !isset( $database_splits[ $new_offset ] ) ) {
						$out['backup_filename'] = $this->archiving_db( $params, $database_backup_filepath );
						$out['status']          = 'success';
						$out['step_finished']   = 1;
						$out['sub_message']     = 'Archiving files';
						$out['prefix_name']     = $params['prefix_name'];
						$out['step']            = 'export_db';
						return $out;
					}
					$out['start_record'] = 0;
					$out['part']         = 0;
				} else {
					// repeat to current array
					$new_offset          = $offset;
					$out['start_record'] = $result['start_record'];
					$out['part']         = $result['part'];
				}
			} else {
				$out['backup_filename'] = $this->archiving_db( $params, $database_backup_filepath );
				$out['status']          = 'success';
				$out['step_finished']   = 1;
				$out['sub_message']     = 'Archiving files';
				$out['prefix_name']     = $params['prefix_name'];
				$out['step']            = 'export_db';
				return $out;
			}
		} else {
			$out['backup_filename'] = $this->archiving_db( $params, $database_backup_filepath );
			$out['status']          = 'success';
			$out['step_finished']   = 1;
			$out['sub_message']     = 'Archiving files';
			$out['prefix_name']     = $params['prefix_name'];
			$out['step']            = 'export_db';
			return $out;
		}
		$out['status']          = 'success';
		$out['step']            = 'export_db';
		$out['offset']          = $new_offset;
		$out['limit']           = $limit;
		$out['step_finished']   = 0;
		$out['prefix_name']     = $params['prefix_name'];
		$out['backup_filename'] = isset($params['backup_filename']) ? $params['backup_filename'] : null;
		$out['sub_message']     = 'exporting DB (' . $sub_percent . '%)';

		return $out;
	}

	protected function archiving_db( $params, $database_backup_filepath ) {
		set_time_limit( -1 );
		ini_set( 'max_execution_time', -1 );
		ini_set( 'memory_limit', '1024M' );
		$filesystem = new WPStack_Connect_Filesystem();

		$clean_domain = isset($params['clean_domain']) ? $params['clean_domain'] : null;
		if (!$clean_domain) {
			$out['step_finished'] = 0;
			$out['status']        = 'error';
			$out['sub_message']   = "Clean domain can't be null";
			$out['sub_percent']   = 0;
			return $out;
		}
		
		if ( ! isset( $params['backup_filename'] ) ) {
			$filename = $clean_domain . $params['prefix_name'] . '.z01';
		} else {
			$filename = $filesystem->generate_backup_filename( $params['backup_filename'] );
		}

		$backup_filename = wpstack_backup_dir() . 'archive/' . $filename;
		$archive_status  = $filesystem->archiving_db( $backup_filename, $database_backup_filepath );
		if ( $archive_status && file_exists( $backup_filename ) ) {
			$multipart_upload = new WPStack_Connect_Multipart_Uploads( $params );
			$key              = 'temp/' . $params['dirpath'] . basename( $backup_filename );
			$multipart_upload->do_upload( $key, $backup_filename );
			$filesystem->remove_files( array( $backup_filename ) );
		}
		$filesystem->cleanup( $database_backup_filepath );

		return $filename;
	}

	protected function archiving_files( $params, $out ) {
		set_time_limit( -1 );
		ini_set( 'max_execution_time', -1 );
		ini_set( 'memory_limit', '1024M' );

		$filesystem = new WPStack_Connect_Filesystem();

		$chunk_files = isset($params['chunk_files']) ? $params['chunk_files'] : [];

		$offset      = intval( $params['offset'] );
		$limit       = intval( $params['limit'] );
		$filename    = $filesystem->generate_backup_filename( $params['backup_filename'] );
		$backup_file = wpstack_backup_dir() . 'archive/' . $filename;
		$total_items        = count( $chunk_files );
		$out['sub_percent'] = 0;

		if ( $total_items > 0 ) {

			$total_steps        = ceil( $total_items / $limit );
			$sub_percent        = round( ( 100 / $total_steps ) * ( ( $offset / $limit ) + 1 ) );
			$out['sub_percent'] = $sub_percent;
			if ( isset( $chunk_files[ $offset ] ) ) {
				$archive_status = $filesystem->archiving_files( $chunk_files, $offset, $backup_file );
				if ( $archive_status && file_exists( $backup_file ) ) {
					$multipart_upload = new WPStack_Connect_Multipart_Uploads( $params );
					$key              = 'temp/' . $params['dirpath'] . basename( $backup_file );
					$multipart_upload->do_upload( $key, $backup_file );
					$filesystem->remove_files( array( $backup_file ) );
				}

				$new_offset = $offset + $limit;
				if ( !isset( $chunk_files[ $new_offset ] ) ) {
					$out['download_url']    = content_url( 'wpstack_backup/archive/' . $filename );
					$out['status']          = 'success';
					$out['step_finished']   = 1;
					$out['sub_message']     = 'Scanning process';
					$out['backup_filename'] = $filename;
					$out['prefix_name']     = $params['prefix_name'];
					$out['step']            = 'archiving_files';
					return $out;
				}
			} else {
				$out['download_url']    = content_url( 'wpstack_backup/archive/' . $filename );
				$out['status']          = 'success';
				$out['step_finished']   = 1;
				$out['sub_message']     = 'Scanning process';
				$out['backup_filename'] = $filename;
				$out['prefix_name']     = $params['prefix_name'];
				$out['step']            = 'archiving_files';
				return $out;
			}
		} else {
			$out['download_url']    = content_url( 'wpstack_backup/archive/' . $filename );
			$out['status']          = 'success';
			$out['step_finished']   = 1;
			$out['sub_message']     = 'Scanning process';
			$out['backup_filename'] = $filename;
			$out['prefix_name']     = $params['prefix_name'];
			$out['step']            = 'archiving_files';
			$out['sub_percent']     = 100;
			return $out;
		}

		$out['status']          = 'success';
		$out['step']            = 'archiving_files';
		$out['offset']          = $new_offset;
		$out['limit']           = $limit;
		$out['step_finished']   = 0;
		$out['backup_filename'] = $filename;
		$out['prefix_name']     = $params['prefix_name'];
		$out['sub_message']     = 'Archiving files processing (' . $sub_percent . '%) ';

		return $out;
	}

	protected function backup_stats() {
		 global $wp_version, $wpdb;

		$statistics['wp_version']      = $wp_version;
		$current_theme                 = $this->get_current_theme( $wp_version );
		$statistics['active_theme']    = $current_theme['Name'] . ' v' . $current_theme['Version'];
		$statistics['active_plugins']  = count( get_option( 'active_plugins' ) );
		$statistics['published_posts'] = (int) $wpdb->get_var( 
			$wpdb->prepare("SELECT COUNT(*) FROM %i WHERE post_type='post' AND post_status='publish'", $wpdb->posts)
		);

		return $statistics;
	}

	protected function ftp_filelist() {
		 $filelist  = array();
		$filesystem = new WPStack_Connect_Filesystem();
		$scan_files = $filesystem->scan_files( realpath( ABSPATH ) );

		foreach ( $scan_files as $pathinfo ) {
			$filelist[] = substr( $pathinfo, strlen( realpath( ABSPATH ) ) );
		}

		return $filelist;
	}

	protected function get_current_theme( $wp_version ) {
		// if ( version_compare( $wp_version, '3.4', '>' ) ) {
			return wp_get_theme();
		// }
		// return get_current_theme();
	}

	protected function fetch_filelists_info() {
		$results       = array();
		$filesystem     = new WPStack_Connect_Filesystem();
		$filelists_info = $filesystem->scan_files_info( realpath( ABSPATH ) );
		foreach ( $filelists_info as $filelists ) {
			$results[ $filelists['filename'] ] = $filelists;
		}
		return $results;
	}

	protected function filelists_info( $params )
	{
		$filesystem = new WPStack_Connect_Filesystem();
		$filelists = $filesystem->scan_files_glob($params);

		return $filelists;
	}

	protected function tablelists_info()
	{
		$filesystem = new WPStack_Connect_Filesystem();
		$filelists = $filesystem->scan_table_lists();

		return $filelists;
	}

	protected function wp_info() {
		$filesystem = new WPStack_Connect_Filesystem();
		return $filesystem->wordpress_stats();
	}

	public function process( $request ) {
		$resp = array();

		switch ( $request->method ) {
			case 'do_backup':
				$resp = $this->do_backup( $request->params );
				break;

			case 'ftp_filelist':
				$resp = $this->ftp_filelist();
				break;

			case 'filelists_info':
				$resp = $this->fetch_filelists_info();
				break;

			case 'filelists':
				$resp = $this->filelists_info( $request->params );
				break;

			case 'tablelists':
				$resp = $this->tablelists_info();
				break;

			case 'wp_info':
				$resp = $this->wp_info();
				break;

			default:
				break;
		}

		if ( is_array( $resp ) ) {
			$resp = $resp;
		}
		return $resp;
	}
}

