<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpstack_cron_added_links' ) ) {

	function wpstack_cron_added_links( $schedules ) {
		$schedules['every_five_minutes'] = array(
			'interval' => 300,
			'display'  => __( 'Every 5 Minutes' ),
		);

		$schedules['every_hours'] = array(
			'interval' => 3600,
			'display'  => __( 'Every Hours' ),
		);
		return $schedules;
	}
}

add_filter( 'cron_schedules', 'wpstack_cron_added_links' );

// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'wpstack_cron_added_links' ) ) {
	wp_schedule_event( time(), 'every_five_minutes', 'wpstack_cron_added_links' );
}

// Hook into that action that'll fire every five minutes
if ( ! function_exists( 'wpstack_add_auto_links' ) ) {

	function wpstack_add_auto_links() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpstack_connect_auto_links';
		$myrows     = $wpdb->get_results( 
			$wpdb->prepare( 'SELECT `alid`,`link`,`keyword` FROM %i WHERE `status` = 1', $table_name)
		);

		if ( ! empty( $myrows ) && is_array( $myrows ) ) {
			foreach ( $myrows as $row ) {
				$link    = trim( $row->link );
				$keyword = trim( $row->keyword );
				$alid    = $row->alid;

				$args = array(
					'post_type'      => array( 'post' ),
					'post_status'    => array( 'publish' ),
					'posts_per_page' => '-1',
					'order'          => 'DESC',
					'orderby'        => 'id',
				);

				$posts = get_posts( $args );

				if ( is_array( $posts ) ) {
					foreach ( $posts as $post ) {
						$psid      = $post->ID;
						$same_link = wpstack_check_auto_links( $psid, $link );

						if ( true == $same_link ) {
							continue;
						}

						$added   = $wpdb->get_results( 
							$wpdb->prepare('SELECT `added`,`psid`,`titlink` FROM %i WHERE `alid`= %s', $table_name, $alid)
						);
						$array   = json_decode( json_encode( $added ), true );
						$add     = $array[0]['added'];
						$addarr  = array();
						$id_done = $array[0]['psid'];
						$idarr   = array();
						$titlink = $array[0]['titlink'];
						$titarr  = array();

						if ( ! empty( $add ) ) {
							$addarr = unserialize( $add );
						}

						if ( ! empty( $id_done ) ) {
							$idarr = json_decode( $id_done );
						}

						if ( ! empty( $titlink ) ) {
							$titarr = json_decode( $titlink );
							$titarr = json_decode( json_encode( $titarr ), true );
						}

						if ( count( $idarr ) >= count( $posts ) ) {
							$keys = array_keys( $addarr );
							$diff = array_diff( $keys, $idarr );
							if ( ! empty( $diff ) ) {
								foreach ( $diff as $d ) {
									unset( $addarr[ $d ] );
								}
							}

							$tkeys = array_keys( $titarr );
							$tdiff = array_diff( $tkeys, $idarr );
							if ( ! empty( $tdiff ) ) {
								foreach ( $tdiff as $td ) {
									unset( $titarr[ $td ] );
								}
							}
							$idarr = array();
						}

						if ( ! in_array( $psid, $idarr ) ) {
							$content = $post->post_content;
							$param   = array(
								'link'    => $link,
								'keyword' => $keyword,
							);
							$search  = wpstack_find_auto_links( $param, $content );

							if ( ! empty( $search ) && is_array( $search ) ) {
								foreach ( $search as $res ) {
									$content = preg_replace( '/' . preg_quote( $res['link'], '/' ) . '/', $res['keyword'], $content );
								}
							}

							$stripcont = strip_tags( $content );
							$word      = 300;

							if ( $word < str_word_count( $stripcont ) ) {
								$exploded   = explode( ' ', $content );
								$chunked    = array_chunk( $exploded, $word );
								$subcontent = implode( ' ', $chunked[0] );
								$stripsub   = strip_tags( $subcontent );
								$n          = $word;
								while ( $word > str_word_count( $stripsub ) ) {
									$n          = $n + 1;
									$chunked    = array_chunk( $exploded, $n );
									$subcontent = implode( ' ', $chunked[0] );
									$stripsub   = strip_tags( $subcontent );
								}
								if ( 1 < count( $chunked ) ) {
									$subcontent = implode( ' ', $chunked[0] );
									$newcontent = '';
									for ( $x = 1; $x < count( $chunked ); $x++ ) {
										$newcontent .= implode( ' ', $chunked[ $x ] ) . ' ';
									}
									$content = $newcontent;
									$count   = 0;
									while ( true ) {
										preg_match( '/(?:<a\s[^><]*?(?:class=["\'][^"\']*?wp-stack-auto-link[^"\']*?["\'])[^><]*?href=[\'\"]' . preg_quote( $link, '/' ) . '*[\'\"][^><]*?>|<a\s[^><]*?href=[\'\"]' . preg_quote( $link, '/' ) . '*[\'\"][^><]*?(?:class=["\'][^"\']*?wp-stack-auto-link[^"\']*?["\'])[^><]*?>)(?!<a)(' . preg_quote( $keyword, '/' ) . ')<\/a>/i', $content, $match, PREG_OFFSET_CAPTURE );

										if ( ! empty( $match ) ) {
											$first = '';
											if ( isset( $match[0][1] ) && is_int( $match[0][1] ) ) {
												$first   = substr( $content, 0, $match[0][1] );
												$content = substr( $content, $match[0][1] );
											}

											$chunk = wpstack_chunked_content( $word, $content );

											if ( $chunk ) {
												$subcontent = $subcontent . ' ' . $first . $chunk['subcontent'];
												$content    = $chunk['content'];
											} else {
												$content = $first . $content;
												break;
											}
										} else {
											$replace = '<a class="wp-stack-auto-link" target="_blank" href="' . $link . '">' . $keyword . '</a>';
											$regex   = '/(?!(?:[^<]+[>]|[^\[]+[\]]|[^>]+<\/[^>]+><\/a>|[^>]+<\/a>|[^>]+<\/h.>|[^>]+><\/h.>|[^>]+<\/script*>|[^>]+<\/code*>))\b($keyword)\b/imsU';
											$regexp  = str_replace( '$keyword', preg_quote( $keyword, '/' ), $regex );
											preg_match( $regexp, $content, $match, PREG_OFFSET_CAPTURE );
											$first = '';
											if ( isset( $match[0][1] ) && is_int( $match[0][1] ) ) {
												$first   = substr( $content, 0, $match[0][1] );
												$content = substr( $content, $match[0][1] );
											}

											$chunk = wpstack_chunked_content( $word, $content );

											if ( $chunk ) {
												$process    = preg_replace( $regexp, $replace, $chunk['subcontent'], 1, $res );
												$subcontent = $subcontent . ' ' . $first . $process;
												$content    = $chunk['content'];
												$count      = $count + $res;
											} else {
												$content = preg_replace( $regexp, $replace, $content, 1, $res );
												$content = $first . $content;
												$count   = $count + $res;
												break;
											}
										}
									}
									$content = $subcontent . ' ' . $content;
									$update  = array(
										'ID'           => $psid,
										'post_content' => $content,
									);
									wp_update_post( $update );
								}
							}

							$param  = array(
								'link'    => $link,
								'keyword' => $keyword,
							);
							$search = wpstack_find_auto_links( $param, $content );
							if ( ! empty( $search ) ) {
								$addarr[ $psid ] = count( $search );
								if ( 0 < count( $search ) ) {
									$title           = get_the_title( $psid );
									$perma           = get_permalink( $psid );
									$titarr[ $psid ] = array(
										'title' => $title,
										'perma' => $perma,
									);
								}
							} else {
								if ( isset( $titarr[ $psid ] ) ) {
									unset( $titarr[ $psid ] );
								}
							}
							array_push( $idarr, $psid );
							$data    = serialize( $addarr );
							$post_id = json_encode( $idarr );
							$tdata   = json_encode( $titarr );
							$wpdb->update(
								$table_name,
								array(
									'added'   => $data,
									'psid'    => $post_id,
									'titlink' => $tdata,
								),
								array( 'alid' => $alid )
							);
						}
					}
				}
			}
		}

	}
}

