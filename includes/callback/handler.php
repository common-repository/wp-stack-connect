<?php

class WPStack_Connect_Callback_Handler {


	public $settings;
	public $site_info;
	public $request;
	public $account;
	public $response;

	function __construct( $settings, $site_info, $request, $account, $response ) {
		$this->request   = $request;
		$this->site_info = $site_info;
		$this->request   = $request;
		$this->account   = $account;
		$this->response  = $response;
		$this->settings  = $settings;
	}

	public function execute() {
		$this->route_request();
		$resp       = array(
			'site_title' => get_bloginfo(),
			'site_url'   => get_site_url()
		);

		$this->response->terminate( $resp );
	}

	public function route_request() {
		switch ( $this->request->module ) {
			case 'backup':
				require_once dirname( __FILE__ ) . '/module/backup.php';
				$module = new WPStack_Connect_backup();
				break;

			case 'publisher':
				require_once dirname( __FILE__ ) . '/module/publisher.php';
				$module = new WPStack_Connect_publisher( $this );
				break;

			case 'media':
				require_once dirname( __FILE__ ) . '/module/media.php';
				$module = new WPStack_Connect_media( $this );
				break;

			case 'post':
				require_once dirname( __FILE__ ) . '/module/post.php';
				$module = new WPStack_Connect_post( $this );
				break;

			case 'smart_update':
				require_once dirname( __FILE__ ) . '/module/smart_update.php';
				$module = new WPStack_Connect_Smart_Update( $this );
				break;

			case 'content_manager':
				require_once dirname( __FILE__ ) . '/module/content-manager.php';
				$module = new WPStack_Connect_Content_Manager( $this );
				break;

			case 'code_inserter':
				require_once dirname( __FILE__ ) . '/module/code-inserter.php';
				$module = new WPStack_Connect_Code_Inserter( $this );
				break;

			case 'activity_log':
				require_once dirname( __FILE__ ) . '/module/activity_log.php';
				$module = new WPStack_Connect_activity_Log( $this );
				break;

			case 'auto_link':
				require_once dirname( __FILE__ ) . '/module/auto-link.php';
				$module = new WPStack_Connect_Auto_Link( $this );
				break;

			case 'users':
				require_once dirname( __FILE__ ) . '/module/users.php';
				$module = new WPStack_Connect_Users( $this );
				break;

			case 'filesystem':
				require_once dirname( __FILE__ ) . '/module/filesystem.php';
				$module = new WPStack_Connect_Filesystem_Core();
				break;

			case 'system_info':
				require_once dirname( __FILE__ ) . '/module/system_info.php';
				$module = new WPStack_Connect_SystemInfo( $this );
				break;
			
			case 'login_logs':
				require_once dirname( __FILE__ ) . '/module/login_logs.php';
				$module = new WPStack_Connect_LoginLogs( $this );
				break;
			
			case 'plugin':
				require_once dirname( __FILE__ ) . '/module/plugin.php';
				$module = new WPStack_Connect_Plugin( $this );
				break;
			
			case 'themes':
				require_once dirname( __FILE__ ) . '/module/themes.php';
				$module = new WPStack_Connect_Themes( $this );
				break;

			default:
				require_once dirname( __FILE__ ) . '/module/debug-log.php';
				$module = new WPStack_Connect_Debug_Log( $this );
				break;
		}

		if ( $module ) {
			$parameters = $this->request;
			wpstack_log( 'Backup log parameters : ' );
			wpstack_log( $parameters );
			$resp = $module->process( $parameters );
			if ( $resp === false ) {
				$resp = array(
					'statusmsg' => 'Bad Command',
					'status'    => false,
				);
			}
			$resp = array(
				$parameters->module => array(
					$parameters->method => $resp,
				),
			);
			$this->response->add_status( 'callbackresponse', $resp );
			return 1;
		} else {
			wp_send_json_error( 'Module ' . $parameters->module . ' does not exist' );
		}
	}
}
