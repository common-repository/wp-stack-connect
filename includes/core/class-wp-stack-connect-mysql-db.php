<?php

class WPStack_Connect_Backup_Mysql_Db {

	protected static $record_per_session = 9000;

	protected static $content = '';

	protected static $total_record = 0;

	protected static $split_databases = array();

	protected static $handled;

	protected static $backup_dir;

	protected static $part = 0;

	protected static $mysql_type = array(
		'numerical' => array(
			'bit',
			'tinyint',
			'smallint',
			'mediumint',
			'int',
			'integer',
			'bigint',
			'real',
			'double',
			'float',
			'decimal',
			'numeric',
		),
		'blob'      => array(
			'tinyblob',
			'blob',
			'mediumblob',
			'longblob',
			'binary',
			'varbinary',
			'bit',
			'geometry',
			'point',
			'linestring',
			'polygon',
			'multipoint',
			'multilinestring',
			'multipolygon',
			'geometrycollection',
		),
	);

	protected static $colum_types = array();

	public static function dump_file( $split_databases, $backup_dir, $offset, $start, $part ) {
		// self::$handled = fopen($filename, 'a+');
		self::$backup_dir = $backup_dir;
		self::$part       = $part;

		$target_tables = array();

		self::$split_databases = $split_databases[ $offset ];

		foreach ( self::$split_databases as $key => $split_database ) {
			$split_database        = (array) $split_database;
			$target_tables[ $key ] = $split_database['table_name'];
		}

		try {
			self::export_table( $target_tables, $start, self::$record_per_session );
		} catch ( Exception $e ) {
			wpstack_log( $e->getMessage() );
			exit;
		}

		$new_start             = $start + self::$record_per_session;
		$data['export_finish'] = 0;
		$data['start_record']  = $new_start;
		$data['part']          = self::$part + 1;
		if ( ( self::$total_record < $new_start && count( self::$split_databases ) == 1 ) || ( count( self::$split_databases ) > 1 ) ) {
			$data['export_finish'] = 1;
			$data['start_record']  = 0;
			$data['part']          = 0;
		}
		fclose( self::$handled );
		return $data;
	}

