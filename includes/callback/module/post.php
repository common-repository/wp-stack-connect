<?php

class WPStack_Connect_post {

	public $account;

	function __construct( $callback_handler ) {
		$this->account = $callback_handler->account;
	}

	public function include_files() {
		require_once ABSPATH . WPINC . '/default-constants.php';
		wp_functionality_constants();
		require_once ABSPATH . WPINC . '/class-wp.php';
		$GLOBALS['wp'] = new WP();
		require_once ABSPATH . WPINC . '/rewrite.php';
		$GLOBALS['wp_rewrite'] = new WP_Rewrite();
		require_once ABSPATH . 'wp-includes/capabilities.php';
		require_once ABSPATH . 'wp-includes/formatting.php';
		require_once ABSPATH . 'wp-includes/link-template.php';
		require_once ABSPATH . 'wp-includes/pluggable.php';
		require_once ABSPATH . 'wp-includes/post.php';
		require_once ABSPATH . 'wp-includes/post-thumbnail-template.php';
		require_once ABSPATH . 'wp-includes/post-template.php';
		require_once ABSPATH . 'wp-includes/query.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	public function create( $parameters ) {
		if ( $parameters['post'] ) {
			$user_id = base64_decode( $this->account->user_id );
			wp_set_current_user( $user_id );
			$post_params = $parameters['post'];
			$new_post    = array(
				'post_type'     => ( array_key_exists( 'type', $post_params ) ) ? sanitize_text_field( $post_params['type'] ) : 'post',
				'post_title'    => ( array_key_exists( 'title', $post_params ) ) ? sanitize_text_field( $post_params['title'] ) : ' ',
				'post_content'  => ( array_key_exists( 'content', $post_params ) ) ? wp_kses_post( $post_params['content'] ) : ' ',
				'post_author'   => ( array_key_exists( 'author', $post_params ) ) ? absint( $post_params['author'] ) : null,
				'post_status'   => ( array_key_exists( 'status', $post_params ) ) ? sanitize_text_field( $post_params['status'] ) : 'publish',
				'post_name'     => ( array_key_exists( 'slug', $post_params ) ) ? sanitize_title( $post_params['slug'] ) : null,
				'post_date'     => ( array_key_exists( 'date', $post_params ) ) ? sanitize_text_field( $post_params['date'] ) : date( 'Y-m-d H:i:s', time() ),
				'post_date_gmt' => ( array_key_exists( 'date', $post_params ) ) ? sanitize_text_field( get_gmt_from_date( $post_params['date'] ) ) : get_gmt_from_date( date( 'Y-m-d H:i:s', time() ) ),
			);

			$post_id     = wp_insert_post( wp_slash( $new_post ), true, false );
			if ( is_wp_error( $post_id ) ) {
				wp_send_json_error( 'Error creating post' );
			}

			if ( array_key_exists( 'featured_media', $post_params ) ) {
				set_post_thumbnail( $post_id, absint( $post_params['featured_media'] ) );
			}

			if ( array_key_exists( 'tags', $post_params ) ) {
				$new_tags = array_map( 'intval', $post_params['tags'] );
				wp_set_post_tags( $post_id, $new_tags );
			}

			if ( array_key_exists( 'categories', $post_params ) ) {
				$new_categories = array_map( 'intval', $post_params['categories'] );
				wp_set_post_categories( $post_id, $new_categories );
			}

			if ( array_key_exists( 'seo', $parameters ) ) {
				$seo_params            = $parameters['seo'];
				$seo_params['post_id'] = $post_id;
				$plugin_management     = new WPStack_Connect_Manage_Plugins();
				$seo_plugin            = $plugin_management->validate_seo_plugin();
				$post_management       = new WPStack_Connect_Manage_Posts();
				$post_management->create_seo_post_meta( $seo_plugin, $seo_params );
			}

			$post = get_post( $post_id );
			if ( $post ) {
				$return = $this->post_return( $post );
				$this->clear_cache();
				wp_send_json( $return );
			}

			wp_send_json_error( 'Something wrong' );

		}

		wp_send_json_error( 'Cannot process request due to insufficient data' );

	}

