<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( wp_next_scheduled( 'wpstack_cron_override_htaccess_bps' ) ) {
	wp_clear_scheduled_hook( 'wpstack_cron_override_htaccess_bps' );
}