	protected static function export_table( $tables, $start, $limit ) {
		global $wpdb;
		set_time_limit( 0 );

		$target_tables = $tables;

		foreach ( $target_tables as $key => $table ) {
			$filename           = self::$backup_dir . $table . '{part}.sql';
			$split_database     = (array) self::$split_databases[ $key ];
			self::$total_record = self::$total_record + $split_database['records'];
			if ( empty( $table ) ) {
				continue; }

			$TableMLine = (array) $wpdb->get_row( 
				$wpdb->prepare('SHOW CREATE TABLE %i', $table)
			);

			if ( $split_database['records'] > 0 ) {
				if ( $split_database['records'] > self::$record_per_session ) {
					$new_part     = str_pad( ( self::$part ), 2, '0', STR_PAD_LEFT );
					$new_filename = str_ireplace( '{part}', '-' . $new_part, $filename );
				} else {
					$new_filename = str_ireplace( '{part}', '', $filename );
				}

				self::$handled = fopen( $new_filename, 'a+' );

				if ( $start == 0 ) {
					self::set_header();
					$content  = "\r\n\r\n-- --------------------------------------------------------\r\n\r\n" . "--\r\n-- Table structure for table `" . $table . "`\r\n--\r\n\r\n";
					$content .= $TableMLine['Create Table'] . ";\n\n";
					$content  = str_ireplace(
						'CREATE TABLE `' . $table . '`',
						'DROP TABLE IF EXISTS `' . $table . "`;\r\nCREATE TABLE IF NOT EXISTS `" . $table . '`',
						$content
					);
					$content .= "--\r\n-- Dumping data for table `" . $table . "`\r\n--\r\n";
					self::write( $content );
				}

				$columns = array();
				$results = $wpdb->get_results( 
					$wpdb->prepare("SHOW COLUMNS FROM %i", $table)
				);

				foreach ( $results as $key => $col ) {
					$col = (array) $col;

					$types                                        = self::parse_column_type( $col );
					self::$colum_types[ $table ][ $col['Field'] ] = array(
						'is_numeric' => $types['is_numeric'],
						'is_blob'    => $types['is_blob'],
						'type'       => $types['type'],
						'type_sql'   => $col['Type'],
						'is_virtual' => $types['is_virtual'],
					);

					array_push( $columns, '`' . $col['Field'] . '`' );

				}

				$column = implode( ',', self::get_column_stmt( $table ) );
				$val_results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT $column FROM %i Limit %d, %d",
						$table,
						$start,
						$limit
					)
				);

				foreach ( $val_results as $row ) {
					$vals = self::escape( $table, $row );
					self::write(
						"REPLACE INTO `$table` (" .
						implode( ', ', $columns ) .
						') VALUES (' . implode( ',', $vals ) . ');' . PHP_EOL
					);
				}
			} else {
				$new_filename  = str_ireplace( '{part}', '', $filename );
				self::$handled = fopen( $new_filename, 'a+' );

				$content  = "\r\n\r\n-- --------------------------------------------------------\r\n\r\n" . "--\r\n-- Table structure for table `" . $table . "`\r\n--\r\n\r\n";
				$content .= $TableMLine['Create Table'] . ";\n\n";
				$content  = str_ireplace(
					'CREATE TABLE `' . $table . '`',
					'DROP TABLE IF EXISTS `' . $table . "`;\r\nCREATE TABLE IF NOT EXISTS `" . $table . '`',
					$content
				);
				self::write( $content );
			}
		}
		$wpdb->flush();
		return true;
	}

	/**
	 * Escape values with quotes when needed
	 *
	 * @param string $table_name Name of table which contains rows
	 * @param array  $row Associative array of column names and values to be quoted
	 *
	 * @return string
	 */
	private static function escape( $table, $row ) {
		$ret = array();
		foreach ( $row as $col_name => $col_value ) {
			if ( is_null( $col_value ) ) {
				$ret[] = 'NULL';
			} elseif ( self::$colum_types[ $table ][ $col_name ]['is_blob'] ) {
				if ( self::$colum_types[ $table ][ $col_name ]['type'] == 'bit' || ! empty( $col_value ) ) {
					$ret[] = "0x${col_value}";
				} else {
					$ret[] = "''";
				}
			} elseif ( self::$colum_types[ $table ][ $col_name ]['is_numeric'] ) {
				$ret[] = $col_value;
			} else {
				$ret[] = self::quote( $col_value );
			}
		}
		return $ret;
	}

	public static function quote( $value ) {
		global $wpdb;
		$value = "'" . $wpdb->_real_escape( $value ) . "'";
		return $wpdb->remove_placeholder_escape( $value );
	}

	protected static function write( $content ) {
		fwrite( self::$handled, $content );
		return true;
	}

	protected static function set_header() {
		global $wpdb, $wp_version;

		$db_name = DB_NAME;

		$content = "-- WordPress MySQL database backup \r\n-- " .
				"\r\n-- Created By WP-Stack-connect \r\n--" .
				"\r\n-- WordPress version: " . $wp_version . ' running on PHP Version ' . phpversion() . ', ' . $wpdb->db_server_info() .
				"\r\n-- Generation Time: " . date( 'M d, Y \a\t h:i A', strtotime( date( 'Y-m-d H:i:s', time() ) ) ) . ' ';

		// $content .= $db_name . "`\r\n--\r\nCREATE DATABASE IF NOT EXISTS `" .
		// 		$db_name . "` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;\r\nUSE `" .
		// 		$db_name . '`;';

		// $content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
		self::write( $content ); 
	}

	/**
	 * Decode column metadata and fill info structure.
	 * type, is_numeric and is_blob will always be available.
	 *
	 * @param array $col_type Array returned from "SHOW COLUMNS FROM table_name"
	 * @return array
	 */
	protected static function parse_column_type( $col_type ) {
		$col_info = array();
		$col_part = explode( ' ', $col_type['Type'] );

		if ( $fparen = strpos( $col_part[0], '(' ) ) {
			$col_info['type']       = substr( $col_part[0], 0, $fparen );
			$col_info['length']     = str_replace( ')', '', substr( $col_part[0], $fparen + 1 ) );
			$col_info['attributes'] = isset( $col_part[1] ) ? $col_part[1] : null;
		} else {
			$col_info['type'] = $col_part[0];
		}
		$col_info['is_numeric'] = in_array( $col_info['type'], self::$mysql_type['numerical'] );
		$col_info['is_blob']    = in_array( $col_info['type'], self::$mysql_type['blob'] );
		$col_info['is_virtual'] = strpos( $col_type['Extra'], 'STORED GENERATED' ) === false ? false : true;

		return $col_info;
	}

	/**
	 * Build SQL List of all columns on current table
	 *
	 * @param string $table_name  Name of table to get columns
	 *
	 * @return string SQL sentence with columns
	 */
	protected static function get_column_stmt( $table ) {
		$col_stmt = array();
		foreach ( self::$colum_types[ $table ] as $col_name => $col_type ) {
			if ( $col_type['type'] == 'bit' ) {
				$col_stmt[] = "LPAD(HEX(`${col_name}`),2,'0') AS `${col_name}`";
			} elseif ( $col_type['is_blob'] ) {
				$col_stmt[] = "HEX(`${col_name}`) AS `${col_name}`";
			} elseif ( $col_type['is_virtual'] ) {
				continue;
			} else {
				$col_stmt[] = "`${col_name}`";
			}
		}

		return $col_stmt;
	}
}
