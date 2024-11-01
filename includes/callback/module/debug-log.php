<?php

class WPStack_Connect_Debug_Log extends WPStack_Connect_Callback_Base {

	public $settings;
	public $account;

    public function get_processed_entries() {

        $wpstack_connect_file_path = get_option( 'wpstack_connect_filepath' );

        $log 	= file_get_contents( $wpstack_connect_file_path );

        $log 	= str_replace( "[\\", "^\\", $log );

        $log 	= str_replace( "[\"", "^\"", $log );

        $log = str_replace( "[internal function]", "^internal function^", $log );

        $lines 	= explode("[", $log);
        $prepended_lines = array();

        $lines 	= array_slice( $lines, -100000 );

        foreach ( $lines as $line ) {
        	if ( !empty($line) ) {
        		$line 			= str_replace( "UTC]", "UTC]@@@", $line );
        		$line 			= str_replace( "Stack trace:", "<hr />Stack trace:", $line );
				if ( strpos( $line, 'PHP Fatal' ) !== false ) {
	        		$line 		= str_replace( "#", "<hr />#", $line );
	        	}
        		$line 			= str_replace( "Argument <hr />#", "Argument #", $line );
        		$line 			= str_replace( "parameter <hr />#", "parameter #", $line );
        		$line 			= str_replace( "the <hr />#", "the #", $line );
        		$line 			= str_replace( "^\\", "[\\", $line );
        		$line 			= str_replace( "^\"", "[\"", $line );
        		$line 			= str_replace( "^internal function^", "[internal function]", $line );
	        	$prepended_line 	= '[' . $line;
	        	$prepended_lines[] 	= $prepended_line;
        	}
        }

        $latest_lines 	= array_reverse( $prepended_lines );

        $errors_master_list = array();

		foreach( $latest_lines as $line ) {

			$line = explode("@@@ ", trim( $line ) );

			$timestamp = str_replace( [ "[", "]" ], "", $line[0] );

			$error = '';
			$error_source = '';	
			$error_file = '';
			$error_file_path = '';
			$error_file_line = '';

			if ( array_key_exists('1', $line) ) {
				$error = $line[1];

				if ( false !== strpos( $error, ABSPATH ) ) {

					if ( false !== strpos( $error, 'Stack trace:' ) ) {
						$error_parts = explode( 'Stack trace:', $error );
						$error_message = str_replace( '<hr />', '', $error_parts[0] );
						if ( isset( $error_parts[1] ) ) {
							$error_stack_trace = ' ' . $error_parts[1];
						}

						$error_message_parts = explode( ' in /', $error_message );

						$error = $error_message_parts[0] . '<hr />Stack trace:' . $error_stack_trace;
						$error = str_replace( ABSPATH, '/', $error );
						if ( isset( $error_message_parts[1] ) ) {
							$error_file = '/' . $error_message_parts[1];
							$error_file_info = explode ( ':', $error_file );
							$error_file_path = $error_file_info[0];
							if ( array_key_exists('1', $error_file_info) ) {
								$error_file_line = $error_file_info[1];
							}
						}
					} else {
						$error_message_parts = explode( ' in /', $error );

						$error = $error_message_parts[0];
						if ( isset( $error_message_parts[1] ) ) {
							$error_file = '/' . $error_message_parts[1];

							$error_file_info = explode ( ' on line ', $error_file );
							$error_file_path = $error_file_info[0];
							if ( array_key_exists('1', $error_file_info) ) {
								$error_file_line = $error_file_info[1];
							}
						}
					}

					$error_file_path = str_replace( ABSPATH, '/', $error_file_path );

					if ( ( false !== strpos( $error_file, '/wp-admin/' ) ) || 
						   ( false !== strpos( $error_file, '/wp-includes/' ) ) ) {
						$error_source = __( 'WordPress core', 'wp-stack-connect' );
					} elseif ( ( false !== strpos( $error_file, '/wp-content/themes/' ) ) ) {
						$error_source = __( 'Theme', 'wp-stack-connect' );
					} elseif ( ( false !== strpos( $error_file, '/wp-content/plugins/' ) ) ) {
						$error_source = __( 'Plugin', 'wp-stack-connect' );
					} else {
						$error_source = '';	
					}

					if ( ( 'Plugin' == $error_source ) || ( 'Theme' == $error_source ) ) {
						$error_file_path_parts = explode( '/', $error_file_path );
						$error_file_directory = $error_file_path_parts[3];
					}

					$plugins = get_plugins();

					if ( 'Plugin' == $error_source ) {
						foreach ( $plugins as $plugin_path_file => $plugin_info ) {
							if ( false !== strpos( $plugin_path_file, $error_file_directory ) ) {
								$error_source_plugin_path_file = $plugin_path_file;
								$error_source_plugin_name = $plugin_info['Name'];
								$error_source_plugin_uri = $plugin_info['PluginURI'];
								// $error_source_plugin_version = $plugin_info['Version'];
							}
						}
					}

					if ( 'Theme' == $error_source ) {
						$theme = wp_get_theme( $error_file_directory );
						if ( $theme->exists() ) {
							$error_source_theme_dir = $error_file_directory;
							$error_source_theme_name = $theme->get( 'Name' );
							$error_source_theme_uri = $theme->get( 'ThemeURI' );
							// $error_source_theme_version = $theme->get( 'Version' );
						} else {
							$error_source_theme_name = $error_file_directory;
						}
					}

				}

			} else {

				$error = __( 'No error message specified...', 'wp-stack-connect' );
	
			}
			
			if ( ( false !== strpos( $error, 'PHP Fatal' )) || ( false !== strpos( $error, 'FATAL' ) ) || ( false !== strpos( $error, 'E_ERROR' ) ) ) {
				$error_type 	= __( 'PHP Fatal', 'wp-stack-connect' );
				$error_details 	= str_replace( "PHP Fatal error: ", "", $error );
				$error_details 	= str_replace( "PHP Fatal: ", "", $error_details );
				$error_details 	= str_replace( "FATAL ", "", $error_details );
				$error_details 	= str_replace( "E_ERROR: ", "", $error_details );
			} elseif ( ( false !== strpos( $error, 'PHP Warning' ) ) || (  false !== strpos( $error, 'E_WARNING' ) ) ) {
				$error_type 	= __( 'PHP Warning', 'wp-stack-connect' );
				$error_details 	= str_replace( "PHP Warning: ", "", $error );
				$error_details 	= str_replace( "E_WARNING: ", "", $error_details );
			} elseif ( ( false !== strpos( $error, 'PHP Notice' ) ) || ( false !== strpos( $error, 'E_NOTICE' ) ) ) {
				$error_type 	= __( 'PHP Notice', 'wp-stack-connect' );
				$error_details 	= str_replace( "PHP Notice: ", "", $error );
				$error_details 	= str_replace( "E_NOTICE: ", "", $error_details );
			} elseif ( false !== strpos( $error, 'PHP Deprecated' ) ) {
				$error_type 	= __( 'PHP Deprecated', 'wp-stack-connect' );
				$error_details 	= str_replace( "PHP Deprecated: ", "", $error );
			} elseif ( ( false !== strpos( $error, 'PHP Parse' ) ) || ( false !== strpos( $error, 'E_PARSE' ) ) ) {
				$error_type 	= __( 'PHP Parse', 'wp-stack-connect' );
				$error_details 	= str_replace( "PHP Parse error: ", "", $error );
				$error_details 	= str_replace( "E_PARSE: ", "", $error_details );
			} elseif ( false !== strpos( $error, 'EXCEPTION:' ) ) {
				$error_type 	= __( 'PHP Exception', 'wp-stack-connect' );
				$error_details 	= str_replace( "EXCEPTION: ", "", $error );
			} elseif ( false !== strpos( $error, 'WordPress database error' ) ) {
				$error_type 	= __( 'Database', 'wp-stack-connect' );
				$error_details 	= str_replace( "WordPress database error ", "", $error );
			} elseif ( false !== strpos( $error, 'JavaScript Error' ) ) {
				$error_type 	= __( 'JavaScript', 'wp-stack-connect' );
				$error_details 	= str_replace( "JavaScript Error: ", "", $error );
			} else {
				$error_type 	= __( 'Other', 'wp-stack-connect' );
				$error_details 	= $error;
				if ( $this->is_json( $error_details ) ) {
					$error_details = '<pre>' . print_r( json_decode( $error_details, true ), true ) . '</pre>';
				}
			}

			if ( ! empty( $error_source ) ) {
				if ( 'WordPress core' == $error_source ) {
					$wp_version = get_bloginfo( 'version' );
					$file_viewer_url = 'https://github.com/WordPress/wordpress-develop/blob/' . $wp_version . '/src' . $error_file_path;
					$error_details = '<span class="error-details">' . $error_details . '</span><hr />' . $error_source . '<br />' . __( 'File', 'wp-stack-connect' ) . ': <a href="' . $file_viewer_url . '" target="_blank" class="error-source-link">' . $error_file_path . '<span class="dashicons dashicons-visibility offset-down"></span></a><br />' . __( 'Line', 'wp-stack-connect' ) . ': ' . $error_file_line;
				} elseif ( 'Theme' == $error_source ) {
					if ( ! defined( 'DISALLOW_FILE_EDIT' ) || ( false === constant( 'DISALLOW_FILE_EDIT' ) ) ) {
						$file_viewer_url = get_admin_url() . 'theme-editor.php?file=' . urlencode( str_replace( '/wp-content/themes/', '', $error_file_path ) ) . '&theme=' . $error_source_theme_dir;
						$error_details = '<span class="error-details">' . $error_details . '</span><hr />' . $error_source . ': <a href="' . $error_source_theme_uri . '" target="_blank" class="error-source-link">' . $error_source_theme_name . '<span class="dashicons dashicons-external offset-up"></span></a><br />' . __( 'File', 'wp-stack-connect' ) . ': <a href="' . $file_viewer_url . '" target="_blank" class="error-source-link">' . $error_file_path . '<span class="dashicons dashicons-visibility offset-down"></span></a><br />' . __( 'Line', 'wp-stack-connect' ) . ': ' . $error_file_line;
					} 
					if ( defined( 'DISALLOW_FILE_EDIT' ) && ( true === constant( 'DISALLOW_FILE_EDIT' ) ) ) {
						$error_details = '<span class="error-details">' . $error_details . '</span><hr />' . $error_source . ': <a href="' . $error_source_theme_uri . '" target="_blank" class="error-source-link">' . $error_source_theme_name . '<span class="dashicons dashicons-external offset-up"></span></a><br />' . __( 'File', 'wp-stack-connect' ) . ': ' . $error_file_path . '<br />' . __( 'Line', 'wp-stack-connect' ) . ': ' . $error_file_line;
					}
				} elseif ( 'Plugin' == $error_source ) {
					if ( ! defined( 'DISALLOW_FILE_EDIT' ) || ( false === constant( 'DISALLOW_FILE_EDIT' ) ) ) {
						$file_viewer_url = get_admin_url() . 'plugin-editor.php?file=' . urlencode( str_replace( '/wp-content/plugins/', '', $error_file_path ) ) . '&plugin=' . urlencode( $error_source_plugin_path_file );
						$error_details = '<span class="error-details">' . $error_details . '</span><hr />' . $error_source . ': <a href="' . $error_source_plugin_uri . '" target="_blank" class="error-source-link">' . $error_source_plugin_name . '<span class="dashicons dashicons-external offset-up"></span></a><br />' . __( 'File', 'wp-stack-connect' ) . ': <a href="' . $file_viewer_url . '" target="_blank" class="error-source-link">' . $error_file_path . '<span class="dashicons dashicons-visibility offset-down"></span></a><br />' . __( 'Line', 'wp-stack-connect' ) . ': ' . $error_file_line;
					} 
					if ( defined( 'DISALLOW_FILE_EDIT' ) && ( true === constant( 'DISALLOW_FILE_EDIT' ) ) ) {
						$error_details = '<span class="error-details">' . $error_details . '</span><hr />' . $error_source . ': <a href="' . $error_source_plugin_uri . '" target="_blank" class="error-source-link">' . $error_source_plugin_name . '<span class="dashicons dashicons-external offset-up"></span></a><br />' . __( 'File', 'wp-stack-connect' ) . ': ' . $error_file_path . '<br />' . __( 'Line', 'wp-stack-connect' ) . ': ' . $error_file_line;
					}
				}
			}

			if ( array_search( trim( $error_details ), array_column( $errors_master_list, 'details' ) ) === false ) {

				$errors_master_list[] = array(
					'type'			=> $error_type,
					'details'		=> trim( $error_details ),
					'occurrences'	=> array( $timestamp ),
				);

			} else {

				$error_position = array_search( trim( $error_details ), array_column( $errors_master_list, 'details' ) ); // integer

				array_push( $errors_master_list[$error_position]['occurrences'], $timestamp );

			}

		}

		return json_encode( $errors_master_list );

	}