	public function get( $parameters ) {
		$user_id = base64_decode( $this->account->user_id );
		wp_set_current_user( $user_id );
		if ( array_key_exists( 'post_id', $parameters ) ) {
			$post_id = absint( $parameters['post_id'] );
			$post    = get_post( $post_id );
			if ( $post ) {
				$return = $this->post_return( $post, true );
				wp_send_json( $return );
			}

			wp_send_json_error( 'Invalid post id' );

		} elseif ( array_key_exists( 'filter', $parameters ) ) {
			$posts                    = array();
			$params                   = array();
			$params['post_type']      = ( array_key_exists( 'type', $parameters ) ) ? sanitize_text_field( $parameters['type'] ) : 'post';
			$params['posts_per_page'] = ( array_key_exists( 'per_page', $parameters ) ) ? absint( $parameters['per_page'] ) : 10;
			if ( array_key_exists( 'author', $parameters ) ) {
				$params['author'] = absint( $parameters['author'] );
			}

			if ( array_key_exists( 'categories', $parameters ) ) {
				$params['cat'] = absint( $parameters['categories'] );
			}

			if ( array_key_exists( 'tags', $parameters ) ) {
				$params['tag_id'] = absint( $parameters['tags'] );
			}

			if ( array_key_exists( 'status', $parameters ) ) {
				$params['post_status'] = sanitize_text_field( $parameters['status'] );
			}

			if ( array_key_exists( 'date', $parameters ) ) {
				$date_query = $parameters['date'];
				if (
					array_key_exists( 'after', $date_query ) &&
					array_key_exists( 'before', $date_query )
				) {
					$params['date_query'] = array(
						array(
							'after'  => sanitize_text_field( $date_query['after'] ),
							'before' => sanitize_text_field( $date_query['before'] ),
						),
					);
				}
			}

			$query      = new WP_Query();
			$post_query = $query->query( $params );
			wp_reset_postdata();

			if ( is_array( $post_query ) ) {
				foreach ( $post_query as $post_result ) {
					$post_id = $post_result->ID;
					$post    = get_post( $post_id );
					array_push( $posts, $this->post_return( $post ) );
				}
			}

			wp_send_json( $posts );
		}

		wp_send_json_error( 'Cannot process request due to insufficient data' );

	}

	public function update( $parameters ) {
		if ( $parameters['post_id'] ) {
			$user_id = base64_decode( $this->account->user_id );
			wp_set_current_user( $user_id );
			$post_id = $parameters['post_id'];
			$post    = get_post( $post_id );
			if ( $post ) {
				$post_params = ( array_key_exists( 'post', $parameters ) ) ? $parameters['post'] : array();
				$edited_post = array(
					'ID'            => $post_id,
					'post_title'    => ( array_key_exists( 'title', $post_params ) ) ? sanitize_text_field( $post_params['title'] ) : get_the_title( $post_id ),
					'post_content'  => ( array_key_exists( 'content', $post_params ) ) ? wp_kses_post( $post_params['content'] ) : apply_filters( 'the_content', $post->post_content ),
					'post_author'   => ( array_key_exists( 'author', $post_params ) ) ? absint( $post_params['author'] ) : (int) $post->post_author,
					'post_status'   => ( array_key_exists( 'status', $post_params ) ) ? sanitize_text_field( $post_params['status'] ) : 'publish',
					'post_type' 	=> ( array_key_exists( 'type', $post_params ) ) ? $post_params['type'] : $post->post_type,
					'post_name'     => ( array_key_exists( 'slug', $post_params ) ) ? sanitize_title( $post_params['slug'] ) : $post->post_name,
					'post_date'     => ( array_key_exists( 'date', $post_params ) ) ? sanitize_text_field( $post_params['date'] ) : $post->post_date,
					'post_date_gmt' => ( array_key_exists( 'date', $post_params ) ) ? sanitize_text_field( get_gmt_from_date( $post_params['date'] ) ) : $post->post_date_gmt,
				);

				if ( ! empty( $edited_post['post_name'] ) && in_array( $edited_post['post_status'], array( 'draft', 'pending' ), true ) ) {
					$post_parent              = ! empty( $post->post_parent ) ? $post->post_parent : 0;
					$edited_post['post_name'] = wp_unique_post_slug( $edited_post['post_name'], $post->ID, 'publish', $post->post_type, $post_parent );
				}

				$updated_post = wp_update_post( wp_slash( $edited_post ), true, false );

				if ( is_wp_error( $updated_post ) ) {
					wp_send_json_error( 'Error updating post' );
				}

				if ( array_key_exists( 'featured_media', $post_params ) ) {
					if ( $post_params['featured_media'] == 'remove' ) {
						delete_post_thumbnail( $post_id );
					} else {
						set_post_thumbnail( $post_id, $post_params['featured_media'] );
					}
				}

				if ( array_key_exists( 'meta', $post_params ) ) {
					$metas = $post_params['meta'];
					if ( is_array( $metas ) ) {
						foreach ( $metas as $key => $value ) {
							update_post_meta( $post_id, $key, $value );
						}
					}
				}

				if ( array_key_exists( 'tags', $post_params ) ) {
					$new_tags = ( is_array( $post_params['tags'] ) ) ? array_map( 'intval', $post_params['tags'] ) : '';
					wp_set_post_tags( $post_id, $new_tags );
				}

				if ( array_key_exists( 'categories', $post_params ) ) {
					$new_categories = ( is_array( $post_params['categories'] ) ) ? array_map( 'intval', $post_params['categories'] ) : array();
					wp_set_post_categories( $post_id, $new_categories );
				}

				if ( array_key_exists( 'seo', $parameters ) ) {
					if ( ! empty( $parameters['seo'] ) ) {
						$seo_params            = $parameters['seo'];
						$seo_params['post_id'] = $post_id;
						$plugin_management     = new WPStack_Connect_Manage_Plugins();
						$seo_plugin            = $plugin_management->validate_seo_plugin();
						$post_management       = new WPStack_Connect_Manage_Posts();
						$post_management->create_seo_post_meta( $seo_plugin, $seo_params );
					}
				}

				$post = get_post( $post_id );
				if ( $post ) {
					$return = $this->post_return( $post );
					$this->clear_cache();
					wp_send_json( $return );
				}
			}

			wp_send_json_error( 'Invalid post id' );

		}

		wp_send_json_error( 'Cannot process request due to insufficient data' );

	}

