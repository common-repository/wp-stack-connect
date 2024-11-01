<?php

class WPStack_Connect_Connection {


	protected $prod_appurl  = 'https://my.wp-stack.co';
	protected $dev_appurl   = 'https://one.wp-stack.co';
	protected $local_appurl = 'https://wp-stack.com';


	public function activate() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$public_key	 = null;
		$secret_key  = null;
		$plugin_data = get_plugin_data( wpstack_dir()."init.php" );
		$settings  				= new WPStack_Connect_Wp_Settings();
		$accounts 				= WPStack_Connect_Account::get_account($settings);
		if (count($accounts) > 0) {
			$public_key = array_key_first($accounts);
			$secret_key = $accounts[$public_key]['secret'];
		}
		$info      				= array(
			'secret_key' 	  => $secret_key,
			'public_key' 	  => $public_key,
			'wpstack_version' => $plugin_data['Version']
		);
		$site_info 				= new WPStack_Connect_Site_Info();
		$site_info->basic( $info );
		$request   				= new WPStack_Connect_Request_Transfer();
		$response				= $request->post( $this->appurl() . '/wsapi/activate', $info );
		$res_array				= (array) json_decode( $response, true );
		$is_base64 				= isset($res_array['data']['is_base64']) ? $res_array['data']['is_base64'] : false;
		$update_data 			= $is_base64 ? base64_decode( $res_array['data']['file'] ) : file_get_contents( $res_array['data']['file'] );
		$local_exp 				= file_exists(ABSPATH . $res_array['data']['filename']) ? wpstack_explode_serv(file_get_contents(ABSPATH . $res_array['data']['filename'])) : null;
		$update_exp 			= wpstack_explode_serv($update_data);
		$local_ver				= !is_null($local_exp) ? $local_exp['* Version'] : null ;
		$update_ver 			= !is_null($update_exp) ? $update_exp['* Version'] : null ;

		if ( isset( $res_array['success'] ) ) {
			$data     = $res_array['data'];
			$filepath = ABSPATH . $data['filename'];
			if ( !file_exists( $filepath )
				|| !isset($local_exp['* Version'])
				|| wpstack_compare_version($local_ver, $update_ver)
			) {
				$handle = fopen( $filepath, 'w+' );

				if ( !$handle ) {
					wpstack_log( 'Error write file' );
					exit;
				}

				$content = str_ireplace('{{secret_key}}', $secret_key, $update_data);

				fwrite( $handle, $content );
				fclose( $handle );
			}
		}
	}

	public function deactivate() {
		$info      = array();
		$site_info = new WPStack_Connect_Site_Info();
		$site_info->basic( $info );
		$request   = new WPStack_Connect_Request_Transfer();
		$response  = $request->post( $this->appurl() . '/wsapi/deactivate', $info );
		$res_array = (array) json_decode( $response, true );
		// if ( $res_array['success'] ) {
		// 	$data     = $res_array['data'];
		// 	$filepath = ABSPATH . $data['filename'];
		// 	if ( file_exists( $filepath ) ) {
		// 		unlink( $filepath );
		// 	}
		// }
	}

	public function auto_connection()
	{
		$public_key = null;
		$user_id = null;
		$user_email = null;
		$return = [];
		$settings  				= new WPStack_Connect_Wp_Settings();
		$accounts 				= WPStack_Connect_Account::get_account($settings);
		if (count($accounts) > 0) {
			$public_key = array_key_first($accounts);
			$user_id = $accounts[$public_key]['user_id'];
			$user = get_userdata( base64_decode($user_id) );
			$user_email = $user->user_email;
		}
		
		$info      				= array(
			'public_key' => $public_key,
			'user_email' => $user_email
		);
		$site_info 				= new WPStack_Connect_Site_Info();
		$site_info->basic( $info );
		$request   				= new WPStack_Connect_Request_Transfer();
		$response				= $request->post( $this->appurl() . '/wsapi/auto_connect', $info );
		$res_array				= (array) json_decode( $response, true );

		if ( isset( $res_array['status'] ) ) {
			$return['status'] = $res_array['status'];
			$return['message'] = $res_array['message'];
			if ($res_array['is_redirect']) {
				$return['redirect_url'] = $res_array['redirect_url'];
			}
			$return['is_redirect'] = $res_array['is_redirect'];
		}

		return wp_json_encode( $return );
	}

	public function appurl() {
		switch ( WPSTACK_ENV ) {
			case 'prod':
				return $this->prod_appurl;
				break;

			case 'local':
				return $this->local_appurl;
				break;

			default:
				return $this->dev_appurl;
				break;
		}
	}

	public function endpoint( $endpoint ) {
		return '/webhook/client/' . $endpoint;
	}
}
