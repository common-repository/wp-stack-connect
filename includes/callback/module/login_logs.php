<?php

class WPStack_Connect_LoginLogs {

	public $account;

	function __construct( $callback_handler ) {
		$this->account = $callback_handler->account;
	}

    public function settings( $parameters ) {
        if ( $parameters ) {
            update_option( 'wpstack_connect_website_subscription', 'premium' );
            $update = update_option( 'wpstack_connect_blocked_settings', $parameters );
            if ( is_wp_error( $update ) ) {
                wp_send_json_error( $update->get_error_message() );
            } else {
                $result = array( 'success' => true );
                wp_send_json( $result );
            }
        } else {
            wp_send_json_error( __( 'Cannot process request due to insufficient data' ) );
        }
    }

    public function block( $parameters ) {
        if ( $parameters['ip_address'] ) {
            update_option( 'wpstack_connect_website_subscription', 'premium' );
            $blocked_ips    = get_option( 'wpstack_connect_blocked_ips', array() );
            $sanitized_ip   = sanitize_text_field( $parameters['ip_address'] );
            if ( ! in_array( $sanitized_ip, $blocked_ips ) ) {
                $blocked_ips[] = $sanitized_ip;
                $update = update_option( 'wpstack_connect_blocked_ips', $blocked_ips );
                if ( is_wp_error( $update ) ) {
                    wp_send_json_error( $update->get_error_message() );
                }
            }
                
            $result = array( 
                'success'   => true,
                'severity'  => 1073,
                'timestamp' => time(),
            );

            wp_send_json( $result );
        } else {
            wp_send_json_error( __( 'Cannot process request due to insufficient data' ) );
        }
    }
    
    public function unblock( $parameters ) {
        if ( $parameters['ip_address'] ) {
            update_option( 'wpstack_connect_website_subscription', 'premium' );
            $blocked_ips    = get_option( 'wpstack_connect_blocked_ips', array() );
            $sanitized_ip   = sanitize_text_field( $parameters['ip_address'] );
            $search_blocked = array_search( $sanitized_ip, $blocked_ips );
            if ( false !== $search_blocked ) {
                unset( $blocked_ips[ $search_blocked ] );
            }

            $update = update_option( 'wpstack_connect_blocked_ips', $blocked_ips );
            if ( is_wp_error( $update ) ) {
                wp_send_json_error( $update->get_error_message() );
            } else {
                $result = array( 'success' => true );
                wp_send_json( $result );
            }
        } else {
            wp_send_json_error( __( 'Cannot process request due to insufficient data' ) );
        }
    }

	public function process( $request ) {
		switch ( $request->method ) {
			case 'settings':
				$this->settings( $request->params );
				break;

            case 'block':
                $this->block( $request->params );
                break;
			
            case 'unblock':
				$this->unblock( $request->params );
				break;

			default:
				break;
		}
	}
}
