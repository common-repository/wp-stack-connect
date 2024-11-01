<?php

class WP_Config_Manager {	

	private $wp_config_src;
	private $wp_configs;

	public function wpconfig_file( $type = 'path' ) {

		if ( file_exists( ABSPATH . 'wp-config.php' ) ) {

			$file = ABSPATH . 'wp-config.php';
			$location = 'WordPress root directory';

		} elseif ( @file_exists( dirname( ABSPATH ) . '/wp-config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/wp-settings.php' ) ) {

			$file = dirname( ABSPATH ) . '/wp-config.php';
			$location = 'parent directory of WordPress root';

		} else {

			$file = 'Undetectable.';
			$location = 'not in WordPress root or it\'s parent directory';

		}

		if ( !is_writable( $file ) ) {

			$writeability = 'not writeable';

        } else {

        	$writeability = 'writeable';
            
        }

		if ( $type == 'path' ) {
	        return $file;
		} elseif ( $type == 'location' ) {
			return $location;
		} elseif ( $type == 'writeability' ) {
			return $writeability;
		} elseif ( $type == 'status' ) {
        	return '<div class="dlm-wpconfig-status" style="display: none;">The wp-config.php file is located in ' . $location . ' ('. $file . ') and is ' . $writeability .'.</div>';
		}

	}