    public function is_json( $string ) {

		json_decode( $string );
		return json_last_error() === JSON_ERROR_NONE;

	}

    public function preview()
    {
        $errors_master_list = json_decode( $this->get_processed_entries(), true );

		$n = 1;
		$entries_to_show = 5;

		?>
		<style>

			#wpstack-connect-preview.postbox .inside {
				margin: 0;
				padding: 0;
			}

			.wpstack-connect-preview {
				padding: 12px;
				border-bottom: 1px solid #e6e7e7;
			    word-wrap:  break-word; /* All browsers since IE 5.5+ */
			    overflow-wrap: break-word; /* Renamed property in CSS3 draft spec */
			}

			.wpstack-connect-preview:nth-child(odd) {
				background-color: #f6f7f7;
			}

			.wpstack-connect-preview-message a.error-source-link {
			    color: #50575e;
			    text-decoration: none;
			}

			.wpstack-connect-preview-message a.error-source-link span {
			    position: relative;
			    margin-left: 2px;
			    color: #777;
			    font-size: 18px;
			    width: 18px;
			    height: 18px;
			    transition: .25s;
			    text-decoration: none;
			}

			.wpstack-connect-preview-message a.error-source-link span.offset-up {
			    top: -1px;
			}

			.wpstack-connect-preview-message a.error-source-link span.offset-down {
			    top: 1px;
			}

