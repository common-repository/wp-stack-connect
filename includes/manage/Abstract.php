<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once 'Interface.php';

abstract class WPStack_Connect_Manage_Abstract implements WPStack_Connect_Manage_Interface {


	public $result;

	public $prefix_sync = 'sync_';

	public function get_response() {
		return $this->result;
	}

}
