<?php

//if uninstall not called from WordPress exit
if(!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}
// check user capabilities
if ( ! current_user_can( 'delete_plugins' ) ) {
	return;
}

$delete = get_site_option('amdbible_delete_db',1);

if($delete){
	global $wpdb;
	$tables = array(
		$wpdb->base_prefix."amdbible_key_eng",
		$wpdb->base_prefix."amdbible_key_abbr_eng",
		$wpdb->base_prefix."amdbible_key_genre_eng",
		$wpdb->base_prefix."amdbible_cross_reference",
		$wpdb->base_prefix."amdbible_kjv",
		$wpdb->base_prefix."amdbible_plans",
		$wpdb->base_prefix."amdbible_plans_info",
		$wpdb->base_prefix."amdbible_devos"
	);
	$tables = implode(",",$tables);
	$wpdb->query("DROP TABLE IF EXISTS ".$tables);
	//delete multi-site network options
	delete_site_option('amdbible_db_version');
	//delete single site options
	delete_option('amdbible_reading_plan');
	delete_option('amdbible_plan_chosen');
	delete_option('amdbible_stand_cx_ref');
	delete_option('amdbible_ch_number_color');
	delete_option('amdbible_ver_number_color');
	delete_option('amdbible_full_reading_page');
	delete_site_option('amdbible_use_local_kjv');
	delete_site_option('amdbible_use_local_devos');
	delete_site_option('amdbible_delete_db');
}

?>