if ( ! function_exists( 'wpstack_find_auto_links' ) ) {

	function wpstack_find_auto_links( $param, $content ) {
		if ( ! empty( $param ) && is_array( $param ) && ! empty( $content ) ) {
			preg_match_all( '/(?:<a\s[^><]*?(?:class=["\'][^"\']*?wp-stack-auto-link[^"\']*?["\'])[^><]*?href=[\'\"]' . preg_quote( $param['link'], '/' ) . '*[\'\"][^><]*?>|<a\s[^><]*?href=[\'\"]' . preg_quote( $param['link'], '/' ) . '*[\'\"][^><]*?(?:class=["\'][^"\']*?wp-stack-auto-link[^"\']*?["\'])[^><]*?>)(?!<a)(' . preg_quote( $param['keyword'], '/' ) . ')<\/a>/i', $content, $matches );

			$return_matches = array();
			foreach ( $matches[0] as $key => $match ) {
				$return_matches[] = array(
					'link'    => $match,
					'keyword' => $matches[1][ $key ],
				);
			}
			return $return_matches;
		}
	}
}

if ( ! function_exists( 'wpstack_check_auto_links' ) ) {

	function wpstack_check_auto_links( $psid, $link ) {
		$striplink = '';
		$ownlink   = '';
		$ownlink   = get_permalink( $psid );
		if ( 'http://' == substr( $link, 0, 7 ) ) {
			$striplink = substr( $link, 7 );
		} elseif ( 'https://' == substr( $link, 0, 8 ) ) {
			$striplink = substr( $link, 8 );
		} elseif ( '//' == substr( $link, 0, 2 ) ) {
			$striplink = substr( $link, 2 );
		} else {
			$striplink = $link;
		}
		$stripown = '';
		if ( 'http://' == substr( $ownlink, 0, 7 ) ) {
			$stripown = substr( $ownlink, 7 );
		} elseif ( 'https://' == substr( $ownlink, 0, 8 ) ) {
			$stripown = substr( $ownlink, 8 );
		} elseif ( '//' == substr( $ownlink, 0, 2 ) ) {
			$stripown = substr( $ownlink, 2 );
		} else {
			$stripown = $ownlink;
		}

		if ( $striplink == $stripown ) {
			return true;
		}
		return false;
	}
}

