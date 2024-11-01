<?php

class WPStack_Connect_Request {


	public $module;
	public $account;
	public $sig;
	public $is_sha1;
	public $params;
	public $method;
	public $subs;
    public $debug_log;

	function __construct( $account, $in_params ) {
        $this->params       = isset( $in_params['params'] ) ? (is_array($in_params['params']) ? $in_params['params'] : []) : [];
        $this->account      = $account;
        $this->sig          = isset( $in_params['sig'] ) ? sanitize_text_field( $in_params['sig'] ) : false;
        $this->method       = isset( $in_params['method'] ) ? sanitize_text_field( $in_params['method'] ) : null;
        $this->is_sha1      = isset( $in_params['is_sha1'] ) ? (bool) $in_params['is_sha1'] : true;
        $this->module       = isset( $in_params['module'] ) ? sanitize_text_field( $in_params['module'] ) : null;
        $this->subs         = isset( $in_params['subs'] ) ? sanitize_text_field( $in_params['subs'] ) : null;
        $this->debug_log    = isset( $in_params['debug_log'] ) ? sanitize_text_field( $in_params['debug_log'] ) : null;
    }
}