			.wpstack-connect-preview-message a.error-source-link:hover {
			    color: #2271b1;
			    text-decoration: underline;
			}

			#debug-log .wpstack-connect-preview-details a.error-source-link:hover span {
			    color: #2271b1;
			    text-decoration: none;
			}

			.wpstack-connect-preview-meta {
				display: flex;
			}

			.wpstack-connect-preview-type {
				margin-right: 4px;
				font-weight: 600;
			}

			.wpstack-connect-preview-footer {
				display: flex;
				justify-content: space-between;
				align-items: center;
				width: 100%;
				box-sizing: border-box;
				padding: 12px;
				background-color: #f6f7f7;
			}

		</style>
		<div class="wpstack-connect-preview-entries">
		<?php

		foreach ( $errors_master_list as $error ) {

			if ( $n <= $entries_to_show ) {

				if ( function_exists( 'wp_date' ) ) {
					$localized_timestamp 	= wp_date( 'M j, Y, H:i:s', strtotime( $error['occurrences'][0] ) ); // last occurrence
				} else {
					$localized_timestamp 	= date_i18n( 'M j, Y - H:i:s', strtotime( $error['occurrences'][0] ) );
				}

				$occurrence_count 		= count( $error['occurrences'] );

				?>
					<div class="wpstack-connect-preview">
						<div class="wpstack-connect-preview-meta">
							<div class="wpstack-connect-preview-type">
								<?php echo esc_html( $error['type'] ); ?>
							</div>
							<div class="wpstack-connect-preview-datetime">
								| <?php echo esc_html( $localized_timestamp ); ?>
							</div>
						</div>
						<div class="wpstack-connect-preview-message">
							<?php echo wp_kses( $error['details'], 'post' ); ?>
						</div>
					</div>
				<?php

			}

			$n++;

		}

		?>
		</div>
		<?php
        exit;
    }

	public function get()
	{
		$errors_master_list = base64_encode( $this->get_processed_entries() );
		return $errors_master_list;
	}

	public function toggle_status( $request )
	{
		$wp_config_manager 	= new WP_Config_Manager;
		$wpstack_connect 	= new WPStack_Connect_Debug_Log_Core($request, $wp_config_manager);
		$result 			= $wpstack_connect->toggle_debugging();
		return $result;
	}

	public function info()
	{
		$log_file_path 		= get_option( 'wpstack_connect_filepath' );
		$file_size 			= size_format( (int) filesize( $log_file_path ) );

		$data = array(
			'size'		=> $file_size,
			'filepath'	=> $log_file_path
		);

		return $data;
	}

	public function process( $request ) {
		$resp = array();

		switch ( $request->method ) {
			case 'preview':
				$resp = $this->preview( $request->params );
				break;

			case 'toggle_status':
				$resp = $this->toggle_status( $request );
				break;

			case 'info':
				$resp = $this->info();
				break;

			default:
				$resp = $this->get();
				break;
		}

		if ( is_array( $resp ) ) 
        {                                                                                      
			$resp = $resp;
		}
		return $resp;
	}
}
