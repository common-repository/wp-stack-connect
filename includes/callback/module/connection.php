<?php

class WPStack_Connect_Manage_Connection extends WPStack_Connect_Callback_Base {


	public $account;
	public $settings;
	public $response;
	public $request;

	function __construct( $account, $settings, $response, $request ) {
		$this->account  = $account;
		$this->settings = $settings;
		$this->response = $response;
		$this->request  = $request;
	}

	public function validate() {
		$return = array(
			'message' => 'Validate connection request',
			'code'    => 403,
		);

		if ( isset( $this->account->public, $this->account->secret ) ) {
			$this->settings->update_option( 'wpstack_connect_connected_status', 'connected' );
			$this->settings->update_option( 'wpstack_connect_website_subscription', $this->request->subs );
			$this->settings->update_option( 'wpstack_connect_message_status', 'public' );

			$connection = new WPStack_Connect_Connection();
			$connection->activate();

			$return = array(
				'message' => 'Connection success',
				'code'    => 200,
			);
		}

		return $return;
	}

	public function disconnect() {
		WPStack_Connect_Account::remove( $this->settings );

		$this->settings->update_option( 'wpstack_connect_connected_status', 'disconnected' );

		$return = array(
			'message' => 'Disconnect success',
			'code'    => 200,
		);

		return $return;
	}

	public function execute( $resp = array() ) {
		$this->route_request();
		$resp = array(
			'site_title' => get_bloginfo(),
			'site_url'   => get_site_url(),
			'home_url'   => get_home_url(),
			'public'     => $this->account->public,
			'secret'     => $this->account->secret,
			'requirements' => array(
				'ZipArchive' => wpstack_zip_archive_checking()
			)
		);

		$this->response->terminate( $resp );
	}

	public function validate_requirement()
	{
		$resp = array(
			'requirements' => array(
				'ZipArchive' => wpstack_zip_archive_checking()
			)
		);

		$this->response->terminate( $resp );
	}

	public function route_request() {
		switch ( $this->request->method ) {
			case 'validate':
				$method = $this->validate();
				break;

			case 'validate_requirement':
				$method = $this->validate_requirement();
				break;

			default:
				$method = $this->disconnect();
				break;
		}

		$resp = $method;
		if ( $resp === false ) {
			$resp = array(
				'statusmsg' => 'Bad Command',
				'status'    => false,
			);
		}
			$resp = array(
				$this->request->module => array(
					$this->request->method => $resp,
				),
			);
			$this->response->add_status( 'callbackresponse', $resp );
			return 1;
	}
}
