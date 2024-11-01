<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once dirname( __FILE__ ) . '/../vendor/autoload.php';

use WPStack_Connect_Vendor\Aws\Sdk;
use WPStack_Connect_Vendor\Aws\S3\S3Client;
use WPStack_Connect_Vendor\Aws\S3\MultipartUploader;
use WPStack_Connect_Vendor\Aws\Exception\MultipartUploadException;

class WPStack_Connect_Multipart_Uploads extends WPStack_Connect_Callback_Base {


	protected $s3Client;

	protected $bucket;

	protected $version;

	protected $region;

	protected $credentials = array();

	protected $is_multiregion = false;

	public function __construct( $params ) {
		$this->bucket   = ( $params['bucket'] ) ? $params['bucket'] : '';
		$this->version  = ( $params['version'] ) ? $params['version'] : '';
		$this->region   = ( $params['region'] ) ? $params['region'] : '';
		$this->s3Client = $this->setup_client( $params['is_multiregion'], $params['credentials'] );
	}

	protected function setup_client( $is_multiregion, $credentials ) {
		if ( $is_multiregion ) {
			$s3Client = $this->client_multiregion( $credentials );
		} else {
			$s3Client = $this->s3_client( $credentials );
		}

		return $s3Client;
	}

	protected function s3_client( $credentials ) {
		$s3Client = new S3Client(
			array(
				'version'                 => $this->version,
				'region'                  => $this->region,
				'credentials'             => $credentials,
				'use_accelerate_endpoint' => false,
			)
		);

		return $s3Client;
	}

	protected function client_multiregion( $credentials ) {
		$s3Client = ( new \WPStack_Connect_Vendor\Aws\Sdk() )->createMultiRegionS3(
			array(
				'version'                 => $this->version,
				'credentials'             => $credentials,
				'use_accelerate_endpoint' => false,
			)
		);

		return $s3Client;
	}

	public function do_upload( $key, $ofile ) {
		$uploader = new MultipartUploader(
			$this->s3Client,
			$ofile,
			array(
				'bucket'          => $this->bucket,
				'key'             => $key,
				'before_initiate' => function ( \WPStack_Connect_Vendor\Aws\Command $command ) {
					$command['CacheControl'] = 'max-age=3600';
				},
				'before_upload'   => function ( \WPStack_Connect_Vendor\Aws\Command $command ) {
					$command['RequestPayer'] = 'requester';
				},
				'before_complete' => function ( \WPStack_Connect_Vendor\Aws\Command $command ) {
					$command['RequestPayer'] = 'requester';
				},
			)
		);

		try {
			$result = $uploader->upload();
		} catch ( MultipartUploadException $e ) {
			$message = $e->getMessage() . "\n";
			wpstack_log( $message );
			throw new Exception( $message, 1 );
		}
	}
}