if (! function_exists('wpstack_log_it')) {
	function wpstack_log_it( $message ) {
		if ( WP_DEBUG === true ) {
			if ( is_array( $message ) || is_object( $message ) ) {
				// error_log will be located according to server configuration
				// you can specify a custom location if needed like this
				// error_log( $var, 0, "full-path-to/error_log.txt")
				error_log( print_r( $message, true ) );
			} else {
				error_log( $message );
			}
		}
	}
}

if ( ! function_exists( 'wpstack_add_links_to_content' ) ) {

	function wpstack_add_links_to_content( $data, $postarr ) {
		if ( is_null( $data ) ) {
			$data                 = get_post( $postarr['ID'], ARRAY_A );
			$data['post_content'] = wp_slash( $data['post_content'] );
		}

		if ( 'post' === $data['post_type'] && 'publish' === $data['post_status'] ) {

			global $wpdb;
			$table_name = $wpdb->prefix . 'wpstack_connect_auto_links';
			$myrows     = $wpdb->get_results( 
				'SELECT `alid`,`link`,`keyword` FROM %s WHERE `status` = 1',
				$table_name
			);
			$content    = wp_unslash( $data['post_content'] );
			$psid       = $postarr['ID'];
			if ( is_array( $myrows ) ) {
				foreach ( $myrows as $row ) {
					$link      = trim( $row->link );
					$keyword   = trim( $row->keyword );
					$same_link = wpstack_check_auto_links( $psid, $link );
					$alid      = $row->alid;

					if ( true == $same_link ) {
						continue;
					}

					$param  = array(
						'link'    => $link,
						'keyword' => $keyword,
					);
					$search = wpstack_find_auto_links( $param, $content );

					if ( ! empty( $search ) && is_array( $search ) ) {
						foreach ( $search as $res ) {
							$content = preg_replace( '/' . preg_quote( $res['link'], '/' ) . '/', $res['keyword'], $content );
						}
					}

					$stripcont = strip_tags( $content );
					$word      = 300;
					$count     = 0;

					if ( $word < str_word_count( $stripcont ) ) {
						$exploded   = explode( ' ', $content );
						$chunked    = array_chunk( $exploded, $word );
						$subcontent = implode( ' ', $chunked[0] );
						$stripsub   = strip_tags( $subcontent );
						$n          = $word;
						while ( $word > str_word_count( $stripsub ) ) {
							$n          = $n + 1;
							$chunked    = array_chunk( $exploded, $n );
							$subcontent = implode( ' ', $chunked[0] );
							$stripsub   = strip_tags( $subcontent );
						}
						if ( 1 < count( $chunked ) ) {
							$subcontent = implode( ' ', $chunked[0] );
							$newcontent = '';
							for ( $x = 1; $x < count( $chunked ); $x++ ) {
								$newcontent .= implode( ' ', $chunked[ $x ] ) . ' ';
							}
							$content = $newcontent;
							while ( true ) {
								preg_match( '/(?:<a\s[^><]*?(?:class=["\'][^"\']*?wp-stack-auto-link[^"\']*?["\'])[^><]*?href=[\'\"]' . preg_quote( $link, '/' ) . '*[\'\"][^><]*?>|<a\s[^><]*?href=[\'\"]' . preg_quote( $link, '/' ) . '*[\'\"][^><]*?(?:class=["\'][^"\']*?wp-stack-auto-link[^"\']*?["\'])[^><]*?>)(?!<a)(' . preg_quote( $keyword, '/' ) . ')<\/a>/i', $content, $match, PREG_OFFSET_CAPTURE );

								if ( ! empty( $match ) ) {
									$first = '';
									if ( isset( $match[0][1] ) && is_int( $match[0][1] ) ) {
										$first   = substr( $content, 0, $match[0][1] );
										$content = substr( $content, $match[0][1] );
									}

									$chunk = wpstack_chunked_content( $word, $content );

									if ( $chunk ) {
										$subcontent = $subcontent . ' ' . $first . $chunk['subcontent'];
										$content    = $chunk['content'];
									} else {
										$content = $first . $content;
										break;
									}
								} else {
									$replace = '<a class="wp-stack-auto-link" target="_blank" href="' . $link . '">' . $keyword . '</a>';
									$regex   = '/(?!(?:[^<]+[>]|[^\[]+[\]]|[^>]+<\/[^>]+><\/a>|[^>]+<\/a>|[^>]+<\/h.>|[^>]+><\/h.>|[^>]+<\/script*>|[^>]+<\/code*>))\b($keyword)\b/imsU';
									$regexp  = str_replace( '$keyword', preg_quote( $keyword, '/' ), $regex );
									preg_match( $regexp, $content, $match, PREG_OFFSET_CAPTURE );
									$first = '';
									if ( isset( $match[0][1] ) && is_int( $match[0][1] ) ) {
										$first   = substr( $content, 0, $match[0][1] );
										$content = substr( $content, $match[0][1] );
									}

									$chunk = wpstack_chunked_content( $word, $content );

									if ( $chunk ) {
										$process    = preg_replace( $regexp, $replace, $chunk['subcontent'], 1, $res );
										$subcontent = $subcontent . ' ' . $first . $process;
										$content    = $chunk['content'];
										$count      = $count + $res;
									} else {
										$content = preg_replace( $regexp, $replace, $content, 1, $res );
										$content = $first . $content;
										$count   = $count + $res;
										break;
									}
								}
							}
							$content = $subcontent . ' ' . $content;
						}
					}
					$added   = $wpdb->get_results( 
						$wpdb->prepare( 'SELECT `added`,`psid`,`titlink` FROM %i WHERE `alid`= %s', $table_name, $alid)
					);
					$array   = json_decode( json_encode( $added ), true );
					$add     = $array[0]['added'];
					$addarr  = array();
					$id_done = $array[0]['psid'];
					$idarr   = array();
					$titlink = $array[0]['titlink'];
					$titarr  = array();

					if ( ! empty( $add ) ) {
						$addarr = unserialize( $add );
					}

					if ( ! empty( $id_done ) ) {
						$idarr = json_decode( $id_done );
					}

					if ( ! empty( $titlink ) ) {
						$titarr = json_decode( $titlink );
						$titarr = json_decode( json_encode( $titarr ), true );
					}

					if ( 0 < $count ) {
						if ( isset( $addarr[ $psid ] ) ) {
							if ( $count > $addarr[ $psid ] ) {
								wpstack_sent_auto_link_api();
							}
						} else {
							wpstack_sent_auto_link_api();
						}
					}

					$addarr[ $psid ] = $count;
					if ( 0 < $addarr[ $psid ] ) {
						$title           = get_the_title( $psid );
						$perma           = get_permalink( $psid );
						$titarr[ $psid ] = array(
							'title' => $title,
							'perma' => $perma,
						);
					} else {
						if ( isset( $titarr[ $psid ] ) ) {
							unset( $titarr[ $psid ] );
						}
					}

					if ( ! in_array( $psid, $idarr ) ) {
						array_push( $idarr, $psid );
					}
					$added_db  = serialize( $addarr );
					$post_id   = json_encode( $idarr );
					$added_tdb = json_encode( $titarr );

					$wpdb->update(
						$table_name,
						array(
							'added'   => $added_db,
							'psid'    => $post_id,
							'titlink' => $added_tdb,
						),
						array( 'alid' => $alid )
					);
				}
				$data['post_content'] = wp_slash( $content );
			}
		}
		return $data;
	}
}


