<?php

class WPStack_Connect_Smart_Update extends WPStack_Connect_Callback_Base {


	protected $get_updates;

	protected $core_info;

	protected $plugin_info;

	protected $theme_info;

	public function include_files() {
		@require_once ABSPATH . 'wp-includes/pluggable.php';
		@require_once ABSPATH . 'wp-admin/includes/file.php';
		@require_once ABSPATH . 'wp-admin/includes/plugin.php';
		@require_once ABSPATH . 'wp-admin/includes/theme.php';
		@require_once ABSPATH . 'wp-admin/includes/misc.php';
		@require_once ABSPATH . 'wp-admin/includes/template.php';
		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	}

	public function wpstack_maintenance_mode( $enable = false, $maintenance_message = '' ) {
		global $wp_filesystem;

		$maintenance_message .= '<?php $upgrading = ' . time() . '; ?>';

		$file = $wp_filesystem->abspath() . '.maintenance';
		if ( $enable ) {
			$wp_filesystem->delete( $file );
			$wp_filesystem->put_contents( $file, $maintenance_message, FS_CHMOD_FILE );
		} else {
			$wp_filesystem->delete( $file );
		}
	}

	public function do_upgrade( $params = null ) {
		if ( $params == null || empty( $params ) ) {
			return array(
				'error' => 'No upgrades passed.',
			);
		}

		$params = isset( $params['upgrades_all'] ) ? $params['upgrades_all'] : $params;

		$core_upgrade    = isset( $params['core_upgrade'] ) ? $params['core_upgrade'] : array();
		$upgrade_plugins = isset( $params['upgrade_plugins'] ) ? $params['upgrade_plugins'] : array();
		$upgrade_themes  = isset( $params['upgrade_themes'] ) ? $params['upgrade_themes'] : array();

		$upgrades = array();
		if ( ! empty( $core_upgrade ) ) {
			$upgrades['core'] = $this->upgrade_core( $core_upgrade );
		}

		if ( ! empty( $upgrade_plugins ) ) {
			$plugin_files = array();
			foreach ( $upgrade_plugins as $plugin ) {
				if ( isset( $plugin['file'] ) ) {
					$plugin_files[ $plugin['file'] ] = $plugin['old_version'];
				}
			}

			if ( ! empty( $plugin_files ) ) {
				$upgrades['plugins'] = $this->upgrade_plugins( $plugin_files );
			}
		}

		if ( ! empty( $upgrade_themes ) ) {
			$theme_temps = array();
			foreach ( $upgrade_themes as $theme ) {
				if ( isset( $theme['theme_tmp'] ) ) {
					$theme_temps[] = $theme['theme_tmp'];
				}
			}

			if ( ! empty( $theme_temps ) ) {
				$upgrades['themes'] = $this->upgrade_themes( $theme_temps );
			}
		}

		$this->wpstack_maintenance_mode( false );

		return $upgrades;
	}

	protected function get_collect_updates() {
		$get_update_plugins = $this->get_plugin_updates();
		$get_update_themes  = $this->get_theme_updates();
		$get_update_core    = $this->get_core_updates();

		$update_data = array(
			'data_plugins' => $get_update_plugins,
			'data_themes'  => $get_update_themes,
			'data_core'    => $get_update_core,
		);

		$this->get_updates = $update_data;
	}

	protected function get_theme_updates() {
		$theme_updates = array();
		$theme_data    = $this->theme_info;
		$themes        = wp_get_themes();

		if ( isset( $theme_data->response )
			&& is_array( $theme_data->response )
			&& sizeof( $theme_data->response ) > 0
		) {
			$list_theme_old_versions = $theme_data->checked;
			foreach ( $theme_data->response as $update ) {
				$update                                    = (array) $update;
				$theme_updates[ $update['theme'] ]         = $update;
				$theme_updates[ $update['theme'] ]['name'] = $themes[ $update['theme'] ]['Name'];
				$theme_updates[ $update['theme'] ]['old_version'] = $list_theme_old_versions[ $update['theme'] ];
			}
		}

		return $theme_updates;
	}

	protected function get_plugin_updates() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$plugin_updates = array();
		$plugin_data    = $this->plugin_info;
		$plugins        = get_plugins();

		if ( isset( $plugin_data->response )
			&& is_array( $plugin_data->response )
			&& sizeof( $plugin_data->response ) > 0
		) {
			foreach ( $plugin_data->response as $update ) {
				$update                                      = (array) $update;
				$plugin_updates[ $update['plugin'] ]         = $update;
				$plugin_updates[ $update['plugin'] ]['name'] = $plugins[ $update['plugin'] ]['Name'];
				$plugin_updates[ $update['plugin'] ]['old_version'] = $plugins[ $update['plugin'] ]['Version'];
			}
		}

