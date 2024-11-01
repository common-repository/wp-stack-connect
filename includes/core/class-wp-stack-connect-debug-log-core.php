<?php

class WPStack_Connect_Debug_Log_Core {

    protected $request;
    protected $wp_config_manager;

    function __construct($request, $wp_config_manager)
    {
        $this->request = $request;
        $this->wp_config_manager = $wp_config_manager;
    }

	public function activate() {
        
        $uploads_path = wp_upload_dir()['basedir'] . '/wp-stack-connect';

        $plain_domain = str_replace( array( ".", "-" ), "", sanitize_text_field( $_SERVER['SERVER_NAME'] ) );

        $unique_key = date( 'YmdHi' );

        $wpstack_connect = $uploads_path . '/' . $plain_domain . '_' . $unique_key .'_debug.log';

        $wpstack_connect_in_option = get_option( 'wpstack_connect_filepath' );

        if ( $wpstack_connect_in_option === false ) {

	        update_option( 'wpstack_connect_filepath', $wpstack_connect, false );

	        $wpstack_connect_in_option = get_option( 'wpstack_connect_filepath' );

        }

        if ( ! is_dir( $uploads_path ) ) {
            mkdir( $uploads_path );
        }

        if ( ! is_file( $wpstack_connect_in_option ) ) {
            file_put_contents( $wpstack_connect_in_option, '' );
        } else {}
	}

    public function deactivate()
    {
        $wpstack_connect_in_option = get_option( 'wpstack_connect_filepath' );

        if ( $wpstack_connect_in_option <> false ) {

	        delete_option( 'wpstack_connect_filepath' );
            unlink($wpstack_connect_in_option);

        }

        
        $this->wp_config_manager->remove( 'constant', 'WP_DEBUG' );
        $this->wp_config_manager->remove( 'constant', 'SCRIPT_DEBUG' );
        $this->wp_config_manager->remove( 'constant', 'WP_DEBUG_LOG' );
        $this->wp_config_manager->remove( 'constant', 'WP_DEBUG_DISPLAY' );
        $this->wp_config_manager->remove( 'constant', 'DISALLOW_FILE_EDIT' );

        // $data = array(
        //     'status'	=> 'disabled',
        //     'copy'		=> false,
        //     'message' 	=> '<strong>' . esc_html__( 'Error Logging', 'wpstack-connect' ) . '</strong>: ' . esc_html__( 'Disabled on', 'wpstack-connect' ) . ' ' . esc_html( $date_time ),
        //     'entries'	=> '',
        //     'size'		=> '',
        // );
    }