if ( ! function_exists( 'wpstack_sent_auto_link_api' ) ) {

	function wpstack_sent_auto_link_api() {
		global $wpdb;
		$table_name   = $wpdb->prefix . 'wpstack_connect_auto_links';
		$link_data    = $wpdb->get_results( 
			$wpdb->prepare('SELECT * from %i WHERE `status` = 1', $table_name)
		);

		if ( $link_data ) {
			$site_info       = new WPStack_Connect_Site_Info();
			$site_url        = $site_info->siteurl();
			$post_parameters = array(
				'link_data' => $link_data,
				'site_url'  => $site_url,
			);
			$connection      = new WPStack_Connect_Connection();
			$request         = new WPStack_Connect_Request_Transfer();
			$request->post( $connection->appurl() . $connection->endpoint( 'auto-link' ), $post_parameters );
		}
	}
}

if ( ! function_exists( 'wpstack_cron_delete_links' ) ) {

	function wpstack_cron_delete_links( $schedules ) {
		$schedules['every_five_minutes'] = array(
			'interval' => 300,
			'display'  => __( 'Every 5 Minutes' ),
		);

		$schedules['every_hours'] = array(
			'interval' => 3600,
			'display'  => __( 'Every Hours' ),
		);
		return $schedules;
	}
}

