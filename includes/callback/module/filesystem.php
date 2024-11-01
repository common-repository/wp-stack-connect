<?php

class WPStack_Connect_Filesystem_Core extends WPStack_Connect_Callback_Base {

	public $settings;
	public $account;

	protected function rm_files( $files ) {
		$result = array();

		foreach ( $files as $file ) {
			$file_result = array();

			if ( file_exists( $file ) ) {

				$file_result['status'] = unlink( $file );

				if ( $file_result['status'] === false ) {
					$file_result['error'] = 'UNLINK_FAILED';
				}
			} else {

				$file_result['status'] = true;
				$file_result['error']  = 'NOT_PRESENT';

			}

			$result[ $file ] = $file_result;
		}

		$result['status'] = true;

		return $result;
	}

	protected function file_exists( $filePath )
	{
		$return = [
			'status' => 'error',
			'message' => 'Not found !',
			'code' => 404
		];
		if (file_exists( ABSPATH.$filePath )) {
			$return = [
				'status' => 'success',
				'message' => 'File exists !',
				'code' => 200
			];
		}

		return $return;
	}

	protected function write_file( $params )
	{
		$path 	  = sanitize_text_field( ABSPATH . $params['filepath'] );
		$filename = sanitize_file_name( $params['filename'] );
		$content  = $params['is_encrypt'] ? base64_decode( sanitize_text_field( $params['content'] ) ) : sanitize_text_field( $params['content'] );
		$filepath = $path . $filename;

		$handle = fopen( $filepath, 'w+' );

		if ( ! $handle ) {
			return [
				'status'  => 'error',
				'message' => "File $filepath created error !",
				'code'    => 500,
			];

			exit;
		}

		fwrite( $handle, $content );
		fclose( $handle );
		return [
			'status'  => 'success',
			'message' => "File $filepath created !",
			'code'    => 200,
		];
	}

	protected function scan_dirs( $params )
	{
		$filesystem = new WPStack_Connect_Filesystem();
		$filelists = $filesystem->scan_files_glob($params);

		return $filelists;
	}

	protected function filelists_info( $params )
	{
		$filesystem = new WPStack_Connect_Filesystem();
		$filelists = $filesystem->scan_files_glob($params);

		return $filelists;
	}

	protected function tables_info($params)
	{
		$filesystem = new WPStack_Connect_Filesystem();
		$filelists = $filesystem->tables_info($params);

		return $filelists;
	}

	protected function scan_tables()
	{
		$filesystem = new WPStack_Connect_Filesystem();
		$filelists = $filesystem->scan_tables();

		return $filelists;
	}

	public function process( $request ) {
		$resp = array();

		switch ( $request->method ) {
			case 'rm_files':
				$resp = $this->rm_files( $request->params['files'] );
				break;

			case 'file_exists':
				$resp = $this->file_exists( $request->params['file'] );
				break;
			
			case 'write_file':
				$resp = $this->write_file( $request->params );
				break;
			
			case 'filelists_info':
				$resp = $this->filelists_info( $request->params );
				break;

			case 'tables_info':
				$resp = $this->tables_info( $request->params );
				break;

			case 'scan_tables':
				$resp = $this->scan_tables();
				break;

			default:
				break;
		}

		if ( is_array( $resp ) ) {
			$resp = $resp;
		}
		return $resp;
	}
}
