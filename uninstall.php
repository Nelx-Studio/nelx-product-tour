<?php
/**
 * Uninstall cleanup.
 *
 * @package NelxProductTour
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$wpdb->delete(
	$wpdb->usermeta,
	array(
		'meta_key' => 'nelxstd_prt_completed_tours',
	),
	array( '%s' )
);