add_filter( 'cron_schedules', 'wpstack_cron_delete_links' );

// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'wpstack_cron_delete_links' ) ) {
	wp_schedule_event( time(), 'every_five_minutes', 'wpstack_cron_delete_links' );
}

// Hook into that action that'll fire every five minutes
if ( ! function_exists( 'wpstack_delete_auto_links' ) ) {

	function wpstack_delete_auto_links() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpstack_connect_auto_links';
		$myrows     = $wpdb->get_results( 
			$wpdb->prepare('SELECT * FROM %i WHERE `status` = 0', $table_name)
		);

		if ( ! empty( $myrows ) && is_array( $myrows ) ) {
			foreach ( $myrows as $row ) {
				$link      = trim( $row->link );
				$keyword   = trim( $row->keyword );
				$alid      = $row->alid;
				$psid      = $row->psid;
				$decode_id = json_decode( $psid );

				if ( ! empty( $decode_id ) && is_array( $decode_id ) ) {
					foreach ( $decode_id as $key => $id ) {
						$post = get_post( $id );

						if ( isset( $post ) ) {
							$content = $post->post_content;
							$param   = array(
								'link'    => $link,
								'keyword' => $keyword,
							);
							$search  = wpstack_find_auto_links( $param, $content );

							if ( ! empty( $search ) && is_array( $search ) ) {
								foreach ( $search as $res ) {
									$content = preg_replace( '/' . preg_quote( $res['link'], '/' ) . '/', $res['keyword'], $content );
								}
							}

							$update = array(
								'ID'           => $id,
								'post_content' => $content,
							);
							wp_update_post( $update );
						}

						unset( $decode_id[ $key ] );
						$encode_id = json_encode( $decode_id );
						$wpdb->update( $table_name, array( 'psid' => $encode_id ), array( 'alid' => $alid ) );
					}
				} else {
					$wpdb->query( 
						$wpdb->prepare('DELETE FROM %i WHERE alid = %s', $table_name, $alid)
					);
				}
			}
		}
	}
}

if ( ! function_exists( 'wpstack_chunked_content' ) ) {

	function wpstack_chunked_content( $word, $content ) {
		$stripcont = strip_tags( $content );
		if ( $word < str_word_count( $stripcont ) ) {
			$exploded   = explode( ' ', $content );
			$chunked    = array_chunk( $exploded, $word );
			$subcontent = implode( ' ', $chunked[0] );
			$stripsub   = strip_tags( $subcontent );
			$n          = $word;
			while ( $word >= str_word_count( $stripsub ) ) {
				$n          = $n + 1;
				$chunked    = array_chunk( $exploded, $n );
				$subcontent = implode( ' ', $chunked[0] );
				$stripsub   = strip_tags( $subcontent );
			}
			if ( 1 < count( $chunked ) ) {
				$subcontent = implode( ' ', $chunked[0] );
				$newcontent = '';
				for ( $x = 1; $x < count( $chunked ); $x++ ) {
					$newcontent .= implode( ' ', $chunked[ $x ] ) . ' ';
				}
				$content = $newcontent;
				return array(
					'subcontent' => $subcontent,
					'content'    => $content,
				);
			}
			return false;
		}
		return false;

	}
}

add_filter( 'wp_insert_post_data', 'wpstack_add_links_to_content', 10, 2 );
add_action( 'wpstack_cron_added_links', 'wpstack_add_auto_links' );
add_action( 'wpstack_cron_delete_links', 'wpstack_delete_auto_links' );