		return $plugin_updates;
	}

	protected function get_core_updates() {
		require ABSPATH . WPINC . '/version.php';
		$core_updates = array();
		$core         = $this->core_info;
		$old_versions = $wp_version;
		foreach ( $core->updates as $update ) {
			$update = (array) $update;
			if ( $update['response'] == 'upgrade' ) {
				$core_updates['core']                    = $update;
				$core_updates['core']['current_version'] = $old_versions;
				break;
			}
		}

		return $core_updates;
	}

	public function get_update_data() {
		return $this->get_updates;
	}

	protected function reload() {
		global $wp_current_filter;
		$wp_current_filter[] = 'load-update-core.php';

		$filterFunction = function( $a ) {
			if ( null == $a ) {
				return false; }
			if ( is_object( $a ) && property_exists( $a, 'last_checked' ) && ! property_exists( $a, 'checked' ) ) {
				return false;
			}
			return $a;
		};

		add_filter( 'pre_site_transient_update_core', $filterFunction, 99);
        add_filter( 'pre_site_transient_update_plugins', $filterFunction, 99);
        add_filter( 'pre_site_transient_update_themes', $filterFunction, 99);

		if ( function_exists( 'wp_clean_update_cache' ) ) {
			wp_clean_update_cache();
		}
		wp_update_plugins();
		wp_update_themes();

		array_pop( $wp_current_filter );
		wp_version_check();
		wp_version_check( array(), true );

		$this->core_info   = get_site_transient( 'update_core' );
		$this->plugin_info = get_site_transient( 'update_plugins' );
		$this->theme_info  = get_site_transient( 'update_themes' );
	}

	public function upgrade_plugins( $plugins = false ) {
		if ( ! function_exists( 'wp_update_plugins' ) ) {
			include_once ABSPATH . 'wp-includes/update.php';
		}

		if ( ! class_exists( 'Plugin_Upgrader_Skin' ) ) {
			include_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader-skin.php';
		}

		if ( class_exists( 'Plugin_Upgrader' ) ) {
			$upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin() );
			$result   = $upgrader->bulk_upgrade( array_keys( $plugins ) );
			if ( ! empty( $result ) ) {
				foreach ( $result as $plugin_slug => $plugin_info ) {
					if ( ! $plugin_info || is_wp_error( $plugin_info ) ) {
						$return[ $plugin_slug ] = $plugin_info;
						continue;
					}

					$return[ $plugin_slug ] = 1;
				}

				return array(
					'upgraded' => array(
						'code'   => 200,
						'status' => 'Plugin successful updated',
					),
				);
			} else {
				return array(
					'error' => 'Upgrade failed.',
				);
			}
		} else {
			return array(
				'error' => 'WordPress update required first.',
			);
		}
	}

	public function upgrade_themes( $themes = false ) {

		if ( ! function_exists( 'wp_update_themes' ) ) {
			include_once ABSPATH . 'wp-includes/update.php';
		}

		if ( ! class_exists( 'Theme_Upgrader_Skin' ) ) {
			include_once ABSPATH . 'wp-admin/includes/class-theme-upgrader-skin.php';
		}

		if ( ! $themes || empty( $themes ) ) {
			return array(
				'error' => 'No theme files for upgrade.',
			);
		}

		if ( class_exists( 'Theme_Upgrader' ) ) {
			$upgrader = new Theme_Upgrader( new Theme_Upgrader_Skin() );
			$result   = $upgrader->bulk_upgrade( $themes );

			$return = array();
			if ( ! empty( $result ) ) {
				foreach ( $result as $theme_tmp => $theme_info ) {
					if ( is_wp_error( $theme_info ) || empty( $theme_info ) ) {
						$return[ $theme_tmp ] = $this->mmb_get_error( $theme_info );
						continue;
					}

					$return[ $theme_tmp ] = 1;
				}

				return array(
					'upgraded' => array(
						'code'   => 200,
						'status' => 'Theme successful updated',
					),
				);
			} else {
				return array(
					'error' => 'Upgrade failed.',
				);
			}
		} else {
			return array(
				'error' => 'WordPress update required first',
			);
		}
	}

	public function upgrade_core( $current ) {

		if ( file_exists( ABSPATH . '/wp-admin/includes/update.php' ) ) {
			include_once ABSPATH . '/wp-admin/includes/update.php';
		}

		$current		= isset( $current['core'] ) ? $current['core'] : $current;
		$current_update = false;
		$core           = get_site_transient( 'update_core' );

		if ( ! class_exists( 'WP_Upgrader_Skin' ) ) {
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
		}
		$coreUpgraderSkin = new WP_Upgrader_Skin();

		if ( isset( $core->updates ) && ! empty( $core->updates ) ) {
			$updates = $core->updates;
			$updated = $core->updates[0];
			if ( ! isset( $updated->response ) || $updated->response == 'latest' ) {
				return array(
					'upgraded' => ' updated',
				);
			}

			if ( $updated->response == 'development' && $current['response'] == 'upgrade' ) {
				return array(
					'error' => '<font color="#900">Unexpected error. Please upgrade manually.</font>',
				);
			} else {
				if ( $updated->response == $current['response'] || ( $updated->response == 'upgrade' && $current['response'] == 'development' ) ) {
					if ( $updated->locale != $current['locale'] ) {
						foreach ( $updates as $update ) {
							if ( $update->locale == $current['locale'] ) {
								$current_update = $update;
								break;
							}
						}
						if ( $current_update == false ) {
							return array(
								'error' => ' Localization mismatch. Try again.',
							);
						}
					} else {
						$current_update = $updated;
					}
				} else {
					return array(
						'error' => ' Transient mismatch. Try again.',
					);
				}
			}
		} else {
			return array(
				'error' => ' Refresh transient failed. Try again.',
			);
		}
		if ( $current_update != false ) {
			global $wp_filesystem, $wp_version;

			if ( version_compare( $wp_version, '3.1.9', '>' ) ) {

				$core   = new Core_Upgrader( $coreUpgraderSkin );
				$result = $core->upgrade( $current_update );

				$this->wpstack_maintenance_mode( false );

				if ( is_wp_error( $result ) ) {
					return array(
						'error' => $result,
					);
				} else {
					return array(
						'upgraded' => array(
							'code'   => 200,
							'status' => 'Core successful updated',
						),
					);
				}
			}
		} else {
			return array(
				'error' => 'failed',
			);
		}
	}

	public function process( $request ) {
		$this->reload();
		$this->include_files();
		$this->get_collect_updates();

		$resp = array();

		switch ( $request->method ) {
			case 'get_lists':
				$resp = $this->get_update_data();
				break;

			case 'do_upgrade':
				$resp = $this->do_upgrade( $request->params );
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
