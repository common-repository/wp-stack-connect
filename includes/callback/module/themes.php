<?php

class WPStack_Connect_Themes {

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
        require_once ABSPATH . 'wp-includes/theme.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/theme.php';
	}

    public function manage() {
        $user_id = base64_decode( $this->account->user_id );
        wp_set_current_user( $user_id );
        $all_themes = wp_get_themes();
        $active     = wp_get_theme();
        $themes     = array();
    
        foreach ( $all_themes as $stylesheet => $theme ) {
            $themes[ $stylesheet ] = $this->get_theme_data( $theme );
        }
    
        $active_themes = $this->get_theme_data( $active );
    
        $data = array(
            'all_themes'    => $themes,
            'active_themes' => $active_themes,
        );
    
        wp_send_json( $data );
    }
    
    private function get_theme_data( $theme ) {
        return array(
            'Name'          => $theme->get( 'Name' ),
            'Description'   => $theme->get( 'Description' ),
            'Author'        => $theme->get( 'Author' ),
            'AuthorURI'     => $theme->get( 'AuthorURI' ),
            'Version'       => $theme->get( 'Version' ),
            'Template'      => $theme->get( 'Template' ),
            'Status'        => $theme->get( 'Status' ),
            'Tags'          => $theme->get( 'Tags' ),
            'TextDomain'    => $theme->get( 'TextDomain' ),
            'DomainPath'    => $theme->get( 'DomainPath' ),
        );
    }
    
    public function activate( $parameters ) {
        if ( isset( $parameters['activate'] ) && $parameters['activate'] ) {
            $user_id = base64_decode( $this->account->user_id );
    
            if ( is_numeric( $user_id ) && get_user_by( 'id', $user_id ) ) {
                wp_set_current_user( $user_id );

                $activated = switch_theme( $parameters['activate'] );
    
                if ( is_wp_error( $activated ) ) {
                    wp_send_json_error( $activated->get_error_message() );
                }
    
                $result = array( 'success' => true );
                wp_send_json( $result );
            } else {
                wp_send_json_error( __( 'Invalid user ID' ) );
            }
        } else {
            wp_send_json_error( __( 'Cannot process request due to insufficient data' ) );
        }
    }
    
    public function delete( $parameters ) {
        if ( isset( $parameters['delete'] ) && is_array( $parameters['delete'] ) && ! empty( $parameters['delete'] ) ) {
            $user_id = base64_decode( $this->account->user_id );
    
            if ( is_numeric( $user_id ) && get_user_by( 'id', $user_id ) ) {
                wp_set_current_user( $user_id );
    
                foreach ( $parameters['delete'] as $theme ) {
                    delete_theme( $theme );
                }
    
                $result = array( 'success' => true );
                wp_send_json( $result );
            } else {
                wp_send_json_error( __( 'Invalid user ID' ) );
            }
        } else {
            wp_send_json_error( __( 'Cannot process request due to insufficient data' ) );
        }
    }    

    public function install( $parameters ) {
        if ( isset( $parameters['install'] ) && is_array( $parameters['install'] ) && ! empty( $parameters['install'] ) ) {
            $errors  = array();
            $user_id = base64_decode( $this->account->user_id );
    
            if ( is_numeric( $user_id ) && get_user_by( 'id', $user_id ) ) {
                wp_set_current_user( $user_id );
    
                foreach ( $parameters['install'] as $slug ) {
                    $theme_info_url = 'https://api.wordpress.org/themes/info/1.2/?action=theme_information&request[slug]=' . $slug;
                    $response       = wp_safe_remote_get( $theme_info_url );
    
                    if ( is_wp_error( $response ) ) {
                        $errors[] = $slug;
                    } else {
                        $theme_data = json_decode( wp_remote_retrieve_body( $response ), true );
    
                        if ( ! empty( $theme_data ) && ! isset( $theme_data['error'] ) && isset( $theme_data['download_link'] ) ) {
                            $download_link = esc_url_raw( $theme_data['download_link'] );
                            $skin          = new Automatic_Upgrader_Skin();
                            $upgrader      = new Theme_Upgrader( $skin );
                            $install       = $upgrader->install( $download_link );
    
                            if ( is_wp_error( $install ) ) {
                                $errors[] = $slug;
                            }
                        } else {
                            $errors[] = $slug;
                        }
                    }
                }
    
                if ( ! empty( $errors ) ) {
                    $failed_themes = implode( ', ', $errors );
                    wp_send_json_error( sprintf( __( 'Failed to install themes: %s' ), $failed_themes ) );
                }
    
                $result = array( 'success' => true );
                wp_send_json( $result );
            } else {
                wp_send_json_error( __( 'Invalid user ID' ) );
            }
        } else {
            wp_send_json_error( __( 'Cannot process request due to insufficient data' ) );
        }
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