	public function delete( $parameters ) {
		if ( $parameters['post_id'] ) {
			$user_id = base64_decode( $this->account->user_id );
			wp_set_current_user( $user_id );
			$post_id = sanitize_text_field( $parameters['post_id'] );
			$post    = get_post( $post_id );
			if ( $post ) {
				if ( $post->post_status == 'trash' ) {
					$return = $this->post_return( $post );
					wp_send_json( $return );
				}

				$deleted_post = wp_delete_post( $post_id );
				if ( is_wp_error( $deleted_post ) ) {
					wp_send_json_error( 'Error deleting post' );
				}

				$post = get_post( $post_id );
				if ( $post ) {
					$return = $this->post_return( $post );
					$this->clear_cache();
					wp_send_json( $return );
				}
			}

			wp_send_json_error( 'Invalid post id' );

		}

		wp_send_json_error( 'Cannot process request due to insufficient data' );

	}

	private function post_return( $post, $post_meta = false ) {
		$featured_media     = (int) get_post_thumbnail_id( $post->ID );
		$featured_media_url = ( $featured_media != 0 ) ? wp_get_attachment_url( $featured_media ) : null;
		$return             = array(
			'success'            => true,
			'id'                 => $post->ID,
			'date'               => $this->prepare_date_response( $post->post_date_gmt, $post->post_date ),
			'date_gmt'           => $this->prepare_date_gmt_response( $post->post_date_gmt, $post->post_date ),
			'modified'           => $this->prepare_date_response( $post->post_modified_gmt, $post->post_modified ),
			'slug'               => $post->post_name,
			'status'             => get_post_status( $post->ID ),
			'link'               => get_permalink( $post->ID ),
			'content'            => apply_filters( 'the_content', $post->post_content ),
			'title'              => get_the_title( $post->ID ),
			'author'             => (int) $post->post_author,
			'featured_media_url' => $featured_media_url,
			'categories'         => wp_get_post_categories( $post->ID ),
			'tags'               => $this->prepare_tags_response( $post->ID ),
			'type'               => $post->post_type,
		);

		if ( $post_meta ) {
			$plugin_management          = new WPStack_Connect_Manage_Plugins();
			$seo_plugin                 = $plugin_management->validate_seo_plugin();
			$post_management            = new WPStack_Connect_Manage_Posts();
			$custom_post_meta           = $post_management->get_seo_post_meta( $seo_plugin, $post->ID );
			$return['custom_post_meta'] = $custom_post_meta;
		}

		return $return;
	}

	protected function prepare_date_response( $date_gmt, $date = null ) {
		if ( isset( $date ) ) {
			return mysql_to_rfc3339( $date );
		}

		if ( '0000-00-00 00:00:00' === $date_gmt ) {
			return null;
		}

		return mysql_to_rfc3339( $date_gmt );
	}

	protected function prepare_date_gmt_response( $date_gmt, $date ) {
		if ( '0000-00-00 00:00:00' === $date_gmt ) {
			$post_date_gmt = get_gmt_from_date( $date );
		} else {
			$post_date_gmt = $date_gmt;
		}

		return $this->prepare_date_response( $post_date_gmt );
	}

	protected function prepare_tags_response( $post_id ) {
		$tags      = array();
		$post_tags = wp_get_post_tags( $post_id );
		if ( is_array( $post_tags ) ) {
			foreach ( $post_tags as $tag ) {
				array_push( $tags, $tag->term_id );
			}
		}

		return $tags;
	}

	protected function clear_cache() {
		$cache = new WPStack_Connect_Cache_Handle();
        $cache->clear_cache();
	}

	public function process( $request ) {
		$this->include_files();
		$resp = array();
		switch ( $request->method ) {
			case 'create':
				$resp = $this->create( $request->params );
				break;

			case 'get':
				$resp = $this->get( $request->params );
				break;

			case 'update':
				$resp = $this->update( $request->params );
				break;

			case 'delete':
				$resp = $this->delete( $request->params );
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
