<?php

class WPStack_Connect_media {

	public $account;

	function __construct( $callback_handler ) {
		$this->account = $callback_handler->account;
	}

	public function include_files() {
		require_once ABSPATH . WPINC . '/rewrite.php';
		$GLOBALS['wp_rewrite'] = new WP_Rewrite();
		require_once ABSPATH . 'wp-includes/capabilities.php';
		require_once ABSPATH . 'wp-includes/pluggable.php';
		require_once ABSPATH . 'wp-includes/rest-api.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	public function upload( $parameters ) {
		$user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );
		$image_url	 = esc_url_raw( $parameters['source'] );
		$image_type  = end( explode( '/', getimagesize( $image_url )['mime'] ) );
		$uniq_name   = date( 'dmY' ) . '' . (int) microtime( true );
		$file_name   = $uniq_name . '.' . $image_type;
		$upload_dir  = wp_upload_dir();
		$upload_file = $upload_dir['path'] . '/' . $file_name;
		$contents    = file_get_contents( $image_url );
		$save_file   = fopen( $upload_file, 'w' );
		fwrite( $save_file, $contents );
		fclose( $save_file );
		$wp_filetype = wp_check_filetype( basename( $file_name ), null );
		$attachment  = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $file_name ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id     = wp_insert_attachment( $attachment, $upload_file );
		$image_new     = get_post( $attach_id );
		$fullsize_path = get_attached_file( $image_new->ID );
		$attach_data   = wp_generate_attachment_metadata( $attach_id, $fullsize_path );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		$image_data = wp_get_attachment_image_src( $attach_id, 'full' );
		if ( ! empty( $image_data ) ) {
			$source_url = array(
				'success'    => true,
				'id'         => $attach_id,
				'source_url' => esc_url( $image_data[0] ),
			);
			wp_send_json( $source_url );
		} else {
			wp_send_json_error( 'WordPress image source url empty' );
		}
	}

	public function process( $request ) {
		$this->include_files();
		$resp = array();
		switch ( $request->method ) {
			case 'upload':
				$resp = $this->upload( $request->params );
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

