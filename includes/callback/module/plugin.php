<?php

class WPStack_Connect_Plugin {

	public $account;

	function __construct( $callback_handler ) {
		$this->account = $callback_handler->account;
	}

    public function include_files() {
		require_once ABSPATH . WPINC . '/rewrite.php';
		$GLOBALS['wp_rewrite'] = new WP_Rewrite();
		require_once ABSPATH . 'wp-includes/capabilities.php';
		require_once ABSPATH . 'wp-includes/pluggable.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once dirname( __FILE__ ) . '/quiet_skin.php';
	}

    public function manage() {
        $user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );
        $filesystem  = new WPStack_Connect_Filesystem();
		$result_data =  $filesystem->plugin_theme();
		wp_send_json( $result_data );
    }

    public function activate( $parameters ) {
        if ( isset( $parameters['activate'] ) && is_array( $parameters['activate'] ) ) {
            $user_id = base64_decode( $this->account->user_id );
            wp_set_current_user( $user_id );
    
            foreach ( $parameters['activate'] as $plugin ) {
                $plugin = sanitize_text_field( $plugin );
                $this->check_custom_activation( $plugin );
                activate_plugin( $plugin );
            }
    
            $result = array( 'success' => true );
            wp_send_json( $result );
        } else {
            wp_send_json_error( esc_html__( 'Cannot process request due to insufficient data' ) );
        }
    }
    
    public function deactivate( $parameters ) {
        if ( isset( $parameters['deactivate'] ) && is_array( $parameters['deactivate'] ) ) {
            $user_id = base64_decode( $this->account->user_id );
            wp_set_current_user( $user_id );
    
            foreach ( $parameters['deactivate'] as $plugin ) {
                $plugin = sanitize_text_field( $plugin );
    
                if ( 'wp-stack-connect/init.php' !== $plugin ) {
                    $this->check_custom_deactivation( $plugin );
                    deactivate_plugins( $plugin );
                }
            }
    
            $result = array( 'success' => true );
            wp_send_json( $result );
        } else {
            wp_send_json_error( esc_html__( 'Cannot process request due to insufficient data' ) );
        }
    }  
    
    public function delete( $parameters ) {
        if ( isset( $parameters['delete'] ) && is_array( $parameters['delete'] ) ) {
            $user_id = base64_decode( $this->account->user_id );
            wp_set_current_user( $user_id );
    
            $delete_plugins  = $parameters['delete'];
            $delete_plugins  = array_map( 'sanitize_text_field', $delete_plugins );
            $search_wp_stack = array_search( 'wp-stack-connect/init.php', $delete_plugins );
            if ( false !== $search_wp_stack ) {
                unset( $delete_plugins[ $search_wp_stack ] );
            }

            foreach ( $delete_plugins as $plugin ) {
                $this->check_custom_deactivation( $plugin );
                deactivate_plugins( $plugin );
            }
    
            delete_plugins( $delete_plugins );
            $result = array( 'success' => true );
            wp_send_json( $result );
        } else {
            wp_send_json_error( esc_html__( 'Cannot process request due to insufficient data' ) );
        }
    }

    private function check_custom_activation( $plugin ) {
        switch ( $plugin ) {
            case 'breeze/breeze.php':
                $this->breeze_requirements();
                break;
            default:
                break;
        }
    }

    private function breeze_requirements() {
        require_once WP_PLUGIN_DIR . '/breeze/inc/breeze-configuration.php';
        require_once WP_PLUGIN_DIR . '/breeze/inc/cache/config-cache.php';
    }

    private function check_custom_deactivation( $plugin ) {
        switch ( $plugin ) {
            case 'breeze/breeze.php':
                $this->breeze_requirements();
                break;
            default:
                break;
        }
    }

    public function install( $parameters ) {
        if ( $parameters['install'] ) {
            $user_id = base64_decode( $this->account->user_id );
			wp_set_current_user( $user_id );
            foreach ( $parameters['install'] as $slug => $link ) {
                $success = true;
                if ( 'WPStack-upload' == $slug ) {
                    $skin       = new WPStack_Connect_Quiet_Skin();
                    $upgrader   = new Plugin_Upgrader( $skin );
                    $install    = $upgrader->install( $link );
                    if ( is_wp_error( $install ) ) {
                        $success = false;
                    }
                } else {
                    $plugin_dir = WP_PLUGIN_DIR . '/' . $slug;    
                    if ( ! is_dir( $plugin_dir ) ) {
                        $api = plugins_api(
                            'plugin_information',
                            array(
                                'slug'   => $slug,
                                'fields' => array(
                                    'short_description' => false,
                                    'sections'          => false,
                                    'requires'          => false,
                                    'rating'            => false,
                                    'ratings'           => false,
                                    'downloaded'        => false,
                                    'last_updated'      => false,
                                    'added'             => false,
                                    'tags'              => false,
                                    'compatibility'     => false,
                                    'homepage'          => false,
                                    'donate_link'       => false,
                                ),
                            )
                        );
                        
                        $skin       = new WPStack_Connect_Quiet_Skin( array( 'api' => $api ) );
                        $upgrader   = new Plugin_Upgrader( $skin );
                        $install    = $upgrader->install( $api->download_link );
                        if ( is_wp_error( $install ) ) {
                            $success = false;
                        }
                    }
                }

                if ( $parameters['activate'] && true == $success ) {
                    $this->install_activate();
                }
            }

            if ( $parameters['clear_cache'] ) {
                $cache = new WPStack_Connect_Cache_Handle();
                $cache->clear_cache();
            }

            $result = array( 'success' => true );
            wp_send_json( $result );
        } else {
            wp_send_json_error( esc_html__( 'Cannot process request due to insufficient data' ) );
        } 
    }

    private function install_activate() {
        $latest_install = $this->get_last_plugin();
        if ( $latest_install['main_file'] ) {
            $this->check_custom_activation( $latest_install['main_file'] );
            activate_plugin( $latest_install['main_file'] );
        }
    }

    private function get_last_plugin() {
        $wp_plugins_dir  = WP_PLUGIN_DIR;
        $plugin_contents = scandir( $wp_plugins_dir );
        $last_plugin_dir = array_filter( $plugin_contents, 
            function ( $dir ) use ( $wp_plugins_dir ) {
                return is_dir( $wp_plugins_dir . '/' . $dir ) && $dir != '.' && $dir != '..';
            } );
        
        
        
        usort( $last_plugin_dir, 
            function( $a, $b ) use ( $wp_plugins_dir ) {
                return filemtime( $wp_plugins_dir . '/' . $b ) - filemtime( $wp_plugins_dir . '/' . $a );
            } );

        if ( ! empty( $last_plugin_dir ) ) {
            $latest_plugin_dir   = $wp_plugins_dir . '/' . $last_plugin_dir[0];
            $latest_plugin_files = scandir( $latest_plugin_dir );
            $main_plugin_file    = null;
            $plugin_data         = [];
            foreach ( $latest_plugin_files as $file ) {
                if ( 'php' == pathinfo( $file, PATHINFO_EXTENSION ) ) {
                    $plugin_data = get_plugin_data( $latest_plugin_dir . '/' . $file );
                    if( !empty( $plugin_data['Name'] ) ) {
                        $main_plugin_file = $file;
                        break;
                    }
                }
            }
        
            return array(
                'slug'      => $last_plugin_dir[0],
                'main_file' => is_null( $main_plugin_file ) ? null : $last_plugin_dir[0] .'/' . $main_plugin_file,
                'data'      => $plugin_data
            );

        }

        return null;
    }

	public function process( $request ) {
        $this->include_files();
		switch ( $request->method ) {
			case 'manage':
				$this->manage();
				break;
			
            case 'activate':
				$this->activate( $request->params );
				break;
            
            case 'deactivate':
				$this->deactivate( $request->params );
				break;
            
            case 'delete':
				$this->delete( $request->params );
				break;
            
            case 'install':
				$this->install( $request->params );
				break;

			default:
				break;
		}
	}
}