	public function configs( $return_type = 'raw' ) {

		$src = file_get_contents( $this->wpconfig_file( 'path' ) );

		$configs             = array();
		$configs['constant'] = array();
		$configs['variable'] = array();		

		foreach ( token_get_all( $src ) as $token ) {
			if ( in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT ), true ) ) {
				$src = str_replace( $token[1], '', $src );
			}
		}

		preg_match_all( '/(?<=^|;|<\?php\s|<\?\s)(\h*define\s*\(\s*[\'"](\w*?)[\'"]\s*)(,\s*(\'\'|""|\'.*?[^\\\\]\'|".*?[^\\\\]"|.*?)\s*)((?:,\s*(?:true|false)\s*)?\)\s*;)/ims', $src, $constants );
		preg_match_all( '/(?<=^|;|<\?php\s|<\?\s)(\h*\$(\w+)\s*=)(\s*(\'\'|""|\'.*?[^\\\\]\'|".*?[^\\\\]"|.*?)\s*;)/ims', $src, $variables );

		if ( ! empty( $constants[0] ) && ! empty( $constants[1] ) && ! empty( $constants[2] ) && ! empty( $constants[3] ) && ! empty( $constants[4] ) && ! empty( $constants[5] ) ) {
			foreach ( $constants[2] as $index => $name ) {
				$configs['constant'][ $name ] = array(
					'src'   => $constants[0][ $index ],
					'value' => $constants[4][ $index ],
					'parts' => array(
						$constants[1][ $index ],
						$constants[3][ $index ],
						$constants[5][ $index ],
					),
				);
			}
		}

		if ( ! empty( $variables[0] ) && ! empty( $variables[1] ) && ! empty( $variables[2] ) && ! empty( $variables[3] ) && ! empty( $variables[4] ) ) {
			$variables[2] = array_reverse( array_unique( array_reverse( $variables[2], true ) ), true );
			foreach ( $variables[2] as $index => $name ) {
				$configs['variable'][ $name ] = array(
					'src'   => $variables[0][ $index ],
					'value' => $variables[4][ $index ],
					'parts' => array(
						$variables[1][ $index ],
						$variables[3][ $index ],
					),
				);
			}
		}

		$this->wp_configs = $configs;

		if ( $return_type == 'raw' ) {
			return $configs;
		} elseif ( $return_type == 'print_r' ) {
			return '<pre>' . print_r( $configs, true ) . '</pre>';
		}
	}

	public function exists( $type, $name ) {
		$wp_config_src = file_get_contents( $this->wpconfig_file( 'path' ) );

		if ( ! trim( $wp_config_src ) ) {
			throw new \Exception( 'Config file is empty.' );
		}
		
		$this->wp_config_src = str_replace( array( "\n\r", "\r" ), "\n", $wp_config_src );

		$this->wp_configs = $this->configs( 'raw' );

		if ( ! isset( $this->wp_configs[ $type ] ) ) {
			throw new \Exception( "Config type '{$type}' does not exist." );
		}

		return isset( $this->wp_configs[ $type ][ $name ] );
	}

	public function get_value( $type, $name ) {
		$wp_config_src = file_get_contents( $this->wpconfig_file( 'path' ) );

		if ( ! trim( $wp_config_src ) ) {
			throw new \Exception( 'Config file is empty.' );
		}

		$this->wp_config_src = $wp_config_src;
		$this->wp_configs    = $this->configs( 'raw' );

		if ( ! isset( $this->wp_configs[ $type ] ) ) {
			throw new \Exception( "Config type '{$type}' does not exist." );
		}

		return $this->wp_configs[ $type ][ $name ]['value'];
	}

	public function add( $type, $name, $value, array $options = array() ) {
		if ( ! is_string( $value ) ) {
			throw new \Exception( 'Config value must be a string.' );
		}

		if ( $this->exists( $type, $name ) ) {
			return false;
		}

		if ( in_array( $value, array( 'true', 'false' ), true ) ) {
			$raw_input = true;
		} else {
			$raw_input = false;			
		}

		$wp_config_src = file_get_contents( $this->wpconfig_file( 'path' ) );

		if ( false !== strpos( $wp_config_src, "Happy publishing" ) ) {
			$anchor = "/* That's all, stop editing! Happy publishing. */";
		} elseif ( false !== strpos( $wp_config_src, "Happy blogging" ) ) {
			$anchor = "/* That's all, stop editing! Happy blogging. */";
		} else {}

		$defaults = array(
			'raw'       => $raw_input,
			'anchor'    => $anchor,
			'separator' => PHP_EOL,
			'placement' => 'before',
		);

		list( $raw, $anchor, $separator, $placement ) = array_values( array_merge( $defaults, $options ) );;

		$raw       = (bool) $raw;
		$anchor    = (string) $anchor;
		$separator = (string) $separator;
		$placement = (string) $placement;

		if ( 'EOF' === $anchor ) {
			$contents = $this->wp_config_src . $this->normalize( $type, $name, $this->format_value( $value, $raw ) );
		} else {
			if ( false === strpos( $this->wp_config_src, $anchor ) ) {
				throw new \Exception( 'Unable to locate placement anchor.' );
			}

			$new_src  = $this->normalize( $type, $name, $this->format_value( $value, $raw ) );
			$new_src  = ( 'after' === $placement ) ? $anchor . $separator . $new_src : $new_src . $separator . $anchor;
			$contents = str_replace( $anchor, $new_src, $this->wp_config_src );
		}

		return $this->save( $contents );
	}

	public function update( $type, $name, $value, array $options = array() ) {
		if ( ! is_string( $value ) ) {
			throw new \Exception( 'Config value must be a string.' );
		}

		list( $add, $raw, $normalize ) = array_values( $options );

		$add       = (bool) $add;
		$raw       = (bool) $raw;
		$normalize = (bool) $normalize;

		if ( ! $this->exists( $type, $name ) ) {
			return ( $add ) ? $this->add( $type, $name, $value ) : false;
		}

		$old_src   = $this->wp_configs[ $type ][ $name ]['src'];
		$old_value = $this->wp_configs[ $type ][ $name ]['value'];
		$new_value = $this->format_value( $value, $raw );

		if ( $normalize ) {
			$new_src = $this->normalize( $type, $name, $new_value );
		} else {
			$new_parts    = $this->wp_configs[ $type ][ $name ]['parts'];
			$new_parts[1] = str_replace( $old_value, $new_value, $new_parts[1] );
			$new_src      = implode( '', $new_parts );
		}

		$contents = preg_replace(
			sprintf( '/(?<=^|;|<\?php\s|<\?\s)(\s*?)%s/m', preg_quote( trim( $old_src ), '/' ) ),
			'$1' . str_replace( '$', '\$', trim( $new_src ) ),
			$this->wp_config_src
		);

		return $this->save( $contents );
	}

	public function remove( $type, $name ) {
		if ( ! $this->exists( $type, $name ) ) {
			return false;
		}

		$wp_config_src = file_get_contents( $this->wpconfig_file( 'path' ) );
		$this->wp_config_src = str_replace( array( "\n\r", "\r" ), "\n", $wp_config_src );
		$this->wp_configs = $this->configs( 'raw' );

		$pattern  = sprintf( '/(?<=^|;|<\?php\s|<\?\s)%s\s*(\S|$)/m', preg_quote( $this->wp_configs[$type][$name]['src'], '/' ) );
		$contents = preg_replace( $pattern, '$1', $this->wp_config_src );

		return $this->save( $contents );
	}

	public function format_value( $value, $raw ) {
		if ( $raw && '' === trim( $value ) ) {
			throw new \Exception( 'Raw value for empty string not supported.' );
		}

		return ( $raw ) ? $value : var_export( $value, true );
	}

	public function normalize( $type, $name, $value ) {
		if ( 'constant' === $type ) {
			$placeholder = "define( '%s', %s );";
		} elseif ( 'variable' === $type ) {
			$placeholder = '$%s = %s;';
		} else {
			throw new \Exception( "Unable to normalize config type '{$type}'." );
		}

		return sprintf( $placeholder, $name, $value );
	}

	public function save( $contents ) {
		if ( ! trim( $contents ) ) {
			throw new \Exception( 'Cannot save the config file with empty contents.' );
		}

		if ( $contents === $this->wp_config_src ) {
			return false;
		}

		$result = file_put_contents( $this->wpconfig_file( 'path' ), $contents, LOCK_EX );

		if ( false === $result ) {
			throw new \Exception( 'Failed to update the config file.' );
		}

		return true;
	}

}