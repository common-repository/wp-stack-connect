<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPStack_Connect_Manage_Plugins extends WPStack_Connect_Manage_Abstract {


	public $end_point = 'plugins';

	public $result;

	// Always put title first
	protected $filter_seo = array(
		'wordpress-seo/wp-seo.php'                    => array(
			'name'     => 'Yoast SEO',
			'prefix'   => '_yoast_wpseo_',
			'meta_key' => array(
				'title',
				'metadesc',
			),
		),
		'seo-by-rank-math/rank-math.php'              => array(
			'name'     => 'Rank Math SEO',
			'prefix'   => 'rank_math_',
			'meta_key' => array(
				'title',
				'description',
			),
		),
		'all-in-one-seo-pack/all_in_one_seo_pack.php' => array(
			'name'     => 'All in One SEO',
			'prefix'   => '_aioseo_',
			'meta_key' => array(
				'title',
				'description',
			),
		),
		'wp-seopress/seopress.php'                    => array(
			'name'     => 'SEOPress',
			'prefix'   => '_seopress_',
			'meta_key' => array(
				'titles_title',
				'titles_desc',
			),
		),
		'wp-meta-seo/wp-meta-seo.php'                 => array(
			'name'     => 'WP Meta SEO',
			'prefix'   => '_metaseo_',
			'meta_key' => array(
				'metatitle',
				'metadesc',
			),
		),
	);

	public function get_plugins( $token ) {
		$this->result[ $this->prefix_sync . $this->end_point ] = WPStack_Connect_Core::get_request_api( $this->end_point, $token );
		return $this->result;
	}

	public function validate_seo_plugin() {
		$plugins_active = get_option( 'active_plugins' );
		$return         = array();
		foreach ( $plugins_active as $plugin_active ) {
			if ( array_key_exists( $plugin_active, $this->filter_seo ) ) {
				$return[ $plugin_active ] = $this->filter_seo[ $plugin_active ];
				break;
			}
		}

		return $return;
	}
}