    public function toggle_debugging()
    {
		$wpstack_connect_file_path = get_option( 'wpstack_connect_filepath' );

		if ( function_exists( 'wp_date' ) ) {
			$date_time 	= wp_date( 'M j, Y - H:i:s' );
		} else {
			$date_time 	= date_i18n( 'M j, Y - H:i:s' );
		}

        if ($this->request->debug_log) {
            if ( $this->wp_config_manager->exists( 'constant', 'WP_DEBUG_LOG' ) ) {
                $wpstack_connect_const = $this->wp_config_manager->get_value( 'constant', 'WP_DEBUG_LOG' );
                if ( in_array( $wpstack_connect_const, array( 'true', 'false' ), true ) ) {
                    $wpstack_connect_const = (bool) $wpstack_connect_const;
                }

                if ( is_bool( $wpstack_connect_const ) ) {
                    if ( is_file( WP_CONTENT_DIR . '/debug.log' ) ) {
                        $default_debug_log_content = file_get_contents( WP_CONTENT_DIR . '/debug.log' );
                        file_put_contents( $wpstack_connect_file_path, $default_debug_log_content );
                        unlink( realpath( WP_CONTENT_DIR . '/debug.log' ) );
                    }

                } elseif ( is_string( $wpstack_connect_const ) ) {
                    if ( is_file( $wpstack_connect_const ) && ( $wpstack_connect_const != $wpstack_connect_file_path ) ) {
                        $custom_debug_log_content = file_get_contents( $wpstack_connect_const );
                        file_put_contents( $wpstack_connect_file_path, $custom_debug_log_content );
                        unlink( $wpstack_connect_const );
                    }

                }

                $copy = true;

            } else {

                $copy = false;

            }

            $options = array(
                'add'       => true,
                'raw'       => true,
                'normalize' => false,
            );

            $this->wp_config_manager->update( 'constant', 'WP_DEBUG', 'true', $options );

            $options = array(
                'add'       => true,
                'raw'       => true,
                'normalize' => false,
            );

            $this->wp_config_manager->update( 'constant', 'SCRIPT_DEBUG', 'true', $options );

            $options = array(
                'add'       => true,
                'raw'       => false,
                'normalize' => false,
            );

            $this->wp_config_manager->update( 'constant', 'WP_DEBUG_LOG', get_option( 'wpstack_connect_filepath' ), $options );

            $options = array(
                'add'       => true,
                'raw'       => true,
                'normalize' => false,
            );

            $this->wp_config_manager->update( 'constant', 'WP_DEBUG_DISPLAY', 'false', $options );

            $options = array(
                'add'       => true,
                'raw'       => true,
                'normalize' => false,
            );

            $this->wp_config_manager->update( 'constant', 'DISALLOW_FILE_EDIT', 'false', $options );

            $log_file_path 		= get_option( 'wpstack_connect_filepath' );
            $log_file_shortpath = str_replace( sanitize_text_field( $_SERVER['DOCUMENT_ROOT'] ), "", $log_file_path );
            $file_size 			= size_format( (int) filesize( $log_file_path ) );

            $errors_master_list = json_decode( $this->get_processed_entries(), true );

            $n = 1;
            $entries = array();

            foreach ( $errors_master_list as $error ) {

                if ( function_exists( 'wp_date' ) ) {
                    $localized_timestamp 	= wp_date( 'M j, Y - H:i:s', strtotime( $error['occurrences'][0] ) );
                } else {
                    $localized_timestamp 	= date_i18n( 'M j, Y - H:i:s', strtotime( $error['occurrences'][0] ) );
                }

                $occurrence_count 		= count( $error['occurrences'] );

                $entry = array( 
                        $n, 
                        $error['type'], 
                        $error['details'], 
                        $localized_timestamp . '<br /><span class="dlm-faint">(' . sprintf( _n( '%s occurrence logged', '%s occurrences logged', $occurrence_count, 'wpstack-connect' ), number_format_i18n( $occurrence_count ) ) . ')<span>',
                );

                $entries[] = $entry;

                $n++;

            }

            $data = array(
                'status'	=> 'enabled',
                'copy'		=> $copy,
                'message' 	=> '<strong>' . esc_html__( 'Error Logging', 'wpstack-connect' ) . '</strong>: ' . esc_html__( 'Enabled on', 'wpstack-connect' ) . ' ' . esc_html( $date_time ),
                'entries'	=> $entries,
                'size'		=> $file_size,
				'filepath'	=> $log_file_path
            );

        } else {
            // Remove Debug constants in wp-config.php

            $this->wp_config_manager->remove( 'constant', 'WP_DEBUG' );
            $this->wp_config_manager->remove( 'constant', 'SCRIPT_DEBUG' );
            $this->wp_config_manager->remove( 'constant', 'WP_DEBUG_LOG' );
            $this->wp_config_manager->remove( 'constant', 'WP_DEBUG_DISPLAY' );
            $this->wp_config_manager->remove( 'constant', 'DISALLOW_FILE_EDIT' );

            $data = array(
                'status'	=> 'disabled',
                'copy'		=> false,
                'message' 	=> '<strong>' . esc_html__( 'Error Logging', 'wpstack-connect' ) . '</strong>: ' . esc_html__( 'Disabled on', 'wpstack-connect' ) . ' ' . esc_html( $date_time ),
                'entries'	=> '',
                'size'		=> '',
				'filepath'	=> ''
            );
            
        }

        return $data;
    }

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
						$error_source = __( 'WordPress core', 'wpstack-connect' );
					} elseif ( ( false !== strpos( $error_file, '/wp-content/themes/' ) ) ) {
						$error_source = __( 'Theme', 'wpstack-connect' );
					} elseif ( ( false !== strpos( $error_file, '/wp-content/plugins/' ) ) ) {
						$error_source = __( 'Plugin', 'wpstack-connect' );
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
							}
						}
					}

					if ( 'Theme' == $error_source ) {
						$theme = wp_get_theme( $error_file_directory );
						if ( $theme->exists() ) {
							$error_source_theme_dir = $error_file_directory;
							$error_source_theme_name = $theme->get( 'Name' );
							$error_source_theme_uri = $theme->get( 'ThemeURI' );
						} else {
							$error_source_theme_name = $error_file_directory;
						}
					}

				}

			} else {

				$error = __( 'No error message specified...', 'wpstack-connect' );
	
			}
			
			if ( ( false !== strpos( $error, 'PHP Fatal' )) || ( false !== strpos( $error, 'FATAL' ) ) || ( false !== strpos( $error, 'E_ERROR' ) ) ) {
				$error_type 	= __( 'PHP Fatal', 'wpstack-connect' );
				$error_details 	= str_replace( "PHP Fatal error: ", "", $error );
				$error_details 	= str_replace( "PHP Fatal: ", "", $error_details );
				$error_details 	= str_replace( "FATAL ", "", $error_details );
				$error_details 	= str_replace( "E_ERROR: ", "", $error_details );
			} elseif ( ( false !== strpos( $error, 'PHP Warning' ) ) || (  false !== strpos( $error, 'E_WARNING' ) ) ) {
				$error_type 	= __( 'PHP Warning', 'wpstack-connect' );
				$error_details 	= str_replace( "PHP Warning: ", "", $error );
				$error_details 	= str_replace( "E_WARNING: ", "", $error_details );
			} elseif ( ( false !== strpos( $error, 'PHP Notice' ) ) || ( false !== strpos( $error, 'E_NOTICE' ) ) ) {
				$error_type 	= __( 'PHP Notice', 'wpstack-connect' );
				$error_details 	= str_replace( "PHP Notice: ", "", $error );
				$error_details 	= str_replace( "E_NOTICE: ", "", $error_details );
			} elseif ( false !== strpos( $error, 'PHP Deprecated' ) ) {
				$error_type 	= __( 'PHP Deprecated', 'wpstack-connect' );
				$error_details 	= str_replace( "PHP Deprecated: ", "", $error );
			} elseif ( ( false !== strpos( $error, 'PHP Parse' ) ) || ( false !== strpos( $error, 'E_PARSE' ) ) ) {
				$error_type 	= __( 'PHP Parse', 'wpstack-connect' );
				$error_details 	= str_replace( "PHP Parse error: ", "", $error );
				$error_details 	= str_replace( "E_PARSE: ", "", $error_details );
			} elseif ( false !== strpos( $error, 'EXCEPTION:' ) ) {
				$error_type 	= __( 'PHP Exception', 'wpstack-connect' );
				$error_details 	= str_replace( "EXCEPTION: ", "", $error );
			} elseif ( false !== strpos( $error, 'WordPress database error' ) ) {
				$error_type 	= __( 'Database', 'wpstack-connect' );
				$error_details 	= str_replace( "WordPress database error ", "", $error );
			} elseif ( false !== strpos( $error, 'JavaScript Error' ) ) {
				$error_type 	= __( 'JavaScript', 'wpstack-connect' );
				$error_details 	= str_replace( "JavaScript Error: ", "", $error );
			} else {
				$error_type 	= __( 'Other', 'wpstack-connect' );
				$error_details 	= $error;
				if ( $this->is_json( $error_details ) ) {
					$error_details = '<pre>' . print_r( json_decode( $error_details, true ), true ) . '</pre>';
				}
			}

			if ( ! empty( $error_source ) ) {
				if ( 'WordPress core' == $error_source ) {
					$wp_version = get_bloginfo( 'version' );
					$file_viewer_url = 'https://github.com/WordPress/wordpress-develop/blob/' . $wp_version . '/src' . $error_file_path;
					$error_details = '<span class="error-details">' . $error_details . '</span><hr />' . $error_source . '<br />' . __( 'File', 'wpstack-connect' ) . ': <a href="' . $file_viewer_url . '" target="_blank" class="error-source-link">' . $error_file_path . '<span class="dashicons dashicons-visibility offset-down"></span></a><br />' . __( 'Line', 'wpstack-connect' ) . ': ' . $error_file_line;
				} elseif ( 'Theme' == $error_source ) {
					if ( ! defined( 'DISALLOW_FILE_EDIT' ) || ( false === constant( 'DISALLOW_FILE_EDIT' ) ) ) {
						$file_viewer_url = get_admin_url() . 'theme-editor.php?file=' . urlencode( str_replace( '/wp-content/themes/', '', $error_file_path ) ) . '&theme=' . $error_source_theme_dir;
						$error_details = '<span class="error-details">' . $error_details . '</span><hr />' . $error_source . ': <a href="' . $error_source_theme_uri . '" target="_blank" class="error-source-link">' . $error_source_theme_name . '<span class="dashicons dashicons-external offset-up"></span></a><br />' . __( 'File', 'wpstack-connect' ) . ': <a href="' . $file_viewer_url . '" target="_blank" class="error-source-link">' . $error_file_path . '<span class="dashicons dashicons-visibility offset-down"></span></a><br />' . __( 'Line', 'wpstack-connect' ) . ': ' . $error_file_line;
					} 
					if ( defined( 'DISALLOW_FILE_EDIT' ) && ( true === constant( 'DISALLOW_FILE_EDIT' ) ) ) {
						$error_details = '<span class="error-details">' . $error_details . '</span><hr />' . $error_source . ': <a href="' . $error_source_theme_uri . '" target="_blank" class="error-source-link">' . $error_source_theme_name . '<span class="dashicons dashicons-external offset-up"></span></a><br />' . __( 'File', 'wpstack-connect' ) . ': ' . $error_file_path . '<br />' . __( 'Line', 'wpstack-connect' ) . ': ' . $error_file_line;
					}
				} elseif ( 'Plugin' == $error_source ) {
					if ( ! defined( 'DISALLOW_FILE_EDIT' ) || ( false === constant( 'DISALLOW_FILE_EDIT' ) ) ) {
						$file_viewer_url = get_admin_url() . 'plugin-editor.php?file=' . urlencode( str_replace( '/wp-content/plugins/', '', $error_file_path ) ) . '&plugin=' . urlencode( $error_source_plugin_path_file );
						$error_details = '<span class="error-details">' . $error_details . '</span><hr />' . $error_source . ': <a href="' . $error_source_plugin_uri . '" target="_blank" class="error-source-link">' . $error_source_plugin_name . '<span class="dashicons dashicons-external offset-up"></span></a><br />' . __( 'File', 'wpstack-connect' ) . ': <a href="' . $file_viewer_url . '" target="_blank" class="error-source-link">' . $error_file_path . '<span class="dashicons dashicons-visibility offset-down"></span></a><br />' . __( 'Line', 'wpstack-connect' ) . ': ' . $error_file_line;
					} 
					if ( defined( 'DISALLOW_FILE_EDIT' ) && ( true === constant( 'DISALLOW_FILE_EDIT' ) ) ) {
						$error_details = '<span class="error-details">' . $error_details . '</span><hr />' . $error_source . ': <a href="' . $error_source_plugin_uri . '" target="_blank" class="error-source-link">' . $error_source_plugin_name . '<span class="dashicons dashicons-external offset-up"></span></a><br />' . __( 'File', 'wpstack-connect' ) . ': ' . $error_file_path . '<br />' . __( 'Line', 'wpstack-connect' ) . ': ' . $error_file_line;
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

}
