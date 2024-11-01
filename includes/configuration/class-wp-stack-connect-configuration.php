<?php

class WPStack_Connect_Configuration {

	public $settings;

	function __construct() {
		global $pagenow;
		$this->settings = new WPStack_Connect_Wp_Settings();
		if ( $pagenow === 'plugins.php' || $pagenow === 'admin-ajax.php' ) {
			$this->register_hook_configuration();
		}
	}

	protected function register_hook_configuration() {
		add_action( 'admin_init', array( $this, 'wpstack_connect_enqueue_script' ) );
		add_action( 'admin_footer', array( $this, 'modal_element_configuration' ) );
		add_action( 'admin_footer', array( $this, 'toast_element_notification' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_element_configuration' ), 10, 2 );
		add_action( 'wp_ajax_disconnected', array( $this, 'disconnected' ) );
		add_action( 'wp_ajax_auto_connect', array( $this, 'auto_connect' ) );
		add_action( 'wp_ajax_update_message_status', array( $this, 'update_message_status' ) );
		add_action( 'admin_init', array( $this, 'wpstack_connect_activate_plugin_message' ) );
	}

	public function wpstack_connect_enqueue_script() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		$wpstack_js_path = plugin_dir_url( __FILE__ ) . 'assets/js/wp-stack-connect-configuration.min.js?time=' . date( 'His' );
		wp_register_script( 'wpstack_connect_configuration', $wpstack_js_path );
		wp_localize_script( 'wpstack_connect_configuration', 'wpstack_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'wpstack_connect_configuration' );
		$this->wp_enqueue_stylesheet();
	}

	public function disconnected() {
		update_option( 'wpstack_connect_client_id', '' );
		update_option( 'wpstack_connect_website_id', '' );
		update_option( 'wpstack_connect_site_url', '' );
		update_option( 'wpstack_connect_return_url', '' );
		update_option( 'wpstack_connect_connected_status', 'disconnect' );
		WPStack_Connect_Account::remove( $this->settings );
		$connection = new WPStack_Connect_Connection();
		$connection->deactivate();
		$result = $this->get_public_key();

		echo wp_json_encode( $result );
		wp_die();
	}

	public function auto_connect() {
		$connection = new WPStack_Connect_Connection();
		$auto_connection = $connection->auto_connection();
		wp_die( $auto_connection );
	}

	public function wp_enqueue_stylesheet() {
		$jquery_modal = plugin_dir_url( __FILE__ ) . 'assets/css/wp-stack-connect-configuration.min.css';
		wp_enqueue_style( 'wpstack-configuration', $jquery_modal );
		$fontawesome = plugin_dir_url( __FILE__ ) . 'assets/font-awesome-4.7.0/css/font-awesome.min.css';
		wp_enqueue_style( 'font-awesome', $fontawesome );
	}

	public function modal_element_configuration() {
		$search        	= array( '{{secret_key}}', '{{public_key}}', '{{button_connect}}' );
		$public_key     = $this->get_public_key();
		$button_connect = $this->connected_status();
		$replace        = array( '********************', $public_key['public_key'], $button_connect );
		$template_path  = dirname( __FILE__ ) . '/template/view-configuration.php';
		$html           = file_get_contents( $template_path );
		$html           = str_ireplace( $search, $replace, $html );
		echo $html;
	}

	public function toast_element_notification()
	{
		?>
			<div aria-live="polite" aria-atomic="true" style="position: fixed;z-index: 500000;top: 5px;right: 5px;width: 100%;">
				<div style="position: absolute; top: 0; right: 0;">
					<div id="wpstack-connect-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
						<div class="toast-header">
							<small class="text-muted" id="wpstack-connect-toast-message">WPStack Connect Notification</small>
							<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	protected function get_public_key() {
		$WPStack_Connect_Account = new WPStack_Connect_Account( $this->settings, '', '', '' );
		$account          = $WPStack_Connect_Account->get_account( $this->settings );
		if ( sizeof( $account ) > 0 ) {
			return array( 'public_key' => array_keys( $account )[0] );
		}

		$public = $WPStack_Connect_Account::rand_string( 32 );
		$secret = $WPStack_Connect_Account::rand_string( 32 );
		$WPStack_Connect_Account->add_account( $this->settings, $public, $secret );

		return array( 'public_key' => $public );
	}

	protected function connected_status() {
		if ( get_option( 'wpstack_connect_connected_status' ) == 'connected' ) {
			return '<button type="button" data-connected="disconnected" class="btn btn-danger"><i class="fa fa-unlink"></i> Disconnect</button>';
		} else {
			return '<button type="button" class="btn btn-primary disabled"><i class="fa fa-link"></i> Connect</button>';
		}
	}

	public function add_element_configuration( $plugin_meta, $plugin_file ) {
		if ( strpos( $plugin_file, 'wp-stack-connect/init.php' ) !== false ) {
			$plugin_meta[] = '<a href="javascript:void(0)" id="display-config">View connection key</a>';
			$plugin_meta[] = '<a href="javascript:void(0)" class="auto-connect">Auto connect</a>';
		}

		return $plugin_meta;
	}

	public function update_message_status() {
		update_option( 'wpstack_connect_message_status', 'public' );
		$result = [ 'reload' => true ];
		echo json_encode( $result );
		wp_die();
	}

	public function wpstack_connect_activate_plugin_message() {
		$active_plugin = get_option( 'active_plugins' );
		if ( in_array( 'wp-stack-connect/init.php', $active_plugin ) ) {
			add_action( 'admin_notices', array( $this, 'wpstack_connect_message' ) );
		}
	}

	public function wpstack_connect_message() {
		$connection_status = get_option( 'wpstack_connect_connected_status' );
		if ( $connection_status !== 'connected') {
			$message_status = get_option( 'wpstack_connect_message_status' );
			if ( $message_status == 'auto-connect' ) {
				?>
					<div class="notice notice-warning is-dismissible">
						<p>WP-Stack plugin is activated</p>
						<p>
							<button type="button" class="wp-core-ui button auto-connect">Connect</button>
						</p>
					</div>
				<?php
			} else {
				$public_key_data = $this->get_public_key();
				$public_key		 = isset( $public_key_data['public_key'] ) ? $public_key_data['public_key'] : '';
				?>
					<div class="notice notice-warning is-dismissible">
						<p>WP-Stack plugin is activated</p>				
						<div class="form-group">
							<label for="notice-public-key">We could not connect to your website, please copy and paste the below key on WP-Stack dashboard</label>
							<div class="input-group">
								<input type="text" class="form-control public-key" id="notice-public-key" name="public_key" placeholder="xxxxxxxxxxxxxxx" value="<?php echo esc_attr( $public_key ); ?>" readonly>
								<div class="input-group-append">
									<button class="btn btn-primary btn-cp-pk" type="button">
										Copy
									</button>
								</div>
							</div>
						</div>
					</div>
				<?php
			}
		}
	}

}
