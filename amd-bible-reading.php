<?php
/*
Plugin Name: AMD Bible Reading
Plugin URI:  http://amasterdesigns.com/wordpress-daily-bible-reading-plugin/
Description: Easily turn any page into a daily bible reading plan with the shortcode [amd_bible_daily], add the daily devotional from Spurgeon's Morning by Morning and Evening by Evening to any page or post with the shortcode [amd_bible_devo], display a passage from the KJV anywhere using the shortcode [amd_bible]ComplexReferenceHere[/amd_bible], display a random verse on every page load using the shortcode [amd_bible_rand], and add a widget with a snippet of the daily passage
Version:     3.1.5
Author:      A Master Designs
Author URI:  http://www.amasterdesigns.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network: True
*/

global $amdbible_db_version;
global $wpdb;
$amdbible_db_version = '2.1';

/**
 * Install plugin and create tables.
 * Also call this function for updating plugin
 */
function amdbible_install(){
	// check user capabilities
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	
	global $wpdb;
	global $amdbible_db_version;
	
	$amdbible_current_db_version = get_site_option('amdbible_db_version',0);
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	$charset_collate = $wpdb->get_charset_collate();
	
	if($amdbible_current_db_version=='2.0'){
		//The following update will delete any changes made to plans and will have to be recreated and/or edited.
		$table_name = $wpdb->base_prefix . "amdbible_plans_info";
		$sql = "DROP TABLE IF EXISTS $table_name;";
		$wpdb->query($sql);
		$sql = "CREATE TABLE $table_name (
			id int(8) unsigned AUTO_INCREMENT NOT NULL COMMENT 'Bible Reading Plan',
			cx tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Plan uses Complex Passages',
			n varchar(50) NOT NULL COMMENT 'Name of Reading Plan',
			d text NOT NULL COMMENT 'Description of Reading Plan',
			PRIMARY KEY  (id)
		) $charset_collate;";
		$wpdb->query($sql);
		
		$table_name = $wpdb->base_prefix . "amdbible_plans";
		$sql = "DROP TABLE IF EXISTS $table_name;";
		$wpdb->query($sql);
		$sql = "CREATE TABLE $table_name (
			id int(8) unsigned AUTO_INCREMENT NOT NULL,
			p tinyint(1) unsigned NOT NULL COMMENT 'Bible Reading Plan',
			d smallint(2) unsigned NOT NULL COMMENT 'Day of the year 1 to 366',
			sv int(8) unsigned zerofill NOT NULL COMMENT 'Start Verse',
			ev int(8) unsigned zerofill NOT NULL COMMENT 'End Verse',
			cx text COMMENT 'Complex Passage Array Serialized',
			PRIMARY KEY  (id),
			UNIQUE KEY p_and_d (p,d)
		) $charset_collate;";
		$wpdb->query($sql);
		
		define('AMDBIBLE_INSTALL', TRUE);
		require_once(plugin_dir_path( __FILE__ )."insert-plans.php");
	
	} else if($amdbible_current_db_version < $amdbible_db_version){
		
	
		$table_name = $wpdb->base_prefix . "amdbible_key_eng";
		
		if($wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name){
			$sql = "CREATE TABLE $table_name (
				b int(11) NOT NULL COMMENT 'Book Num',
				n text NOT NULL COMMENT 'Name',
				t varchar(2) NOT NULL COMMENT 'Which Testament this book is in',
				g tinyint(3) unsigned NOT NULL COMMENT 'A genre ID to identify the type of book this is',
				PRIMARY KEY  (b)
			) $charset_collate;";
			dbDelta( $sql );
		}
		
		
		$table_name = $wpdb->base_prefix . "amdbible_key_abbr_eng";
		if($wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name){
			$sql = "CREATE TABLE $table_name (
				id smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Abbreviation ID',
				a varchar(255) NOT NULL,
				b smallint(5) unsigned NOT NULL COMMENT 'ID of book that is abbreviated',
				p tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether an abbreviation is the primary one for the book',
				PRIMARY KEY  (id)
			) $charset_collate;";
			dbDelta( $sql );
		}
		
		$table_name = $wpdb->base_prefix . "amdbible_key_genre_eng";
		if($wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name){
			$sql = "CREATE TABLE $table_name (
				g tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Genre ID',
				n varchar(255) NOT NULL COMMENT 'Name of genre',
				PRIMARY KEY  (g)
			) $charset_collate;";
			dbDelta( $sql );
		}
		
		$table_name = $wpdb->base_prefix . "amdbible_kjv";
		if($wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name){
			$sql = "CREATE TABLE $table_name (
				id int(8) unsigned zerofill NOT NULL,
				b int(11) NOT NULL COMMENT 'Book Num',
				c int(11) NOT NULL COMMENT 'Chapter Num',
				v int(11) NOT NULL COMMENT 'Verse Num',
				t text NOT NULL COMMENT 'Text',
				PRIMARY KEY  (id)
			) $charset_collate;";
			dbDelta( $sql );
		}
		
		$table_name = $wpdb->base_prefix . "amdbible_plans_info";
		if($wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name){
			$sql = "CREATE TABLE $table_name (
				id int(8) unsigned AUTO_INCREMENT NOT NULL COMMENT 'Bible Reading Plan',
				cx tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Plan uses Complex Passages',
				n varchar(50) NOT NULL COMMENT 'Name of Reading Plan',
				d text NOT NULL COMMENT 'Description of Reading Plan',
				PRIMARY KEY  (id)
			) $charset_collate;";
			dbDelta( $sql );
		}
		
		$table_name = $wpdb->base_prefix . "amdbible_plans";
		if($wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name){
			$sql = "CREATE TABLE $table_name (
				id int(8) unsigned AUTO_INCREMENT NOT NULL,
				p tinyint(1) unsigned NOT NULL COMMENT 'Bible Reading Plan',
				d smallint(2) unsigned NOT NULL COMMENT 'Day of the year 1 to 366',
				sv int(8) unsigned zerofill NOT NULL COMMENT 'Start Verse',
				ev int(8) unsigned zerofill NOT NULL COMMENT 'End Verse',
				cx text COMMENT 'Complex Passage Array Serialized',
				PRIMARY KEY  (id),
				UNIQUE KEY p_and_d (p,d)
			) $charset_collate;";
			dbDelta( $sql );
		}
		
		$table_name = $wpdb->base_prefix . "amdbible_devos";
		if($wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name){
			$sql = "CREATE TABLE $table_name (
				id int(8) unsigned AUTO_INCREMENT NOT NULL,
				d varchar(5) NOT NULL COMMENT 'Date of Devotion',
				ap varchar(2) NOT NULL COMMENT 'Morning or Evening',
				v varchar(50) NOT NULL COMMENT 'Verse for Devotion',
				t varchar(255) NOT NULL COMMENT 'Title of Devotion',
				c text NOT NULL COMMENT 'Devotion Content',
				PRIMARY KEY  (id)
			) $charset_collate;";
			dbDelta( $sql );
		}
		
		define('AMDBIBLE_INSTALL', TRUE);
		require_once(plugin_dir_path( __FILE__ )."insert-keys.php");
		if(file_exists(plugin_dir_path( __FILE__ )."insert-kjv.php")){
			require_once(plugin_dir_path( __FILE__ )."insert-kjv.php");
		} else {
			require_once(plugin_dir_path( __FILE__ )."insert-kjv-empty.php");
		}
		require_once(plugin_dir_path( __FILE__ )."insert-plans.php");
		if(file_exists(plugin_dir_path( __FILE__ )."insert-devos.php")){
			require_once(plugin_dir_path( __FILE__ )."insert-devos.php");
		} else {
			require_once(plugin_dir_path( __FILE__ )."insert-devos-empty.php");
		}
		
	}
	
	update_site_option( 'amdbible_db_version', $amdbible_db_version );
	
}

//create tables upon plugin activation
register_activation_hook( __FILE__, 'amdbible_install' );

/**
 * Deactivate plugin.
 */
function amdbible_deactivate() {
	// check user capabilities
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	/* 
	* These removal items were moved to uninstall instead of deactivate to keep user settings and changes to database
	*/
}
//delete custom tables upon plugin deactivation
register_deactivation_hook( __FILE__, 'amdbible_deactivate' );


if(is_multisite()){
	add_action('network_admin_menu','amdbible_mu_admin_menu');
}

function amdbible_mu_admin_menu(){
	add_menu_page("AMD Network Settings","AMD MU Settings",'manage_network_options','amd-bible-reading-mu','amdbible_mu_admin_page','dashicons-book-alt',79);
}
//
//
//	DEBUG! - SETTINGS ARE NOT SAVING/SHOWING CORRECTLY
//
//
function amdbible_mu_admin_page(){
	if(!current_user_can('manage_network_options')){
		return;
		//wp_die('Error 401: Access Denied. You are not a network administrator');
	}
	
	if(isset($_POST['my_submit'])){
		$amd_sanitized_options = array();
		if(!isset($_POST['amd_option']['amdbible_use_local_kjv'])){
			//if not checked set to false ( 0 )
			$amd_sanitized_options['amdbible_use_local_kjv'] = 0;
		} else {
			$amd_sanitized_options['amdbible_use_local_kjv'] = amdbible_validate_bool($_POST['amd_option']['amdbible_use_local_kjv']);
		}
		if(!isset($_POST['amd_option']['amdbible_use_local_devos'])){
			//if not checked set to false ( 0 )
			$amd_sanitized_options['amdbible_use_local_devos'] = 0;
		} else {
			$amd_sanitized_options['amdbible_use_local_devos'] = amdbible_validate_bool($_POST['amd_option']['amdbible_use_local_devos']);
		}
		if(!isset($_POST['amd_option']['amdbible_delete_db'])){
			//if not checked set to false ( 0 )
			$amd_sanitized_options['amdbible_delete_db'] = 0;
		} else {
			$amd_sanitized_options['amdbible_delete_db'] = amdbible_validate_bool($_POST['amd_option']['amdbible_delete_db']);
		}
		foreach((array)$amd_sanitized_options as $key => $value){
			update_site_option($key,$value);
		}
	}
	$amdbible_use_local_kjv = get_site_option('amdbible_use_local_kjv',0);
	$amdbible_use_local_devos = get_site_option('amdbible_use_local_devos',0);
	$amdbible_delete_db = get_site_option('amdbible_delete_db',0);
	?>
	<div class="wrap">
		<h2>My Settings</h2>
		<?php if(isset($_POST['my_submit'])) : ?>
			<div id="message" class="updated fade">
				<p>
					<?php _e( 'Settings Saved', 'my' ) ?>
				</p>
			</div>
		<?php endif; ?>
		<form method="post" action="">
			<?php settings_fields('amd_network_settings_group'); ?>
			<p style="margin-bottom:30px;">
				<label style="display:block;" class="input_label">Check the following box after installing the KJV Bible database locally using AMD Library:</label>
				<br />
				<input name="amd_option[amdbible_use_local_kjv]" type="checkbox" <?php if($amdbible_use_local_kjv) echo 'checked="checked"'; ?> value="true" />
				<span class="checkbox_text">Use Local Bible</span>
				<br />
			</p>
			<p style="margin-bottom:30px;">
				<label style="display:block;" class="input_label">Check the following box after installing the devotional database locally using AMD Library:</label>
				<br />
				<input name="amd_option[amdbible_use_local_devos]" type="checkbox" <?php if($amdbible_use_local_devos) echo 'checked="checked"'; ?> value="true" />
				<span class="checkbox_text">Use Local Devos</span>
				<br />
			</p>
			<p style="margin-bottom:30px;">
				<label style="display:block; color:red;" class="input_label"><strong>CAUTION:</strong> By checking the following box and uninstalling the plugin, all plugin related options and database information will be removed. </label>
				<br />
				<input name="amd_option[amdbible_delete_db]" type="checkbox" <?php if($amdbible_delete_db) echo 'checked="checked"'; ?> value="true" />
				<span class="checkbox_text" style="color:red;"><strong>Delete all data on uninstall.</strong></span>
				<br />
			</p>
			<p ></p>
			<p>
				<input name="my_submit" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php
}

/**
 *
 * Add Admin Menu and Page
 *
 */
add_action( 'admin_menu', 'amdbible_admin_page' );
function amdbible_admin_page(){
	$page_title = 'AMD Bible Reading Settings';
	$menu_title = 'AMD Settings';
	$capability = 'manage_options';
	$menu_slug = 'amd-bible-reading';
	$function = 'amdbible_settings_page';
	$icon_url = 'dashicons-book-alt';
	$position = 79;
	
	add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
	
	add_action( 'admin_init', 'amdbible_settings' );

}

if(is_multisite()){
	add_action('network_admin_menu', 'amdbible_admin_plan_editor');
} else {
	add_action('admin_menu', 'amdbible_admin_plan_editor');
}
function amdbible_admin_plan_editor(){
	if(is_multisite()){
		$parent_slug='amd-bible-reading-mu';
	} else {
		$parent_slug='amd-bible-reading';
	}
	$page_title='AMD Bible Reading Plan Editor';
	$menu_title='Plan Editor';
	$capability='manage_options';
	$menu_slug='amdbible-plan-editor';
	$function='amdbible_plan_editor_page';
	
	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}

if(is_multisite()){
	add_action('network_admin_menu', 'amdbible_admin_library');
} else {
	add_action('admin_menu', 'amdbible_admin_library');
}
function amdbible_admin_library(){
	if(is_multisite()){
		$parent_slug='amd-bible-reading-mu';
	} else {
		$parent_slug='amd-bible-reading';
	}
	$page_title='AMD Library';
	$menu_title='AMD Library';
	$capability='manage_options';
	$menu_slug='amdbible-library';
	$function='amdbible_library_page';
	
	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}

add_action('admin_menu', 'amdbible_about');
function amdbible_about(){
	$parent_slug='amd-bible-reading';
	$page_title='About AMD Bible Reading Plugin';
	$menu_title='About AMD';
	$capability='manage_options';
	$menu_slug='amdbible-about';
	$function='amdbible_about_page';
	
	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}

function amdbible_validate_int($input){
	//no user permissions are needed to check input
	if(ctype_digit($input)){
		return $input;
	} else {
		return '';
	}
}

function amdbible_validate_bool($input){
	//no user permissions are needed to check input
	if($input){
		return '1';
	} else{
		return '0';
	}
}

function amdbible_validate_color($input){
	//no user permissions are needed to check input
	if(preg_match('/^#([a-f0-9]{3}){1,2}$/i',$input)){
		return $input;
	} else {
		return '#000000';
	}
}

function amdbible_admin_scripts($hook){
	//only render style script on the AMD Settings page.
	if('toplevel_page_amd-bible-reading' != $hook){
		return;
	}
	wp_enqueue_style('wp-color-picker');
	wp_enqueue_script('amdbible_script_handle',plugins_url('amdbible-script.js',__FILE__),array('wp-color-picker'),false,true);
}
add_action('admin_enqueue_scripts','amdbible_admin_scripts');

function amdbible_settings() {
	//register_setting( $option_group, $option_name [, $sanitize_callback ] );	//for format help
	register_setting( 'amd-settings-group', 'amdbible_plan_chosen', 'amdbible_validate_int' );
	register_setting( 'amd-settings-group', 'amdbible_full_reading_page', 'amdbible_validate_int' );
	register_setting( 'amd-settings-group', 'amdbible_stand_cx_ref', 'amdbible_validate_bool' );
	register_setting( 'amd-settings-group', 'amdbible_ch_number_color', 'amdbible_validate_color' );
	register_setting( 'amd-settings-group', 'amdbible_ver_number_color', 'amdbible_validate_color' );
	
	if(!is_multisite()){
		register_setting( 'amd-settings-group', 'amdbible_use_local_kjv', 'amdbible_validate_bool' );
		register_setting( 'amd-settings-group', 'amdbible_use_local_devos', 'amdbible_validate_bool' );
		register_setting( 'amd-settings-group', 'amdbible_delete_db', 'amdbible_validate_bool' );
	}
	
	
	add_settings_section('amd-plan-option','Currently Selected Reading Plan','amdbible_plan_options','amd-bible-reading');
	//add_settings_field( $id , $title , $callback , $page [, $section [, $args ] ] );	//for format help
	add_settings_field('plan_options','Selected Plan','amdbible_reading_plan','amd-bible-reading','amd-plan-option');
	add_settings_field('full_reading_page','Select Page for default Full Reading Plan','amdbible_full_reading_page','amd-bible-reading','amd-plan-option');
	add_settings_field('stand_ref','Standardize CX References','amdbible_stand_ref','amd-bible-reading','amd-plan-option');
	add_settings_field('amd_ch_num_color','Chapter Number Color','amdbible_ch_num_color','amd-bible-reading','amd-plan-option');
	add_settings_field('amd_ver_num_color','Verse Number Color','amdbible_ver_num_color','amd-bible-reading','amd-plan-option');
	
	
	if(!is_multisite()){
		add_settings_field('use_local_kjv','Use local Bible database','amdbible_use_local_kjv','amd-bible-reading','amd-plan-option');
		add_settings_field('use_local_devos','Use local devotional database','amdbible_use_local_devos','amd-bible-reading','amd-plan-option');
		add_settings_field('amd_delete','Delete Database Data','amdbible_delete','amd-bible-reading','amd-plan-option');
	}
	
}

function amdbible_plan_options(){
	//no user permissions are needed here. read predefined data only.
	global $wpdb;
	$amdbible_plans = $wpdb->get_results( "SELECT id,n,d FROM {$wpdb->base_prefix}amdbible_plans_info" );
	foreach($amdbible_plans as $reading_plan){
		if(get_option('amdbible_plan_chosen',1)==$reading_plan->id){
			$current_plan['id'] =  $reading_plan->id;
			$current_plan['n'] =  $reading_plan->n;
			$current_plan['d'] =  $reading_plan->d;
		}
	}
	echo $current_plan['n'].'<br>'.$current_plan['d'].'</p>';
}

function amdbible_reading_plan(){
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wpdb;
	$amdbible_plans = $wpdb->get_results( "SELECT id,n,d FROM {$wpdb->base_prefix}amdbible_plans_info" );
	foreach($amdbible_plans as $reading_plan){
		if(get_option('amdbible_plan_chosen',1)==$reading_plan->id){
			$current_plan['id'] =  $reading_plan->id;
			$current_plan['n'] =  $reading_plan->n;
			$current_plan['d'] =  $reading_plan->d;
		}
	}
	echo '<select name="amdbible_plan_chosen">';
	foreach($amdbible_plans as $reading_plan){
		echo '<option title="'.$reading_plan->d.'"';
		if(get_option('amdbible_plan_chosen',1)==$reading_plan->id){
			echo 'selected="selected"';
		}
		echo 'value="'.$reading_plan->id.'">'.$reading_plan->id.' - '.$reading_plan->n.'</option>';
	}
	echo '</select>';
}

function amdbible_stand_ref(){
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<input type="checkbox" name="amdbible_stand_cx_ref" value="1"';
	if(get_option('amdbible_stand_cx_ref',1)){
		echo ' checked="checked"';
	}
	echo '>';
}

function amdbible_delete(){
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<input type="checkbox" name="amdbible_delete_db" value="1"';
	if(get_option('amdbible_delete_db',0)){
		echo ' checked="checked"';
	}
	echo '> <p style="color:red;"><strong>CAUTION:</strong> By checking this box and uninstalling the plugin, all plugin related options and database information will be removed. Check this box for a complete removal.</p>';
}

function amdbible_ch_num_color(){
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<input type="text" name="amdbible_ch_number_color" class="my-color-field" data-default-color="#990033" value="'.sanitize_text_field(get_option('amdbible_ch_number_color','#990033')).'" />';
}

function amdbible_ver_num_color(){
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<input type="text" name="amdbible_ver_number_color" class="my-color-field" data-default-color="#000000" value="'.sanitize_text_field(get_option('amdbible_ver_number_color','#000000')).'" />';
}

function amdbible_use_local_kjv(){
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<input type="checkbox" name="amdbible_use_local_kjv" value="1"';
	if(get_option('amdbible_use_local_kjv',0)){
		echo ' checked="checked"';
	}
	echo '> <p>Check this after installing the KJV Bible on the AMD Library settings page. This will make your site run faster and independently of external sites.</p>';
}

function amdbible_use_local_devos(){
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<input type="checkbox" name="amdbible_use_local_devos" value="1"';
	if(get_option('amdbible_use_local_devos',0)){
		echo ' checked="checked"';
	}
	echo '> <p>Check this after installing the devotional on the AMD Library settings page. This will make your site run faster and independently of external sites.</p>';
}

function amdbible_full_reading_page(){
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$pageID = get_option('amdbible_full_reading_page',0);
	?>
	<select name="amdbible_full_reading_page" class="widefat">
		<option value="" >Select Page</option>
		<?php
			// get_page_link( $id )
			$pages = get_pages();
			foreach ($pages as $page) {
				echo '<option value="' . $page->ID . '" id="' . $page->ID . '"', $pageID == $page->ID ? ' selected="selected"' : '', '>', $page->post_title, '</option>';

			}
		?>
	</select>
	<?php
}

/**
 * Display a custom menu page
 */
function amdbible_settings_page(){
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h2>Default Settings for AMD Bible Reading Plugin</h2>
		<p><a href="https://wordpress.org/support/plugin/amd-bible-reading/reviews/" target="_blank">Rate this plugin</a> | <a href="https://wordpress.org/support/plugin/amd-bible-reading/" target="_blank">Help and Support</a> | <a href="https://paypal.me/AnthonyMaster/5" target="_blank">Donate</a></p>
		<?php if(isset($current_plan)){ ?>
		<p><b>Currently Selected Plan:</b> <?php echo $current_plan['n']; ?><br><?php echo $current_plan['d']; ?></p>
		<?php } ?>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'amd-settings-group' ); ?>
			<?php do_settings_sections( 'amd-bible-reading' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

//add settings link to plugins page
function amdbible_add_settings_link ( $links ) {
	//no user permissions should be required here.
	$settings_link = '<a href="admin.php?page=amd-bible-reading">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'amdbible_add_settings_link' );

//add links on plugin list below this plugin for support, rating, donate, and docs
function amdbible_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'amd-bible-reading.php' ) !== false ) {
		$new_links = array(
				'donate' => '<a href="https://paypal.me/AnthonyMaster/5" target="_blank">Donate...</a>',
                'doc' => '<a href="admin.php?page=amdbible-about" target="_blank">Documentation</a>',
                'support' => '<a href="https://wordpress.org/support/plugin/amd-bible-reading/" target="_blank">Support...</a>',
                'rate' => '<a href="https://wordpress.org/support/plugin/amd-bible-reading/reviews/" target="_blank">Rate this plugin...</a>'
				);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'amdbible_plugin_row_meta', 10, 2 );

add_action('wp_dashboard_setup', 'amdbible_admin_dashboard_widgets');

if(is_multisite()){
	add_action('wp_network_dashboard_setup', 'amdbible_admin_dashboard_widgets');
}

function amdbible_admin_dashboard_widgets() {
	global $wp_meta_boxes;
	wp_add_dashboard_widget('amdbible_dashboard_reading', 'Daily Bible Reading', 'amdbible_dashboard_daily_reading');
	wp_add_dashboard_widget('amdbible_dashboard_devo', 'Daily Devotional', 'amdbible_dashboard_devotional');
	
}

function amdbible_dashboard_daily_reading() {
	echo wpautop(do_shortcode("[amd_bible_daily inline=true reference_before=true reference_after=false limit_type='verses' limit=10]..."));
	if(get_option('amdbible_full_reading_page',0)){
		echo '<p><a href="'.get_page_link(get_option('amdbible_full_reading_page')).'" target="_blank">Continue Reading</a> (page and plan set in <a href="'.admin_url('admin.php?page=amd-bible-reading').'">AMD Settings</a>)</p>';
	} else {
		echo '<p>Full Reading Page not set in <a href="'.admin_url('admin.php?page=amd-bible-reading').'">Settings</a></p>';
	}
}

function amdbible_dashboard_devotional() {
	echo wpautop(do_shortcode("[amd_bible_devo]"));
}


function amdbible_about_page(){
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	$page = 'amd-bible-reading';
	$settings_page = add_query_arg(compact('page'), admin_url('admin.php'));
	
	$page = 'amdbible-plan-editor';
	$amdbible_plan_editor = add_query_arg(compact('page'), network_admin_url('admin.php'));
	
	$post_type = 'page';
	$new_page = add_query_arg(compact('post_type'),admin_url('post-new.php'));
	
	$widgets = admin_url('widgets.php');
	
	?>
	<div class="wrap">
		<h2>About AMD Bible Reading?</h2>
		<p><a href="https://wordpress.org/support/plugin/amd-bible-reading/reviews/" target="_blank">Rate this plugin</a> | <a href="https://wordpress.org/support/plugin/amd-bible-reading/" target="_blank">Help and Support</a> | <a href="https://paypal.me/AnthonyMaster/5" target="_blank">Donate</a></p>
		<p>I have built this plugin to be the very best King James Version (KJV) Bible Daily Reading Plugin for WordPress. I have set forth to make this plugin very simple and easy to use. This plugin will generate one widget, "Daily Bible Snippet", and provides support for four shortcodes <code>[amd_bible_devo]</code>; <code>[amd_bible_daily]</code>; <code>[amd_bible_rand]</code>; and <code>[amd_bible]</code>.</p>
		<h2>How to Use?</h2>
		<p>There are a few recommended steps to ensure that the plugin works correctly with your site.</p>
		<ul>
			<li>
				<h3 style="padding-left:5px;">&bull; Select Your Reading Plan:</h3>
				<p style="padding-left:20px;">Navigate to <a href="<?php print $settings_page; ?>" target="_blank">AMD Settings</a>, and set the 'Selected Plan' option. This will set the reading plan for the <code>[amd_bible_daily]</code> shortcode and the widget</p>
			</li>
			<li>
				<h3 style="padding-left:5px;">&bull; Set Standardize References Option:</h3>
				<p style="padding-left:20px;">Navigate to <a href="<?php print $settings_page; ?>" target="_blank">AMD Settings</a>, and set your preference for the 'Standardize CX References' option. Leaving this option checked will force Complex References to become formatted before displaying on the front-end using the <code>[amd_bible_daily]</code> shortcode or widget.</p>
			</li>
			<li>
				<h3 style="padding-left:5px;">&bull; Create a Bible Reading Page:</h3>
				<p style="padding-left:20px;">Add a <a href="<?php print $new_page; ?>">New Page</a>, Name the page and enter the <code>[amd_bible_daily]</code> shortcode into the content and publish the page. There are 14 attributes that can be used to control how the passage is displayed. The defaults very depending on the inline attribute. Please see the below section for default and accepted values:</p>
				<table class='amd_table_borders' cellspacing="0" cellpadding="3">
					<tr>
						<th>NAME</th>
						<th>ACCEPTED VALUES</th>
						<th>DEFAULT</th>
						<th>DEFAULT when inline=true</th>
					</tr>
					<tr>
						<td>inline</td>
						<td>boolean</td>
						<td>false</td>
						<td>(set to true)</td>
					</tr>
					<tr>
						<td>limit</td>
						<td>integer (0=unlimited)</td>
						<td>0</td>
						<td>0</td>
					</tr>
					<tr>
						<td>limit_type</td>
						<td>string 'words' or 'verses'</td>
						<td>''</td>
						<td>''</td>
					</tr>
					<tr>
						<td>show_book</td>
						<td>boolean</td>
						<td>true</td>
						<td>false</td>
					</tr>
					<tr>
						<td>show_chapter</td>
						<td>boolean</td>
						<td>true</td>
						<td>false</td>
					</tr>
					<tr>
						<td>show_verse</td>
						<td>boolean</td>
						<td>true</td>
						<td>false</td>
					</tr>
					<tr>
						<td>reference_before</td>
						<td>boolean</td>
						<td>true</td>
						<td>false</td>
					</tr>
					<tr>
						<td>reference_after</td>
						<td>boolean</td>
						<td>false</td>
						<td>true</td>
					</tr>
					<tr>
						<td>form_before</td>
						<td>boolean</td>
						<td>true</td>
						<td>false</td>
					</tr>
					<tr>
						<td>form_after</td>
						<td>boolean</td>
						<td>true</td>
						<td>false</td>
					</tr>
					<tr>
						<td>plan</td>
						<td>integer</td>
						<td colspan='2'>(Set in AMD Settings)</td>
					</tr>
					<tr>
						<td>day</td>
						<td>integer 1-366</td>
						<td colspan='2'>(current day) can be overridden by variable in URL takes precedence over date variable in shortcode.</td>
					</tr>
					<tr>
						<td>date</td>
						<td>a date string</td>
						<td colspan='2'>(current_date) can be overridden by variable in URL.</td>
					</tr>
					<tr>
						<td>date_format</td>
						<td>string (valid date format)</td>
						<td colspan='2'>'D., M. j, Y'</td>
					</tr>
					<tr>
						<td>no_reading_text</td>
						<td>string</td>
						<td colspan='2'>There is no reading scheduled for this day. Use this day to catch up or read ahead."</td>
					</tr>
				</table>
			</li>
			<li>
				<h3 style="padding-left:5px;">&bull; Add the Widget:</h3>
				<p style="padding-left:20px;">Navigate to Appearance -> <a href="<?php print $widgets; ?>" target="_blank">Widgets</a>, and add the Daily Bible Snippet Widget to your selected sidebar. There are 8 options with the Widget. <br /><br />1) Select the Reading Plan to use for the widget instance.<br /><br />2) Scripture starts with Reference inline? When this option is checked, the scripture will begin with the reference. This is helpful if using a custom widget title. <br /><br />3) Use Reference for Title? When this option is checked, The title will default to the Reference for the daily scripture passage(s). <br /><br />4) Title: this input will be used as the title given that the reference is not used by the previous option.<br /><br />5) Limit Type: this will define how the passage is limited. The options are 'words' or 'verses'. If limit type is set to words, the last verse will most likely be interrupted, but the content will be more of a standard length.<br /><br />6) Limit: using the limit type in the previous option this option defines how many items to display.<br /><br />7) Full Reading Page: select the page created in the previous setup step. This will show the full daily bible reading utilizing the <code>[amd_bible_daily]</code> shortcode.<br /><br />8) Read More Text: this input controls what text is displayed with the page linked in the previous setting. make sure to save your settings.</p>
				<p style="padding-left:20px;"><em><strong>Note:</strong> Some themes support sidebars differently and there are also plugins that will control where and how sidebars will display. I recommend <a target="_blank" href="https://wordpress.org/plugins/smk-sidebar-generator/">SMK Sidebar Generator</a> and <a target="_blank" href="https://wordpress.org/plugins/widget-visibility-time-scheduler/">Widget Visibility Time Scheduler</a></em></p>
			</li>
		</ul>
		
		
		<h2>Displaying the Daily Devotional Morning/Evening</h2>
		<p>I have found Charles Spurgeon Devotionals to be very encouraging and uplifting and have decided to include them in this plugin by utilizing another shortcode <code>[amd_bible_devo]</code>. The steps to adding this into your content is very simple and can be done on any post or page. Simply add the <code>[amd_bible_devo]</code> shortcode into your content at the desired location. At this time there is no option to display a different devotion than the one that is current for the specific time of day and date.</p>
		
		
		<h2>Displaying a random verse</h2>
		<p>Random verses can now be displayed using the new shortcode <code>[amd_bible_rand]</code>. There are four main attributes that can be utilized to determine where the random verse is selected from: <br /><br /><b>ot</b> - (boolean) when set to true this will show verses from the old testament only<br /><br /><b>nt</b> - (boolean) when set to true this will show verses from the new testament only<br /><br /><b>book</b> - (numeric or string: book name, or book abbreviation) when set verses will originate from selected book<br /><br /><b>chapter</b> - (numeric) when set along with book attribute, this will determine from which chapter the verses will originate<br /><br /><b>most_read</b> - (boolean) when set to true this will show verses from a list of the top read 100 Bible verses from a study conducted by biblegateway.com<br /><br /><b>essential</b> - (boolean) when set to true this will show verses from a list of Bible verses essential to the Christian life. These verses cover the following topics: Victorious Life, Romans Road, Assurance, Baptism, Believe in Christ, Bible, Biblical Inspiration, Christ's Sacrifice, Dedication, Friendship, Forgiveness of Sins, Guidance, Hell, Home, Law, Local Church, One Way of Salvation, Others, Peace, Principles, Problem Solving, Procrastination, Salvation without Works, Second Coming, Victory over Satan, Witnessing</p>
		
		
		<h2>Displaying Passages directly using shortcodes with complex references</h2>
		<p>Bible verses and passages can now be displayed anywhere shortcodes are accepted using the shortcode <code>[amd_bible]Your Reference[/amd_bible]</code>. Replace 'Your Reference' with any simple or complex Bible Reference. (See reference examples below.) There are eight attributes that can be used to control how the passage is displayed. The defaults very depending on the inline attribute. Please see the below section for default and accepted values:</p>
		<table class='amd_table_borders' cellspacing="0" cellpadding="3">
			<tr>
				<th>NAME</th>
				<th>ACCEPTED VALUES</th>
				<th>DEFAULT</th>
				<th>DEFAULT when inline=false</th>
			</tr>
			<tr>
				<td>inline</td>
				<td>boolean</td>
				<td>true</td>
				<td>(set to false)</td>
			</tr>
			<tr>
				<td>limit</td>
				<td>integer (0=unlimited)</td>
				<td>0</td>
				<td>0</td>
			</tr>
			<tr>
				<td>limit_type</td>
				<td>string 'words' or 'verses'</td>
				<td>''</td>
				<td>''</td>
			</tr>
			<tr>
				<td>show_book</td>
				<td>boolean</td>
				<td>false</td>
				<td>true</td>
			</tr>
			<tr>
				<td>show_chapter</td>
				<td>boolean</td>
				<td>false</td>
				<td>true</td>
			</tr>
			<tr>
				<td>show_verse</td>
				<td>boolean</td>
				<td>false</td>
				<td>true</td>
			</tr>
			<tr>
				<td>reference_before</td>
				<td>boolean</td>
				<td>false</td>
				<td>true</td>
			</tr>
			<tr>
				<td>reference_after</td>
				<td>boolean</td>
				<td>true</td>
				<td>false</td>
			</tr>
		</table>
		<p>Examples of shortcodes:<br /></p>
		<ul style="padding-left:10px;">
			<li><code>[amd_bible]Psalm 1[/amd_bible]</code></li>
			<li><code>[amd_bible inline='false']Psalm 1-3[/amd_bible]</code></li>
			<li><code>[amd_bible show_book=true show_chapt=true show_verse= true reference_before=true reference_after=false]Psalm 1:1, 3-5; 2-4:2[/amd_bible]</code></li>
		</ul>
		
		<p style="padding-left:10px;"><em><strong>Note:</strong> It is possible to use a shortcode in a widget with <a target="_blank" href="https://wordpress.org/plugins-wp/shortcode-widget/">Shortcode Widget</a></em></p>
		<h2>Editing Reading Plans</h2>
		<div class="notice notice-info inline"><p>Restricted to Super Admins on WordPress Multisite Networks</p></div>
		<?php if((is_multisite() && !current_user_can('manage_network_options'))){ ?>
		<div class="notice notice-error inline"><p>You do not have access to edit reading plans</p></div>
		<?php } ?>
		<p><strong>Complex and Custom Reading Plans are Here!</strong> It is now possible to edit existing Bible Reading Plans and even add new plans. Navigate to AMD Settings -> <a href="<?php print $amdbible_plan_editor; ?>" target="_blank">Plan Editor</a>, and either create a new plan or choose an existing plan to edit. New plans cannot have the same names as existing plans. After creating the plan please add the plan details before selecting the plan in AMD Settings as the chosen plan. This will prevent your widget and shortcodes from displaying no passages. Plans can either use complex references or simple references (leaving complex references unchecked). With simple references there will be two input fields for each date, starting reference and ending reference. Only simple, single verse references can be used in this format. To easily indicate the end of the chapter verse '999' can be used, for instance 'Genesis 4:999' will show up to all of the verses in chapter 4 of Genesis. Book abbreviations can be used such as "Gen" for Genesis. With complex references, you have an additional option to upload a CSV file to easily and quickly insert your custom reading plan from your favorite source. CSV files must be comma delimited with no headers and column 1 being the day of year, column 2 being the corresponding complex reference (or starting reference if simple plan, and column 3 being the ending reference). Complex references can consist of varying diversities. See examples:</p>
		<ul style="padding-left:10px;">
			<li><code>Gen. 1-3</code></li>
			<li><code>Gen 1:1-10</code></li>
			<li><code>Genesis 1:1-5, 7, 10; 2</code></li>
			<li><code>1King 1, 3, 4:5-9; 5</code></li>
			<li><code>First Kings 2</code></li>
			<li><code>II Kings 1; John 4</code></li>
			<li><code>John 1:1, 2:4-10</code></li>
			<li><code>Jude</code></li>
		</ul>
		<p>Be sure to Save Changes before navigating away from the plan editor page or your changes will be lost. <span style="color:red;"><strong>CAUTION!</strong> changing a plan from a simple to complex or vice versa and saving valid data will erase the alternate settings. You could easily erase an entire plan this way.</span></p>
		<h2>Restoring All Data to original</h2>
		<p>In order to restore all data to original installation, you will need to delete the plugin and all associated data and reinstall. To do this <strong>before uninstalling or deactivating the plugin</strong>, you will need to check the "Delete Database Data" option. This will erase any custom reading plans you may have created.</p>
		<h2>What does AMD stand for?</h2>
		<p>AMD stands for A Master Designs. A Master Designs was birthed by a dream I had to fully support myself in the ministry serving the Lord as Paul was a tent maker. Currently this dream has yet to reach fruition but is a work in progress. If I can be of any assistance to you please let me know. My skills involve graphic design, web development, PC support and repair, and overall tech guru. For more information, please visit my website at <a target="_blank" href="http://amasterdesigns.com">A Master Designs</a>. A Master Designing for THE MASTER!</p>
		<h4> - Anthony Master</h4>
		<svg style='display:none;'><style>.amd_table_borders th, .amd_table_borders td { border:solid 1px #323232;}</style></svg>
		<p><a href="https://wordpress.org/support/plugin/amd-bible-reading/reviews/" target="_blank">Rate this plugin</a> | <a href="https://wordpress.org/support/plugin/amd-bible-reading/" target="_blank">Help and Support</a> | <a href="https://paypal.me/AnthonyMaster/5" target="_blank">Donate</a></p>
	</div>
	<?php
}

function amdbible_export_csv(){
	// check user capabilities
	if((!is_multisite() && !current_user_can('manage_options')) || (is_multisite() && !current_user_can('manage_network_options'))){
		return;
	}
	if(isset($_POST['csv_export_bible']) && ($_POST['csv_export_bible']=='kjv')){
		$version = 'kjv';
		global $wpdb;
		$sql = "
			SELECT
			  id,t
			FROM
			  {$wpdb->base_prefix}amdbible_{$version}
		";
		$rows = $wpdb->get_results($sql,'ARRAY_A');
		if($rows){
			$output_filename = 'amdbible_kjv.csv';
			$output_handle = @fopen('php://output', 'w');

			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename=' . 
			$output_filename);
			header('Expires: 0');
			header('Pragma: public');
		    // Parse results to csv format
			foreach ($rows as $row) {
				
				//future version compress exports
				//$row['t'] = gzencode($row['t']);
				
				$leadArray = (array) $row; // Cast the Object to an array
				// Add row to file
				fputcsv($output_handle, $leadArray);
			}
			// Close output file stream
			fclose($output_handle);
			die();
		}
	} else if(isset($_POST['csv_export_devos']) && ($_POST['csv_export_devos']=='devos')){
		$devo = 'devos';
		global $wpdb;
		$sql = "
			SELECT
			  id,d,ap,v,t,c
			FROM
			  {$wpdb->base_prefix}amdbible_{$devo}
		";
		$rows = $wpdb->get_results($sql,'ARRAY_A');
		if($rows){
			$output_filename = 'amdbible_devos.csv';
			$output_handle = @fopen('php://output', 'w');

			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename=' . 
			$output_filename);
			header('Expires: 0');
			header('Pragma: public');
		    // Parse results to csv format
			foreach ($rows as $row) {
				$leadArray = (array) $row; // Cast the Object to an array
				// Add row to file
				fputcsv($output_handle, $leadArray);
			}
			// Close output file stream
			fclose($output_handle);
			die();
		}
	} else if(isset($_POST['csv_export_plan']) && is_numeric($_POST['csv_export_plan'])){
		$plan['id'] = intval($_POST['csv_export_plan']);
		global $wpdb;
		$sql = "
			SELECT
			  id,cx,n,d
			FROM
			  {$wpdb->base_prefix}amdbible_plans_info
			WHERE
			  id=%s
		";
		$sql = $wpdb->prepare($sql,$plan['id']);
		$plan = $wpdb->get_row($sql,'ARRAY_A');
		if($plan['cx']==='1'){
			$sql = "
				SELECT
				  d,cx
				FROM
				  {$wpdb->base_prefix}amdbible_plans
				WHERE
				  p=%s
				ORDER BY
				  d
			";
			$sql = $wpdb->prepare($sql,$plan['id']);
			$rows = $wpdb->get_results($sql,'ARRAY_A');
			foreach($rows as $key=>$value){
				$ref_data = json_decode($value['cx']);
				$rows[$key]['ref'] = $ref_data[0];
				unset($rows[$key]['cx']);
			}
		} else {
			$sql = "
				SELECT
				  d,sv,ev
				FROM
				  {$wpdb->base_prefix}amdbible_plans
				WHERE
				  p=%s
				ORDER BY
				  d
			";
			$sql = $wpdb->prepare($sql,$plan['id']);
			$rows = $wpdb->get_results($sql,'ARRAY_A');
			foreach($rows as $key=>$value){
				$rows[$key]['sv'] = amdbible_reference($value['sv']);
				$rows[$key]['ev'] = amdbible_reference($value['ev']);
			}
		}
		if($rows){
			$output_filename = $plan['n'].date_i18n('-YmdHi').'.csv';
			$output_handle = @fopen('php://output', 'w');

			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename=' . 
			$output_filename);
			header('Expires: 0');
			header('Pragma: public');
		    // Parse results to csv format
			foreach ($rows as $row) {
				$leadArray = (array) $row; // Cast the Object to an array
				// Add row to file
				fputcsv($output_handle, $leadArray);
			}
			// Close output file stream
			fclose($output_handle);
			die();
		}
	}
}
add_action('admin_init','amdbible_export_csv');

function amdbible_import_csv(){
	if((!is_multisite() && current_user_can('manage_options')) || (is_multisite() && current_user_can('manage_network_options'))){
		if(isset($_FILES['processCSV'])){
			$allowed =  array('csv','CSV');
			$filename = $_FILES['processCSV']['name'];
			
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			if(in_array($ext,$allowed)){
				$csv_mimetypes = array('text/csv', 'text/plain', 'application/csv', 'text/comma-separated-values', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'text/anytext', 'application/octet-stream', 'application/txt');
				if (in_array($_FILES['processCSV']['type'], $csv_mimetypes)) {
					$csvData = file_get_contents($_FILES["processCSV"]["tmp_name"]);
					$hash = hash("md5",$csvData);
					$lines = explode(PHP_EOL, $csvData);
					$data = array();
					foreach ($lines as $line) {
						$line_csv = str_getcsv($line);
						if(!empty($line_csv)){
							$data[] = str_getcsv($line);
						}
					}
					$returnData = array();
					if($filename==="amdbible_devos.csv"){
						//only allow non edited devotional.
						if($hash=="cfbe4ea72839394141d4a67eeb681e4e"){
							global $wpdb;
							foreach($data as $element){
								array('id','d','ap','v','t','c');
								if(isset($element[0]) && is_numeric($element[0]) && isset($element[1]) && preg_match('/[0-9]{2}\/[0-9]{2}/',$element[1]) && isset($element[2]) && ($element[2]=="AM" || $element[2]=="PM") && isset($element[3]) && !empty($element[3]) && isset($element[4]) && !empty($element[4]) && isset($element[5]) && !empty($element[5])){
									$id = intval($element[0]);
									$d = $element[1];
									$ap = $element[2];
									$v = sanitize_text_field($element[3]);
									$t = sanitize_text_field($element[4]);
									$c = wp_kses_post($element[5]);
									$returnData[$id] = array('id'=>$id,'d'=>$d,'ap'=>$ap,'v'=>$v,'t'=>$t,'c'=>$c);
								}
							}
							foreach($returnData as $id=>$verse_data){
								$sql = "REPLACE INTO {$wpdb->base_prefix}amdbible_devos (id,d,ap,v,t,c) VALUES (%d,%s,%s,%s,%s,%s)";
								$sql = $wpdb->prepare($sql,$verse_data['id'],$verse_data['d'],$verse_data['ap'],$verse_data['v'],$verse_data['t'],$verse_data['c']);
								$wpdb->query($sql);
							}
							$returnData = 'Devotional import completed.';
						}
					} else if($filename==="amdbible_kjv.csv"){
						//allow only non edited file data
						if($hash=="fa1ab03b32324cdfc52d4d0a9c00bcc7"){
							global $wpdb;
							foreach($data as $element){
								array('id','b','c','v','t');
								if(isset($element[0]) && preg_match('/[0-9]{7}[0-9]?/',$element[0]) && isset($element[1]) && !empty($element[1])){//01001001
									$id = str_pad($element[0], 8, "0", STR_PAD_LEFT);
									$b = intval(ltrim(substr($element[0],0,2),'0'));
									$c = intval(ltrim(substr($element[0],2,3),'0'));
									$v = intval(ltrim(substr($element[0],5,3),'0'));
									$t = sanitize_text_field($element[1]);
								}
								$returnData[$id] = array('id'=>$id,'b'=>$b,'c'=>$c,'v'=>$v,'t'=>$t);
							}
							foreach($returnData as $id=>$verse_data){
								$sql = "INSERT INTO {$wpdb->base_prefix}amdbible_kjv (id,b,c,v,t) VALUES (%d,%d,%d,%d,%s) ON DUPLICATE KEY UPDATE t = %s";
								$sql = $wpdb->prepare($sql,$verse_data['id'],$verse_data['b'],$verse_data['c'],$verse_data['v'],$verse_data['t'],$verse_data['t']);
								$wpdb->query($sql);
							}
							$returnData = 'Bible import completed.';
						}
					} else {
						foreach($data as $element){
							if(isset($element[0]) && is_numeric($element[0]) && $element[0]>=0 && $element[0]<367 && isset($element[1]) && !empty($element[1])){
								if(isset($element[2]) && !empty($element[2])){
									$returnData[$element[0]] = array($element[1],$element[2]);
								} else {
									$returnData[$element[0]] = $element[1];
								}
							}
						}
					}
					print json_encode($returnData);
					die();
				}
			}
		}
	}
}
add_action('admin_init','amdbible_import_csv');

function amdbible_library_page(){
	// check user capabilities
	if((!is_multisite() && !current_user_can('manage_options')) || (is_multisite() && !current_user_can('manage_network_options'))){
		return;
	}
	global $wpdb;
	?>
	<div class="wrap">
		<?php if(isset($messages)){foreach($messages as $message){ ?>
		<div class="<?php echo $message[0]; ?>"><p><?php echo $message[1]; ?></p></div>
		<?php }} ?>
		<?php if(is_multisite()){?>
		<div class="notice notice-warning"><p>WARNING: These changes take effect on the entire network of sites!</p></div>
		<?php } ?>
		<h2>AMD Library</h2>
		<h3>Download Links</h3>
		<p>The King James Version of the Bible in the appropriate CSV file format is available at <a href="http://amasterdesigns.com/amdbible_kjv/" target="_blank">http://amasterdesigns.com/amdbible_kjv/</a></p>
		<p>Spurgeon's Morning and Evening Daily Devotional in the appropriate CSV file format is available at <a href="http://amasterdesigns.com/amdbible_devos/" target="_blank">http://amasterdesigns.com/amdbible_devos/</a></p>
		<hr />
		<h3>Exports</h3>
		<form method="post" id="download_form" action="">
			<input type="hidden" name="csv_export_bible" value="kjv" />
			<?php submit_button('Export KJV Bible','primary'); ?>
		</form>
		<form method="post" id="download_form" action="">
			<input type="hidden" name="csv_export_devos" value="devos" />
			<?php submit_button('Export Devotional','primary'); ?>
		</form>
		<hr />
		<h3>Imports</h3>
		<form method="post">
			<input type="hidden" name="amdbible_action" value="upload_bible_kjv" />
			<table class="form-table">
			  <tr valign="top">
				<th scope="row">KJV Bible</th>
				<td><p style="color:red;">Only supports files named exactly "amdbible_kjv.csv"</p></td>
				<td><input type="file" name="file" id="amdbible-kjv"></td>
				<td><input type="button" value="Import Bible" class="button-primary" onclick="upload('amdbible-kjv');return false;"> <span id="amdbible-kjvImportingWait" style='display:none'><img src="/wp-admin/images/wpspin_light-2x.gif" /></span></td>
			  </tr>
			</table>
		</form>
		<form method="post">
			<input type="hidden" name="amdbible_action" value="upload_devos" />
			<table class="form-table">
			  <tr valign="top">
				<th scope="row">Devotional</th>
				<td><p style="color:red;">Only supports files named exactly "amdbible_devos.csv"</p></td>
				<td><input type="file" name="file" id="amdbible-devos"></td>
				<td><input type="button" value="Import Devo" class="button-primary" onclick="upload('amdbible-devos');return false;"> <span id="amdbible-devosImportingWait" style='display:none'><img src="/wp-admin/images/wpspin_light-2x.gif" /></span></td>
			  </tr>
			</table>
		</form>
		<script type="text/javascript">
			function upload(fileInput){
			  var formData = new FormData();
			  formData.append("action", "upload-attachment");
			  jQuery('#'+fileInput+'ImportingWait').show();
			  var fileInputElement = document.getElementById(fileInput);
			  formData.append("processCSV", fileInputElement.files[0]);
			  formData.append("name", fileInputElement.files[0].name);
			  <?php $my_nonce = wp_create_nonce('media-form'); ?>
			  formData.append("_wpnonce", "<?php echo $my_nonce; ?>");
			  var xhr = new XMLHttpRequest();
			  xhr.onreadystatechange=function(){
				if (xhr.readyState==4 && xhr.status==200){
				  jQuery('#'+fileInput+'ImportingWait').hide();
				  //console.log(xhr.responseText);
				  jQuery('#wpbody-content').prepend('<div class="notice notice-success is-dismissible"><p>'+xhr.responseText+'</p></div>');
				}
			  }
			  xhr.open("POST","/wp-admin/admin.php",true);
			  xhr.send(formData);
			}
		</script>
		
		
	</div>
	<?php
}


function amdbible_plan_editor_page(){
	// check user capabilities
	if((!is_multisite() && !current_user_can('manage_options')) || (is_multisite() && !current_user_can('manage_network_options'))){
		return;
	}
	global $wpdb;
	$messages = array();
	$amdbible_plans = $wpdb->get_results( "SELECT id,cx,n,d FROM {$wpdb->base_prefix}amdbible_plans_info" );
	if(isset($_POST['plan_to_delete']) && is_numeric($_POST["plan_to_delete"]) && isset($_POST['delete'])){
		if($_POST['plan_to_delete']!='1' && $_POST['plan_to_delete']!='2'){
			$plan_to_delete = intval($_POST['plan_to_delete']);
			$sql = "DELETE FROM {$wpdb->base_prefix}amdbible_plans WHERE p=%s";
			$sql = $wpdb->prepare($sql,$plan_to_delete);
			$wpdb->query($sql);
			$sql = "DELETE FROM {$wpdb->base_prefix}amdbible_plans_info WHERE id=%s";
			$sql = $wpdb->prepare($sql,$plan_to_delete);
			$updates = $wpdb->query($sql);
			if($updates){
				$messages[] = array("notice notice-success is-dismissible","Plan deleted successfully.");
				$amdbible_plans = $wpdb->get_results( "SELECT id,cx,n,d FROM {$wpdb->base_prefix}amdbible_plans_info" );
			}
		} else {
			$messages[] = array("notice notice-error is-dismissible","You cannot delete the default plans.");
		}
	}
	if(isset($_POST["plan_to_edit"]) && is_numeric($_POST["plan_to_edit"])){
		$plan_to_edit = strval(intval($_POST["plan_to_edit"]));
		if(isset($_POST['plan_description'])){
			$plan_description = sanitize_text_field($_POST['plan_description']);
			$sql = "UPDATE {$wpdb->base_prefix}amdbible_plans_info SET d=%s WHERE id=%s";
			$sql = $wpdb->prepare($sql,$plan_description,$plan_to_edit);
			$updates = $wpdb->query($sql);
			if($updates){
				$messages[] = array("notice notice-success is-dismissible","Plan description was updated successfully.");
			}
			$amdbible_plans = $wpdb->get_results( "SELECT id,cx,n,d FROM {$wpdb->base_prefix}amdbible_plans_info" );
		}
	}
	if(isset($_POST['new_plan_name']) && !empty($_POST['new_plan_name'])){
		if(!isset($_POST['new_plan_description']) || empty($_POST['new_plan_description'])){
			$messages[] = array("notice notice-warning is-dismissible","Description is Required.");
		}
		$plan_name = sanitize_text_field($_POST['new_plan_name']);
		$plan_description = sanitize_text_field($_POST['new_plan_description']);
		//check for existing plan name to reduce confusion on the backend settings
		$sql = "SELECT id FROM {$wpdb->base_prefix}amdbible_plans_info WHERE n=%s";
		$existing_plan_id = $wpdb->get_var($wpdb->prepare($sql,$plan_name));
		if(!empty($existing_plan_id)){
			$messages[] = array("notice notice-warning is-dismissible","Plan name already exists.");
		} else {
			$sql = "INSERT INTO {$wpdb->base_prefix}amdbible_plans_info (n,d) VALUES (%s,%s)";
			$sql = $wpdb->prepare($sql,$plan_name,$plan_description);
			$updates = $wpdb->query($sql);
			if($updates){
				$messages[] = array("notice notice-success is-dismissible","Plan was created successfully. Please add plan details before activating.");
				$amdbible_plans = $wpdb->get_results( "SELECT id,cx,n,d FROM {$wpdb->base_prefix}amdbible_plans_info" );
				$plan_to_edit = $wpdb->insert_id;
			}
		}
	} else if(isset($_POST["plan_data"]) && isset($_POST["plan"]) && is_numeric($_POST["plan"])){
		$plan_to_edit = strval(intval($_POST["plan"]));
		$valid=true;
		$tempdata = array();
		$temp_to_del_data = array();
		$invalidData = array();
		foreach($_POST["plan_data"] as $d=>$data){
			$d = intval($d);
			foreach($data as $pos=>$ref){
				$ref = sanitize_text_field($ref);
				if(empty($ref)){
					$temp_to_del_data[] = intval($d);
				} else {
					if($pos=='cx'){
						$decoded_ref = amdbible_cx_reference_decode($ref);
						if($decoded_ref[0]===false){
							$valid = false;
							$invalidData[$d][$pos] = $decoded_ref[1];
						}
						$tempdata[$d][$pos] = json_encode(array($ref,$decoded_ref));
					} else {
						if($pos=='ev'){
							$decoded_ref = amdbible_reference_decode($ref,true);
						} else if($pos=='sv'){
							$decoded_ref = amdbible_reference_decode($ref,false);
						} else {
							return;
						}
						if($decoded_ref[0]===false){
							$valid = false;
							$invalidData[$d][$pos] = $decoded_ref[1];
						}
						$tempdata[$d][$pos] = $decoded_ref;
					}
				}
			}
		}
		if($valid && isset($tempdata)){
			$changes = false;
			foreach($tempdata as $d=>$data){
				if(isset($data["cx"])){
					$p=$plan_to_edit;
					$sv=0;
					$ev=0;
					$cx=$data['cx'];
				} else if(isset($data['sv']) && isset($data['ev'])) {
					$p=$plan_to_edit;
					$sv=$data['sv'];
					$ev=$data['ev'];
					$cx='';
				} else {
					//cx nor sv/ev received skip to next iteration
					continue;
				}
				$sql = "INSERT INTO {$wpdb->base_prefix}amdbible_plans (p,d,sv,ev,cx) VALUES (%d,%d,%s,%s,%s) ON DUPLICATE KEY UPDATE sv=%s,ev=%s,cx=%s";
				$sql = $wpdb->prepare($sql,$p,$d,$sv,$ev,$cx,$sv,$ev,$cx);
				$updates = $wpdb->query($sql);
				if($updates && !$changes){
					$changes = true;
				}
			}
			foreach($temp_to_del_data as $d){
				//delete day from plan in database
				$sql = "DELETE FROM {$wpdb->base_prefix}amdbible_plans WHERE p=%d AND d=%d";
				$p=$plan_to_edit;
				$sql = $wpdb->prepare($sql,$p,$d);
				$updates = $wpdb->query($sql);
				if($updates && !$changes){
					$changes = true;
				}
			}
			if($changes){
				$messages[] = array("notice notice-success is-dismissible","Plan was updated successfully.");
			} else {
				$messages[] = array("notice notice-warning is-dismissible","No changes were made.");
			}
		} else if($valid===false){
			$messages[] = array("notice notice-error is-dismissible","There were errors with the submitted plan data. Please see below.");
		}
	}
	$temp_reading_plan_data = array();
	foreach($amdbible_plans as $reading_plan){
		if(isset($plan_to_edit) && $plan_to_edit==$reading_plan->id){
			if(isset($_POST["editing"]) && $_POST["editing"]=="plan_cx"){
				if(isset($_POST["plan_cx"])){
					$query=$wpdb->update("{$wpdb->base_prefix}amdbible_plans_info",array('cx'=>1),array('id'=>$plan_to_edit));
					$current_plan['cx'] =  '1';
				} else {
					$query=$wpdb->update("{$wpdb->base_prefix}amdbible_plans_info",array('cx'=>0),array('id'=>$plan_to_edit));
					$current_plan['cx'] =  '0';
				}
			} else {
				$current_plan['cx'] =  $reading_plan->cx;
			}
			$current_plan['id'] =  $reading_plan->id;
			$current_plan['n'] =  $reading_plan->n;
			$current_plan['d'] =  $reading_plan->d;
			$sql = $wpdb->prepare( "SELECT id,d,sv,ev,cx FROM {$wpdb->base_prefix}amdbible_plans WHERE p=%s ORDER BY d" , $reading_plan->id );
			$temp_reading_plan_data = $wpdb->get_results( $sql );
		}
	}
	
	$reading_plan_data = array();
	foreach($temp_reading_plan_data as $data){
		$reading_plan_data[$data->d] = $data;
	}
	for($i=1;$i<367;$i++){
		if(!isset($reading_plan_data[$i])){
			$sv = (isset($_POST["plan_data"][$i]["sv"])) ? sanitize_text_field($_POST["plan_data"][$i]["sv"]) : '';//string
			$ev = (isset($_POST["plan_data"][$i]["ev"])) ? sanitize_text_field($_POST["plan_data"][$i]["ev"]) : '';//string
			if(isset($_POST["plan_data"][$i]["cx"])){
				$cx_json = json_decode($_POST["plan_data"][$i]["cx"]);
				if(isset($cx_json[0])){
					//only the complex reference is needed
					$cx_json = array(sanitize_text_field($cx_json[0]));
					$cx = json_encode($cx_json);
					unset($cx_json);
				} else {
					$cx = '';
				}
			} else {
				$cx = '';
			}
			$er = (isset($invalidData[$i])) ? $invalidData[$i] : '';
			$reading_plan_data[$i] = (object) array('id'=>$reading_plan->id,'d'=>"$i",'sv'=>$sv,'ev'=>$ev,'cx'=>$cx,'er'=>$er);
		}
	}
	//reorder the array for any days that were filled in that were not in database.
	ksort($reading_plan_data);
	foreach($reading_plan_data as $key=>$object){
		$decoded_plan = json_decode($object->cx);
		if(isset($decoded_plan[0])){
			$reading_plan_data[$key]->cx = sanitize_text_field($decoded_plan[0]);
		}
	}
	?>
	<div class="wrap">
		<?php if(isset($messages)){foreach($messages as $message){ ?>
		<div class="<?php echo $message[0]; ?>"><p><?php echo $message[1]; ?></p></div>
		<?php }} ?>
		<?php if(is_multisite()){?>
		<div class="notice notice-warning"><p>WARNING: These changes take effect on the entire network of sites!</p></div>
		<div class="notice notice-info is-dismissible"><p>We suggest adding a new plan instead of massively editing a current plan that may be in live action on another site in this network.</p></div>
		<?php } ?>
		<h2>AMD Bible Reading Plan Editor</h2>
		<p><a href="https://wordpress.org/support/plugin/amd-bible-reading/reviews/" target="_blank">Rate this plugin</a> | <a href="https://wordpress.org/support/plugin/amd-bible-reading/" target="_blank">Help and Support</a> | <a href="https://paypal.me/AnthonyMaster/5" target="_blank">Donate</a></p>
		<?php if(isset($current_plan)){ ?>
		<p><b>Currently Selected Plan:</b> <?php echo $current_plan['n']; ?><br><?php echo $current_plan['d']; ?></p>
		<?php } ?>
		<form method="post">
			<input type="hidden" id="editing" name="editing" />
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Choose Plan to Edit</th>
					<td>
						<select id="plan_to_edit" name="plan_to_edit" onchange="document.getElementById('editing').value='plan_to_edit';this.form.submit()">
							<option <?php if(!isset($current_plan)){ ?>selected<?php } ?> disabled>Please Select</option>
							<?php foreach($amdbible_plans as $reading_plan){ ?>
							<option title="<?php echo $reading_plan->d; ?>" <?php if(isset($current_plan) && $current_plan['id']==$reading_plan->id){ ?>selected="selected"<?php } ?> value="<?php echo $reading_plan->id; ?>"><?php echo $reading_plan->id." - ".$reading_plan->n; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<?php if(isset($current_plan)){ ?><tr valign="top">
					<th scope="row">Complex References</th>
					<td>
						<input type="checkbox" id="plan_cx" name="plan_cx" onchange="document.getElementById('editing').value='plan_cx';this.form.submit()" <?php if($current_plan['cx']){ ?>checked="checked"<?php } ?> />
					</td>
				</tr>
				<?php if(true || $current_plan['cx']){ ?><tr valign="top">
				<th scope="row">Upload CSV file</th>
				<td>
					<input type="file" name="file" id="file">
					<input type="button" value="Process File" class="button-primary" onclick="upload();return false;">
				</td>
				</tr><?php }} ?>
			</table>
			<script type="text/javascript">
			function upload(){
			  var formData = new FormData();
			  formData.append("action", "upload-attachment");
				
			  var fileInputElement = document.getElementById("file");
			  formData.append("processCSV", fileInputElement.files[0]);
			  formData.append("name", fileInputElement.files[0].name);
			  <?php $my_nonce = wp_create_nonce('media-form'); ?>
			  formData.append("_wpnonce", "<?php echo $my_nonce; ?>");
			  var xhr = new XMLHttpRequest();
			  xhr.onreadystatechange=function(){
				if (xhr.readyState==4 && xhr.status==200){
				  var jsonResponse = JSON.parse(xhr.responseText);
				  for(var day in jsonResponse){
					  if(jsonResponse[day].constructor === Array){
						  document.getElementById('plan_data_sv_'+day).value = jsonResponse[day][0];
						  document.getElementById('plan_data_ev_'+day).value = jsonResponse[day][1];
					  } else {
						  document.getElementById('plan_data_cx_'+day).value = jsonResponse[day];
					  }
				  }
				}
			  }
			  xhr.open("POST","/wp-admin/admin.php",true);
			  xhr.send(formData);
			}
			</script>
		</form>
		<?php if(isset($current_plan)){ ?>
		<form method="post" id="download_form" action="">
			<input type="hidden" name="csv_export_plan" value="<?php echo $current_plan['id']; ?>" />
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Export</th>
					<td><?php submit_button('Export Plan','secondary'); ?></td>
				</tr>
			</table>
		</form>
		<?php } ?>
		<?php if(!isset($plan_to_edit)){ ?>
		<form method="post">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"></th>
					<td><h4>Create New Plan</h4></td>
				</tr>
				<tr valign="top">
					<th scope="row">Plan Name</th>
					<td><input type="text" name="new_plan_name" placeholder="New Plan Name" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">Description</th>
					<td><textarea type="text" name="new_plan_description"></textarea></td>
				</tr>
			</table>
			<?php submit_button('Create New Plan'); ?>
		</form>
		<?php } else { ?>
		<form method="post">
			<input type="hidden" name="plan_to_edit" value="<?php echo $current_plan['id']; ?>" />
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Description</th>
					<td colspan="2"><textarea type="text" name="plan_description" style="width:100%;"><?php echo $current_plan['d']; ?></textarea></td>
				</tr>
				<tr>
					<th></th>
					<td><?php submit_button('Update Plan Description','secondary'); ?></td>
				</tr>
			</table>
		</form>
		<form method="post">
			<input type="hidden" name="plan_to_delete" value="<?php echo $current_plan['id']; ?>" />
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Delete Plan</th>
					<td colspan="2">
						<input type="checkbox" name="delete" value="1" />
					</td>
					<td><?php submit_button('Delete this plan','secondary'); ?></td>
				</tr>
			</table>
		</form>
		<?php } ?>
		<?php if(isset($current_plan)){ ?>
		<form method="post">
			<input type="hidden" name="plan" value="<?php echo $current_plan['id']; ?>" />
			<table class="form-table">
				<tr valign="top">
					<th>Day of Year</th>
					<?php if($current_plan['cx']){ ?>
					<th scope="col">Complex Reference</th>
					<?php } else { ?>
					<th scope="col">Starting Verse</th>
					<th scope="col">Ending Verse</th>
					<?php } ?>
				</tr>
				<?php foreach($reading_plan_data as $data){ $style = ($data->d % 2 == 0) ? "style='background:rgb(225,225,225);'" : ""; ?>
				<tr valign="top" <?php echo $style; ?>>
					<td><?php echo $data->d.": ".getDateFromDay($data->d); ?></td>
					<?php if($current_plan['cx']){ ?>
					<td>
						<?php
							$er = NULL;
							if(property_exists($data,"er")){
								$er = $data->er;
								if(isset($er['cx'])){
									
								}
							}
						?><input <?php if(isset($er['cx'])){ ?>style="background-color:lavenderblush;"<?php } ?> type="text" id="plan_data_cx_<?php echo $data->d; ?>" name="plan_data[<?php echo $data->d; ?>][cx]" value="<?php echo $data->cx; ?>" style="width:380px;"><?php if(isset($er['cx'])){ ?><span style="color:red;"><?php echo $er['cx']; ?></span><?php } ?>
					</td>
					<?php } else { ?>
					<td>
						<?php
							$er = NULL;
							if(property_exists($data,"er")){
								$er = $data->er;
								if(isset($er['sv'])){
									
								}
							}
						?><input <?php if(isset($er['sv'])){ ?>style="background-color:lavenderblush;"<?php } ?> type="text" name="plan_data[<?php echo $data->d; ?>][sv]" id="plan_data_sv_<?php echo $data->d; ?>" value="<?php echo amdbible_reference($data->sv); ?>"><?php if(isset($er['sv'])){ ?><span style="color:red;"><?php echo $er['sv']; ?></span><?php } ?>
					</td>
					<td>
						<?php
							$er = NULL;
							if(property_exists($data,"er")){
								$er = $data->er;
								if(isset($er['ev'])){
									
								}
							}
						?><input <?php if(isset($er['ev'])){ ?>style="background-color:lavenderblush;"<?php } ?> type="text" name="plan_data[<?php echo $data->d; ?>][ev]" id="plan_data_ev_<?php echo $data->d; ?>" value="<?php echo amdbible_reference($data->ev); ?>"><?php if(isset($er['ev'])){ ?><span style="color:red;"><?php echo $er['ev']; ?></span><?php } ?>
					</td>
					<?php } ?>
				</tr>
				<?php } ?>
			</table>
			<?php submit_button(); ?>
		</form>	
		<?php } ?>
	</div>
	<?php
}

add_action( 'admin_head', 'amdbible_add_tinymce' );
function amdbible_add_tinymce() {
    global $typenow;

    // Only on Post Type: post and page
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return ;

    add_filter( 'mce_external_plugins', 'amdbible_add_tinymce_plugin' );
    // Add to line 1 form WP TinyMCE
    add_filter( 'mce_buttons', 'amdbible_add_tinymce_button' );
}

// Include the JS for TinyMCE
function amdbible_add_tinymce_plugin( $plugin_array ) {

    $plugin_array['amdbible_button'] = plugins_url( '/amdbible-tinymce.js', __FILE__ );
    // Print all plugin JS path
    return $plugin_array;
}

// Add the button keys via JS
function amdbible_add_tinymce_button( $buttons ) {

    array_push( $buttons, 'amd_passage_key','amd_daily_bible_key','amd_devo_key','amd_rand_verse_key' );
    // Print all buttons
    return $buttons;
}

function getDateFromDay($dayOfYear) {
	//no user permissions should be required here.
	$date = DateTime::createFromFormat('z', strval($dayOfYear-1));
	return date_format($date, 'D., M. j, Y');
}


/**
 * Get the actual ending verse useful when end of chapter denoted using 999.
 *
 * @param int $ev Ending Verse.
 *
 * @return int Ending Verse Number.
 */
function amdbible_ev($ev){
	//ensure parameter is workable
	if(preg_match('/[0-9]{7,8}/',$ev)){
		global $wpdb;
		return $wpdb->get_var("SELECT id FROM {$wpdb->base_prefix}amdbible_kjv WHERE id BETWEEN 0 AND $ev ORDER BY id DESC LIMIT 1");
	} else {
		return;
	}
}

/**
 * Get the name of the Book of the Bible.
 *
 * @param int $b Book Number.
 *
 * @return string Name of the Book of the Bible.
 */
function amdbible_bk_name($b){
	//ensure parameter is workable
	if(preg_match('/[0-9]{1,2}/',$b)){
		global $wpdb;
		return $wpdb->get_var("SELECT n FROM {$wpdb->base_prefix}amdbible_key_eng WHERE b ='$b'");
	} else {
		return;
	}
	
}

/**
 * Get the passage from the database.
 *
 * @param int $sv Starting Verse.
 * @param int $ev Ending Verse.
 *
 * @return array Array of objects containing verse info formatted as (id), (b)ook number, (c)hapter number, (v)erse number, and (t)ext.
 */
function amdbible_passage($sv,$ev){
	//ensure parameters are workable
	if(!preg_match('/[0-9]{7,8}/',$sv) || !preg_match('/[0-9]{7,8}/',$ev)){
		return;
	}
	if(get_site_option('amdbible_use_local_kjv',0)){
		global $wpdb;
		$query = "
			SELECT *
			FROM {$wpdb->base_prefix}amdbible_kjv
			WHERE id BETWEEN $sv AND $ev
		";
		return $wpdb->get_results($query);
	} else {
		$url = 'http://api.amasterdesigns.com/?sv='.$sv.'&ev='.$ev;
		$JSON = file_get_contents($url);
		//return JSON only if properly formatted
		if(preg_match('/\[(\{"id":"[0-9]{8}","b":"[0-9]{1,2}","c":"[0-9]{1,3}","v":"[0-9]{1,3}","t":"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"\})(,\{"id":"[0-9]{8}","b":"[0-9]{1,2}","c":"[0-9]{1,3}","v":"[0-9]{1,3}","t":"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"\})*\]/',$JSON)){
			return json_decode($JSON);
		} else {
			return;
		}
		
	}
}

function amdbible_devo($month,$day,$ampm){
	//ensure parameters are workable
	if(($ampm!='AM' && $ampm!='PM') || !preg_match('/[0-9]{2}/',$month) || !preg_match('/[0-9]{2}/',$day)){
		return;
	}
	if(get_site_option('amdbible_use_local_devos',0)){
		global $wpdb;
		$query = "
			SELECT *
			FROM {$wpdb->base_prefix}amdbible_devos
			WHERE d='".$month."/".$day."' AND ap='$ampm'
		";
		return $wpdb->get_row($query);
	} else {
		$url = 'http://api.amasterdesigns.com/?m='.$month.'&d='.$day.'&ap='.$ampm;
		$JSON = file_get_contents($url);
		//return JSON only if properly formatted
		//preg_match('/\{"id":"[0-9]+","d":"[0-9]{2}\\\/[0-9]{2}","ap":"[AP]M","v":"[^"\\]*(?:\\.[^"\\]*)*","t":"[^"\\]*(?:\\.[^"\\]*)*","c":"[^"\\]*(?:\\.[^"\\]*)*"\}/',$JSON)
		if(preg_match('/\{"id":"[0-9]+","d":"[0-9]{2}\\\\\/[0-9]{2}","ap":"[AP]M","v":"[^"\\\\]*(?:\\\\.[^"\\\\]*)*","t":"[^"\\\\]*(?:\\\\.[^"\\\\]*)*","c":"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"\}/',$JSON)){
			return json_decode($JSON);
		} else {
			return;
		}
	}
}

function amdbible_cx_reference($day,$plan){
	if(!is_numeric($day) || empty($plan) || !isset($plan['id']) || !is_numeric($plan['id']) || !isset($plan['cx']) || !$plan['cx']){
		//variables are not properly defined or not complex plan return false
		return false;
	}
	global $wpdb;
	$query = "
	    SELECT *
	    FROM {$wpdb->base_prefix}amdbible_plans
	    WHERE p=%d AND d=%d
	";
	$ref = $wpdb->get_results($wpdb->prepare($query,$plan['id'],$day));
	$ref = json_decode($ref[0]->cx);
	$ref = $ref[0];
	return $ref;
}

/**
 * Get the reference using starting verse and optional ending verse.
 *
 * @param int $sv Starting Verse.
 * @param int|null $ev Ending Verse.
 *
 * @return string Bible Reference.
 */
function amdbible_reference($sv,$ev = null){
	//ensure parameters are workable
	if(empty($sv) || $sv=="00000000" || !preg_match('/[0-9]{7,8}/',$sv) || ($ev!==null && !preg_match('/[0-9]{7,8}/',$ev))){
		return;
	}
	$sb = amdbible_bk_name(substr($sv,0,2));
	$sc = intval(substr($sv,2,3));
	$sve = intval(substr($sv,5,3));
	if(!is_null($ev) && !empty($ev) && $sv!=$ev){
		$ev = amdbible_ev($ev);
		$eb = amdbible_bk_name(substr($ev,0,2));
		$ec = intval(substr($ev,2,3));
		$ev = intval(substr($ev,5,3));
		if($sb!=$eb){
			return $sb." ".$sc.":".$sve." - ".$eb." ".$ec.":".$ev;
		} else {
			if($sc!=$ec){
				return $sb." ".$sc.":".$sve." - ".$ec.":".$ev;
			} else {
				return $sb." ".$sc.":".$sve."-".$ev;
			}
		}
	} else {
		return $sb." ".$sc.":".$sve;
	}
}

//get book names and abbreviations equivalents in the form of an array("abbreviation\name"=>book_number)
$table_name = $wpdb->base_prefix.'amdbible_key_abbr_eng';
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
	$sql = "SELECT a AS n,b FROM {$wpdb->base_prefix}amdbible_key_abbr_eng UNION SELECT n,b FROM {$wpdb->base_prefix}amdbible_key_eng ORDER BY b,n";
	global $books;
	$books = $wpdb->get_results($sql,OBJECT_K);
	foreach($books as $key=>$objects){
		$books[$key] = $objects->b;
	}
}

/**
 * Decode the reference into verse code.
 *
 * @param string $reference Biblical reference.
 * @param bool $ev True if end of chapter (i.e. 999).
 *
 * @return string verse code OR original reference on decode failure.
 */
function amdbible_reference_decode($reference,$ev=false){
	global $books;
	if(preg_match("/^\s?((?:[1-3]|(?:I|II|III)|(?:1st|2nd|3rd)|(?:First|Second|Third)|(?:Song of ))? ?[A-Z]{1,3}[a-z]*)\.?\s?(?:([0-9]{1,3})(?::([0-9]{1,3}))?)?$/", $reference, $match)){
		if(isset($books[$match[1]])){
			$book = str_pad($books[$match[1]],3,"0",STR_PAD_LEFT);
			if(isset($match[2])){
				$chapter = str_pad($match[2],3,"0",STR_PAD_LEFT);
				if(isset($match[3])){
					$verse = str_pad($match[3],3,"0",STR_PAD_LEFT);
				} else if($ev){
					$verse = "999";
				} else {
					$verse = "001";
				}
			} else if($ev) {
				$chapter = "999";
				$verse = "999";
			} else {
				$chapter = "001";
				$verse = "001";
			}
			return $book.$chapter.$verse;
		}
	}
	return array(false,"");
}
 
function amdbible_cx_reference_decode($reference,$position="sv",$book_name=NULL,$references=array()){
	if(empty($reference)){
		return array(false,"Reference cannot be empty");
	} else {
		if(preg_match("/^\s?((?:[1-3]|(?:I|II|III)|(?:1st|2nd|3rd)|(?:First|Second|Third)|(?:Song of ))? ?[A-Z]{1,3}[a-z]*)\.?\s?/", $reference, $match)){
			$book_name = $match[1];
			$reference = substr($reference, strlen($match[0]));
		} else if(!is_null($book_name)){
			//book_name already set continue to check for chapter
			// this line needed to prevent error in else statement
		} else {
			return array(false,"Book not defined.");
		}
		if(empty($reference) || (substr($reference, 0, 1) == ";") || (substr($reference, 0, 1) == "-")){
			if($position=="sv"){
				$chapter=1;
				$verse=1;
				$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
			}
			if((empty($reference) || (substr($reference, 0, 1) == ";")) && $position=="sv"){
				$position="ev";
			}
			if((substr($reference, 0, 1) == "-") AND $position=="sv"){
				$reference = substr($reference,1);
				$position="ev";
				$references = amdbible_cx_reference_decode($reference,$position,$book_name,$references);
				return $references;
			}
			$chapter=999;
			$verse=999;
			$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
			if(substr($reference, 0, 1) == ";"){
				$reference = substr($reference,1);
				$position="sv";
				$references = amdbible_cx_reference_decode($reference,$position,$book_name,$references);
				return $references;
			}
		} else if(preg_match("/^\s?([0-9]{1,3})(?:\z|;|:|,|-)/",$reference)){
			if(preg_match("/^\s?([0-9]{1,3}):([0-9]{1,3})(?![0-9])/",$reference,$match)){
				$chapter=$match[1];
				$verse=$match[2];
				$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
				$reference = substr($reference,strlen($match[0]));
				if(preg_match("/^\s?-\s?([0-9]{1,3})(?![0-9])/",$reference,$match)){
					$position="ev";
					$reference = trim(substr(trim($reference),1));
					if(preg_match("/^\s?([0-9]{1,3}):([0-9]{1,3})(?![0-9])/",$reference,$match2)){
						$chapter=$match2[1];
						$verse=$match2[2];
						$reference = substr($reference,strlen($match2[0]));
					} else {
						$verse=$match[1];
						$reference = substr($reference,strlen($match[1]));
					}
					$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
					while(preg_match("/^,\s?([0-9]{1,3})(?![0-9])/",$reference,$match)){
						$verse=$match[1];
						$reference = substr($reference,strlen($match[0]));
						$position="sv";
						$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
						if(preg_match("/^\s?-\s?([0-9]{1,3})(?![0-9])/",$reference,$match)){
							$verse=$match[1];
							$reference = substr($reference,strlen($match[0]));
						}
						$position="ev";
						$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
					}
					if(substr($reference, 0, 1) == ";"){
						$reference = substr($reference,1);
						$position="sv";
						$references = amdbible_cx_reference_decode($reference,$position,$book_name,$references);
						return $references;
					} else if(empty($reference)){
						return $references;
					}
				} else if(preg_match("/^\s?-\s?((?:[1-3]|(?:I|II|III)|(?:1st|2nd|3rd)|(?:First|Second|Third)|(?:Song of ))? ?[A-Z]{1,3}[a-z]*)\.?\s/", $reference, $match)){
					$position="ev";
					$book_name=$match[1];
					$reference = substr($reference,strlen($match[0]));
					$references = amdbible_cx_reference_decode($reference,$position,$book_name,$references);
					return $references;
				}
				while(preg_match("/^,\s?([0-9]{1,3})(?![0-9])/",$reference,$match)){
					$position="ev";
					$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
					$verse=$match[1];
					$reference = substr($reference,strlen($match[0]));
					$position="sv";
					$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
					if(preg_match("/^\s?-\s?([0-9]{1,3})(?![0-9])/",$reference,$match)){
						$verse=$match[1];
						$reference = substr($reference,strlen($match[0]));
					}
				}
				$position="ev";
				$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
				if(substr($reference, 0, 1) == ";"){
					$reference = substr($reference,1);
					$position="sv";
					$references = amdbible_cx_reference_decode($reference,$position,$book_name,$references);
					return $references;
				}
			} else if(preg_match("/^\s?([0-9]{1,3})(?:\z|-|,|;)/",$reference,$match)){
				$chapter=$match[1];
				if($position=="sv"){
					$verse=1;
				} else {
					$verse=999;
				}
				$reference = substr(trim($reference),strlen($match[1]));
				$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
				if(empty($reference)){
					if($position=="sv"){
						$position="ev";
						$verse=999;
						$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
					}
				} else {
					if(in_array(substr($reference, 0, 1),array(",",";"))){
						$reference = substr($reference,1);
						if($position=="sv"){
							$position="ev";
							$verse=999;
							$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
						}
						$position="sv";
						$references = amdbible_cx_reference_decode($reference,$position,$book_name,$references);
						return $references;
					}
					if(substr($reference, 0, 1) == "-"){
						$reference = substr($reference,1);
						$position="ev";
						if(preg_match("/^\s?-\s?((?:[1-3]|(?:I|II|III)|(?:1st|2nd|3rd)|(?:First|Second|Third)|(?:Song of ))? ?[A-Z]{1,3}[a-z]*)\.?\s/", $reference, $match)){
							$book_name=$match[1];
							$reference = substr($reference,strlen($match[0]));
							$references = amdbible_cx_reference_decode($reference,$position,$book_name,$references);
							return $references;
						}
						if(preg_match("/^\s?([0-9]{1,3}):([0-9]{1,3})(?![0-9])/",$reference,$match)){
							$chapter=$match[1];
							$verse=$match[2];
							$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
							$reference = substr($reference,strlen($match[0]));
							while(preg_match("/^,\s?([0-9]{1,3})(?![0-9])/",$reference,$match)){
								$position="sv";
								$verse=$match[1];
								$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
								$reference = substr($reference,strlen($match[0]));
								if(preg_match("/^\s?-\s?([0-9]{1,3})(?![0-9])/",$reference,$match)){
									$verse=$match[1];
									$reference = substr($reference,strlen($match[0]));
								}
								$position="ev";
								$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
							}
						} else if(preg_match("/^\s?([0-9]{1,3})(?![0-9])/",$reference,$match)){
							$chapter=$match[1];
							$verse=999;
							$references = amdbible_set_reference($book_name,$chapter,$verse,$position,$references);
							$reference = substr($reference,strlen($match[0]));
							if(substr($reference, 0, 1) == ","){
								$reference = substr($reference,1);
								$position="sv";
								$references = amdbible_cx_reference_decode($reference,$position,$book_name,$references);
								return $references;
							}
							if(substr($reference, 0, 1) == ";"){
								$reference = substr($reference,1);
								$position="sv";
								$references = amdbible_cx_reference_decode($reference,$position,$book_name,$references);
								return $references;
							}
						}
					}
				}
			}
		} else {
			return array(false,"Unexpected character(s) in string at '$reference'.");
		}
	}
	if(!empty($reference)){
		return array(false,"Remaining reference '$reference' could not be decoded.");
	}
	return $references;
}

function amdbible_set_reference($book,$chapter,$verse,$position,$references){
	global $books;
	if(isset($books[$book])){
		$ref_code = str_pad($books[$book],2,"0",STR_PAD_LEFT);
	} else {
		return array(false,"Book name '$book' not defined invalid spelling or abbreviation");
	}
	$ref_code .= str_pad($chapter,3,"0",STR_PAD_LEFT);
	$ref_code .= str_pad($verse,3,"0",STR_PAD_LEFT);
	if($position=="sv"){
		$references[][$position] = $ref_code;
	} else if($position=="ev") {
		$references[count($references)-1][$position] = $ref_code;
	} else {
		return array(false,"The starting and ending verse could not be calculated.");
	}
	return $references;
}

/**
 * Format the Bible Passage.
 *
 * @param array $passages Array of objects containing verse info formatted as (id), (b)ook number, (c)hapter number, (v)erse number, and (t)ext.
 * @param boolean|int $limit False or number of items to limit.
 * @param null|string $limit_type Values 'words' or 'verses' to set limit type.
 * @param boolean $show_book Show book of the Bible.
 * @param boolean $show_chapt Show chapter of the book.
 * @param boolean $show_verse_num Show verse number of the chapter.
 *
 * @return string HTML formatted Bible text with book, chapter, and/or verse numbers if set to true.
 */
function amdbible_format_passage($passages,$limit = false,$limit_type = null,$show_book = true,$show_chapt = true,$show_verse_num = true, $inline = false,$no_reading_text = "There is no reading scheduled for this day. Use this day to catch up or read ahead."){
	//
	$no_reading_text = sanitize_text_field($no_reading_text);
	//limit must be a integer not a true boolean.
	if(is_numeric($limit)){
		$limit = intval($limit);
	} else {
		$limit = false;
	}
	if($inline){
		$content = "<span class='amdbible_inline'>";
	} else if($limit){
		$content = "<div class='amdbible_snippet'>";
	} else {
		$content = "<div class='amdbible_passage'>";
	}
	$b = $c = $v = null;
	$count = 0;
	$snippet = false;
	if(empty($passages) || is_null($passages)){
		if(!$inline){
			$content .= "<p></p><p>";
		}
		$content .= "<span class='amdbible_text'>".$no_reading_text."</span>";
		if(!$inline){
			$content .= "</p>";
		}
	}
	if(!is_null($passages)){
		foreach($passages as $verse){
			if($limit && $count >= $limit){
				break;
				$snippet = true;
			}
			if($verse->c != $c){
				if($verse->b != $b){
					if($b !== null){
						if(!$inline){
							$content .= "</p>";
						}
					}
					if($show_book){
						if($inline){
							$content .= "<span class='amdbible_book'><b>".sanitize_text_field(amdbible_bk_name($verse->b))."</b></span> ";
						} else {
							$content .= "<p class='amdbible_book'>".sanitize_text_field(amdbible_bk_name($verse->b))."</p>";
						}
					}
				}
				if($show_chapt){
					if($inline){
						$content .= "<span class='amdbible_chapter'>Chapter ".intval($verse->c).".</span> ";
					} else {
						$content .= "<p><span class='amdbible_chapter'>".intval($verse->c)."</span>";
					}
				} else {
					if(!$inline){
						$content .= "<p>";
					}
				}
			}
			if($show_verse_num){
				$content .= "<span class='amdbible_verse'>".intval($verse->v)."</span> ";
			}
			if($limit && $limit_type == 'words'){
				$words_in_verse = str_word_count($verse->t);
				$count = $count+$words_in_verse;
				if($count > $limit){
					$trim  = $words_in_verse - ($count - $limit);
					$words = str_word_count($verse->t, 2);
					$pos = array_keys($words);
					$verse->t = substr($verse->t, 0, $pos[$trim]);
				}
			}
			$content .= "<span class='amdbible_text'>".sanitize_text_field($verse->t)."</span> ";
			$b = $verse->b;
			$c = $verse->c;
			$v = $verse->v;
			if($limit && $limit_type == 'verses'){
				$count++;
			}
		}
	}
	if($snippet){
		$content .= ". . .";
	}
	if(!$inline){
		$content .= "</p></div>";
	}
	return $content;
}

/**
 * Link the css file.
 */
function amdbible_css(){
	wp_register_style('amdbible-passage',plugins_url('/amdbible-passage.css', __FILE__ ),array(),'20151001','all');
	wp_enqueue_style('amdbible-passage');
	$ch_number_color = get_option('amdbible_ch_number_color','#990033');
	$ver_number_color = get_option('amdbible_ver_number_color','#000000');
	$custom_css = "
		.amdbible_chapter{
			color: {$ch_number_color};
		}
		.amdbible_verse{
			color: {$ver_number_color};
		}
	";
	wp_add_inline_style('amdbible-passage',$custom_css);
	
}
// add action to link css file
add_action('wp_enqueue_scripts','amdbible_css');

/**
 * This function will allow pages that do not use permalinks but instead use GET variables to display the page to work correctly with the forms contained in the shortcode amd_bible_daily
 *
 * @param array $removed_vars Array of string names of attributes that should not be forwarded.
 *
 * @return string HTML form inputs containing GET attributes to forward
 */
function amdbible_getdata_hidden_fields($removed_vars = array()){
	$content = "";
	foreach($_GET as $name=>$value){
		if(!in_array($name,$removed_vars)){
			//secure output
			$name = esc_attr($name);
			$value = esc_attr($value);
			$content .= '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
		}
	}
	return $content;
}

/**
 * Display the HTML form for selecting the daily passage.
 *
 * @param object $date Datetime object.
 *
 * @return string HTML form for daily selection.
 */
function amdbible_date_form($date){
	$prev = date_i18n("z",$date);
	$next = date_i18n("z",$date)+2;
	ob_start();
?>
<div style="width:15%; float:left;">
	<form action="" method="get">
		<?php echo amdbible_getdata_hidden_fields(array('d','date')); ?>
		<button type="submit" name="d" value="<?php echo esc_attr($prev); ?>">&lt;</button>
	</form>
</div>
<div style="width:70%; float:left; text-align:center">
	<form action="" method="get">
		<?php echo amdbible_getdata_hidden_fields(array('d','date')); ?>
		<p><input name="date" type="date" value="<?php echo esc_attr(date_i18n("Y-m-d",$date)); ?>" onChange="this.form.submit();" /></p>
	</form>
</div>
<div style="width:15%; float:left;">
	<form action="" method="get">
		<?php echo amdbible_getdata_hidden_fields(array('d','date')); ?>
		<button type="submit" name="d" value="<?php echo esc_attr($next); ?>" style="float:right">&gt;</button>
	</form>
</div>
<div style="width=0%; height:0%; clear:left;"></div>
<?php

	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

/**
 * Display the HTML form for selecting the daily passage.
 *
 * @param int $day Day of the year 1-366.
 * @param int $plan Reading Plan default 1.
 *
 * @return array passage array containing info for each passage in plan and complete array containing all of the passages together in one array.
 */
function amdbible_daily_passage($day,$plan){
	if(!is_numeric($day) || empty($plan) || !isset($plan['id']) || !isset($plan['cx'])){
		//variables are not properly defined return false
		return false;
	}
	$day = intval($day);
	$plan['id'] = intval($plan['id']);
	global $wpdb;
	$return  = array("complete"=>array(),"passages"=>array());	
	$query = "
	    SELECT *
	    FROM {$wpdb->base_prefix}amdbible_plans
	    WHERE p=%d AND d=%d
	";
	$plan_passage = $wpdb->get_results($wpdb->prepare($query,$plan['id'],$day));
	foreach($plan_passage as $p_data){
		if(isset($plan['cx']) && $plan['cx'] && property_exists($p_data,'cx')){
			$cx_data = json_decode($p_data->cx);
			
			foreach($cx_data[1] as $data){
				$sv = $data->sv;
				$ev = $data->ev;
				$passage = amdbible_passage($sv,$ev);
				$return["passages"][] = array(
					"sv"=>$sv,
					"ev"=>$ev,
					"passage"=>$passage
				);
				$return["complete"] = array_merge($return["complete"],$passage);
			}
		} else {
			$sv = $p_data->sv;
			$ev = $p_data->ev;
			$passage = amdbible_passage($sv,$ev);
			$return["passages"][] = array(
				"sv"=>$sv,
				"ev"=>$ev,
				"passage"=>$passage
			);
			$return["complete"] = array_merge($return["complete"],$passage);
		}
		break;
	}
	return $return;
}

function amdbible_get_passage($decoded_reference){
	$return  = array("complete"=>array(),"passages"=>array());
	$cx_data = $decoded_reference;
	foreach($cx_data as $data){
		$sv = $data['sv'];
		$ev = $data['ev'];
		$passage = amdbible_passage($sv,$ev);
		$return["passages"][] = array(
			"sv"=>$sv,
			"ev"=>$ev,
			"passage"=>$passage
		);
		$return["complete"] = array_merge($return["complete"],$passage);
	}
	return $return;
}

function amdbible_get_plan($id = null){
	global $wpdb;
	if(is_null($id)){
		$id = get_option('amdbible_plan_chosen',1);
	}
	$id = intval($id);
	$plan = array(
		'id'=>'1',
		'cx'=>false,
		'n'=>'Straight Through',
		'd'=>'Read the Bible through in 1 year.'
	);
	if(is_numeric($id)){
		$query = "
			SELECT *
			FROM {$wpdb->base_prefix}amdbible_plans_info
			WHERE id=%d
		";
		$plan_temp = $wpdb->get_row($wpdb->prepare($query,$id),ARRAY_A);
		if($plan_temp){
			$plan = $plan_temp;
		}
	}
	return $plan;
}

function amdbible_shortcode_devo( $atts ) {
	$txt = "";
	$month = date_i18n('m');
	$day = date_i18n('d');
	$ap = date_i18n('A');
	$m_e = ($ap=="AM") ? "Morning" : "Evening";
	$date = date_i18n("l, F jS");
	$devo = amdbible_devo($month,$day,$ap);
	if(empty($devo)){
		$txt = <<<EOD
		<h3>$date - $m_e</h3>
		<p>Sorry, devotional for this day is not loaded.</p>
EOD;
		
	} else {
		$title = esc_html($devo->t);
		$verse = esc_html($devo->v);
		//html markup is allowed in content
		$content = $devo->c;
		$txt = <<<EOD
		<h3>$date - $m_e</h3>
		<h1>$title - $verse</h1>
		<h5><i>— Morning & Evening, with Charles Spurgeon Devotionals</i></h5>
		<p>$content</p>
EOD;
	}
	return $txt;
}
add_shortcode( 'amd_bible_devo', 'amdbible_shortcode_devo' );

/**
 * Display the Bible Reading Plan in place of shortcode
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string content to be displayed in place of shortcode
 */
function amdbible_shortcode_daily( $atts ) {
	$args = shortcode_atts(
		array(
			'plan' => null,
			'limit' => 0,
			'limit_type' => null,
			'show_book' => null,
			'show_chapt' => null,
			'show_verse' => null,
			'inline' => false,
			'reference_before' => null,
			'reference_after' => null,
			'form_before' => null,
			'form_after' => null,
			'date' => null, // *
			'day' => null, // *
			'date_format' => "D., M. j, Y",
			'no_reading_text' => "There is no reading scheduled for this day. Use this day to catch up or read ahead."
		),
		$atts
	);
	if(is_null($args['limit_type'])){
		$args['limit_type'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? 'words' : 'verses';
	}
	if(is_null($args['show_book'])){
		$args['show_book'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['show_verse'])){
		$args['show_verse'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['show_chapt'])){
		$args['show_chapt'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['reference_before'])){
		$args['reference_before'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['reference_after'])){
		$args['reference_after'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? true : false;
	}
	if(is_null($args['form_before'])){
		$args['form_before'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['form_after'])){
		$args['form_after'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	$limit = filter_var($args['limit'] ,FILTER_VALIDATE_INT);
	$limit_type = esc_attr($args['limit_type']);
	if($limit_type!='words'&&$limit_type!='verses'){
		$limit_type = null;
	}
	$show_book = filter_var($args['show_book'] ,FILTER_VALIDATE_BOOLEAN);
	$show_chapt = filter_var($args['show_chapt'] ,FILTER_VALIDATE_BOOLEAN);
	$show_verse = filter_var($args['show_verse'] ,FILTER_VALIDATE_BOOLEAN);
	$inline = filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN);
	$reference_before = filter_var($args['reference_before'],FILTER_VALIDATE_BOOLEAN);
	$reference_after = filter_var($args['reference_after'] ,FILTER_VALIDATE_BOOLEAN);
	$form_before = filter_var($args['form_before'] ,FILTER_VALIDATE_BOOLEAN);
	$form_after = filter_var($args['form_after'] ,FILTER_VALIDATE_BOOLEAN);
	$day = filter_var($args['day'] ,FILTER_VALIDATE_INT);
	if((1 >= $day) || ($day >= 366)){
		$day = null;
	}
	$attr_date = $args['date'];
	if(is_null($attr_date) || !strtotime($attr_date)){
		$attr_date = null;
	}
	$date_format = $args['date_format'];
	$no_reading_text = esc_attr($args['no_reading_text']);
	
	
	$txt = "";
	//accept a parameter of d for day of the year value between 1 and 366
	if(isset($_GET["d"]) && $_GET["d"] && $_GET["d"]>=1 && $_GET["d"]<=366){
		$doy = intval($_GET["d"]);
		$diff = $doy-date_i18n('z')-1;
		if($diff>=0){$diff = "+".$diff;}
		$date = strtotime("$diff days");
	//accept the a parameter of date where format is yyyy-mm-dd
	} else if(isset($_GET["date"]) && $_GET["date"] && $_GET["date"]==date_i18n("Y-m-d",strtotime($_GET["date"]))){
		$date = strtotime($_GET["date"]);
	} else if(!is_null($day)){
		$diff = $day-date_i18n('z')-1;
		if($diff>=0){$diff = "+".$diff;}
		$date = strtotime("$diff days");
	} else if(!is_null($attr_date)){
		$date = strtotime($attr_date);
	//else set the date to current day
	} else {
		$date = time();
	}
	//offset day of year by 1 to conform with 1-366 instead of 0-365
	$day = date_i18n('z',$date)+1;
	
	$plan = amdbible_get_plan($args['plan']);
	$data = amdbible_daily_passage($day,$plan);
	if($data===false){
		//Data returned empty or false so there is no need
		// to continue and print empty passage.
		return false;
	}
	$passages = $data["complete"];
	$references = "";
	
	if(!get_option('amdbible_stand_cx_ref',1) && isset($plan['cx']) && $plan['cx']){ //check for standardize-cx_ref option 
		$references = amdbible_cx_reference($day,$plan);
	} else {
		foreach($data["passages"] as $passage){
			if(!empty($references)){
				$references .= '; ';
			}
			$references .= amdbible_reference($passage["sv"],$passage["ev"]);
		}
	}
	$references = esc_html($references);
	
	if($form_before){
		$txt .= amdbible_date_form($date);
	}
	if($reference_before){
		if($inline){
			$txt .= "<span class='amdbible_title'>".date_i18n($date_format,$date).". <em>".$references." KJV</em> &mdash; </span>";
		} else {
			$txt .= "<p class='amdbible_title'>".date_i18n($date_format,$date)." &mdash; ".$references." KJV</p>";
		}
	}
	$txt .= amdbible_format_passage($passages,$limit,$limit_type,$show_book,$show_chapt,$show_verse,$inline,$no_reading_text);
	if($reference_after){
		if($inline){
			$txt .= " <span class='amdbible_title'> &mdash; <em>".$references." KJV reading for ".date_i18n($date_format,$date)."</em></span>";
		} else {
			$txt .= "<p class='amdbible_title'>".$references." KJV reading for ".date_i18n($date_format,$date)."</p>";
		}
	}
	if($form_after){
		$txt .= amdbible_date_form($date);
	}
	return $txt;
}
add_shortcode( 'amd_bible_daily', 'amdbible_shortcode_daily' );

function amdbible_shortcode_passage( $atts, $content = null ) {
	$txt = "";
	$args = shortcode_atts(
		array(
			'limit' => 0,
			'limit_type' => null,
			'show_book' => null,
			'show_chapt' => null,
			'show_verse' => null,
			'inline' => true,
			'reference_before' => null,
			'reference_after' => null,
		),
		$atts
	);
	if(is_null($args['limit_type'])){
		$args['limit_type'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? 'words' : 'verses';
	}
	if(is_null($args['show_book'])){
		$args['show_book'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['show_verse'])){
		$args['show_verse'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['show_chapt'])){
		$args['show_chapt'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['reference_before'])){
		$args['reference_before'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['reference_after'])){
		$args['reference_after'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? true : false;
	}
	$limit = filter_var($args['limit'] ,FILTER_VALIDATE_INT);
	$limit_type = esc_attr($args['limit_type']);
	if($limit_type!='words'&&$limit_type!='verses'){
		$limit_type = null;
	}
	$show_book = filter_var($args['show_book'] ,FILTER_VALIDATE_BOOLEAN);
	$show_chapt = filter_var($args['show_chapt'] ,FILTER_VALIDATE_BOOLEAN);
	$show_verse = filter_var($args['show_verse'] ,FILTER_VALIDATE_BOOLEAN);
	$inline = filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN);
	$reference_before = filter_var($args['reference_before'],FILTER_VALIDATE_BOOLEAN);
	$reference_after = filter_var($args['reference_after'] ,FILTER_VALIDATE_BOOLEAN);
	/* The following code snippet is not yet supported and will be reviewed in a later revision
	if(empty($content) && isset($_GET['amdPassage'])){
		$content = $_GET['amdPassage'];
		$limit_type = (is_null($limit_type)) ? 'verses' : $limit_type;
		if($limit_type=='verses'){
			$limit = 500;
		} else {
			$limit = 12500;
		}
	}
	*/
	if(!empty($content)){
		$decoded_ref = amdbible_cx_reference_decode($content);
		if($decoded_ref[0]===false){
			return $content;
		}		
		$data = amdbible_get_passage($decoded_ref);
			if($data===false){
			//Data returned empty or false so there is no need
			// to continue and print empty passage.
			return false;
		}
		$passages = $data["complete"];
		$references = "";
		if(!get_option('amdbible_stand_cx_ref',1)){ //check for standardize-cx_ref option 
			$references = $content;
		} else {
			foreach($data["passages"] as $passage){
				if(!empty($references)){
					$references .= '; ';
				}
				$references .= amdbible_reference($passage["sv"],$passage["ev"]);
			}
		}
		if($reference_before){
			if($inline){
				$txt .= "<span class='amdbible_title'><em>".$references." KJV</em> &mdash; </span>";
			} else {
				$txt .= "<p class='amdbible_title'>".$references." KJV</p>";
			}
		}
		$txt .= amdbible_format_passage($passages,$limit,$limit_type,$show_book,$show_chapt,$show_verse,$inline);
		if($reference_after){
			if($inline){
				$txt .= " <span class='amdbible_title'> &mdash; <em>".$references." KJV</em></span>";
			} else {
				$txt .= "<p class='amdbible_title'>".$references." KJV</p>";
			}
		}
	} else {
		return;
	}
	return $txt;
}
add_shortcode( 'amd_bible', 'amdbible_shortcode_passage' );

function amdbible_randomn_passage( $atts ){
	$txt = "";
	global $books;
	global $wpdb;
	$args = shortcode_atts(
		array(
			'limit' => 0,
			'limit_type' => null,
			'show_book' => null,
			'show_chapt' => null,
			'show_verse' => null,
			//'inline' => true, //possibly change this if expanded to allow random chapter.
			'reference_before' => null,
			'reference_after' => null,
			'ot'=>null,
			'nt'=>null,
			'book'=>null,
			'chapter'=>null,
			//'show_type'=>null, //possibly change this if expanded to allow random chapter.
			'most_read'=>false,
			'essential'=>false,
			'start_para'=>false,
			'end_para'=>false,
		),
		$atts
	);
	$args['inline'] = true; //possibly change this if expanded to allow random chapter.
	if(is_null($args['limit_type'])){
		$args['limit_type'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? 'words' : 'verses';
	}
	if(is_null($args['show_book'])){
		$args['show_book'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['show_verse'])){
		$args['show_verse'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['show_chapt'])){
		$args['show_chapt'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['reference_before'])){
		$args['reference_before'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? false : true;
	}
	if(is_null($args['reference_after'])){
		$args['reference_after'] = (filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN)) ? true : false;
	}
	$limit = filter_var($args['limit'] ,FILTER_VALIDATE_INT);
	$limit_type = esc_attr($args['limit_type']);
	if($limit_type!='words'&&$limit_type!='verses'){
		$limit_type = null;
	}
	$show_book = filter_var($args['show_book'] ,FILTER_VALIDATE_BOOLEAN);
	$show_chapt = filter_var($args['show_chapt'] ,FILTER_VALIDATE_BOOLEAN);
	$show_verse = filter_var($args['show_verse'] ,FILTER_VALIDATE_BOOLEAN);
	$inline = filter_var($args['inline'] ,FILTER_VALIDATE_BOOLEAN);
	$reference_before = filter_var($args['reference_before'],FILTER_VALIDATE_BOOLEAN);
	$reference_after = filter_var($args['reference_after'] ,FILTER_VALIDATE_BOOLEAN);
	$most_read = filter_var($args['most_read'] ,FILTER_VALIDATE_BOOLEAN);
	$essential = filter_var($args['essential'] ,FILTER_VALIDATE_BOOLEAN);
	$start_para = filter_var($args['start_para'] ,FILTER_VALIDATE_BOOLEAN);
	$end_para = filter_var($args['end_para'] ,FILTER_VALIDATE_BOOLEAN);
	
	
	$most_read_list = array(
		array('43003016','43003016'),
		array('24029011','24029011'),
		array('45008028','45008028'),
		array('50004013','50004013'),
		array('01001001','01001001'),
		array('20003005','20003005'),
		array('20003006','20003006'),
		array('45012002','45012002'),
		array('50004006','50004006'),
		array('40028019','40028019'),
		array('49002008','49002008'),
		array('48005022','48005022'),
		array('45012001','45012001'),
		array('43010010','43010010'),
		array('44018010','44018010'),
		array('44018009','44018009'),
		array('44018011','44018011'),
		array('48002020','48002020'),
		array('62001009','62001009'),
		array('45003023','45003023'),
		array('43014006','43014006'),
		array('40028020','40028020'),
		array('45005008','45005008'),
		array('50004008','50004008'),
		array('50004007','50004007'),
		array('07001009','07001009'),
		array('23040031','23040031'),
		array('49002009','49002009'),
		array('45006023','45006023'),
		array('48005023','48005023'),
		array('23053005','23053005'),
		array('60003015','60003015'),
		array('55003016','55003016'),
		array('40006033','40006033'),
		array('58012002','58012002'),
		array('60005007','60005007'),
		array('49002010','49002010'),
		array('46010013','46010013'),
		array('40011028','40011028'),
		array('58011001','58011001'),
		array('47005017','47005017'),
		array('58013005','58013005'),
		array('47012009','47012009'),
		array('45010009','45010009'),
		array('23041010','23041010'),
		array('01001026','01001026'),
		array('40011029','40011029'),
		array('43016033','43016033'),
		array('44001008','44001008'),
		array('55001007','55001007'),
		array('23053004','23053004'),
		array('47005021','47005021'),
		array('45015013','45015013'),
		array('43011025','43011025'),
		array('58011006','58011006'),
		array('43005024','43005024'),
		array('59001002','59001002'),
		array('23053006','23053006'),
		array('44002038','44002038'),
		array('49003020','49003020'),
		array('40011030','40011030'),
		array('01001027','01001027'),
		array('51003012','51003012'),
		array('58012001','58012001'),
		array('59005016','59005016'),
		array('44017011','44017011'),
		array('50004019','50004019'),
		array('43001001','43001001'),
		array('46006019','46006019'),
		array('62003016','62003016'),
		array('19133001','19133001'),
		array('43014027','43014027'),
		array('58004012','58004012'),
		array('43015013','43015013'),
		array('33006008','33006008'),
		array('45010017','45010017'),
		array('43001012','43001012'),
		array('59001012','59001012'),
		array('59001003','59001003'),
		array('45008038','45008038'),
		array('45008039','45008039'),
		array('58010025','58010025'),
		array('61001004','61001004'),
		array('50001006','50001006'),
		array('19133003','19133003'),
		array('58004016','58004016'),
		array('19037004','19037004'),
		array('43003017','43003017'),
		array('44004012','44004012'),
		array('23026003','23026003'),
		array('60002024','60002024'),
		array('06001008','06001008'),
		array('40028018','40028018'),
		array('51003023','51003023'),
		array('40022037','40022037'),
		array('19133002','19133002'),
		array('40005016','40005016'),
		array('23055008','23055008'),
		array('58004015','58004015'),
		array('43013035','43013035'),
	);
	$essential_list = array(
		array('46006019','46006020'),
		array('48002020','48002020'),
		array('60002002','60002002'),
		array('43014013','43014013'),
		array('62003022','62003022'),
		array('41016015','41016015'),
		array('45003010','45003010'),
		array('45005008','45005008'),
		array('45003023','45003023'),
		array('45006023','45006023'),
		array('45010009','45010010'),
		array('45010013','45010013'),
		array('43003036','43003036'),
		array('58013005','58013005'),
		array('43010028','43010028'),
		array('62005011','62005013'),
		array('45006003','45006005'),
		array('43001012','43001012'),
		array('44016031','44016031'),
		array('43003016','43003016'),
		array('06001008','06001008'),
		array('55002015','55002015'),
		array('19119011','19119011'),
		array('40005018','40005018'),
		array('55003016','55003017'),
		array('23053006','23053006'),
		array('56002014','56002014'),
		array('47005021','47005021'),
		array('45006011','45006013'),
		array('45012001','45012002'),
		array('42009023','42009023'),
		array('20017017','20017017'),
		array('20018024','20018024'),
		array('19119063','19119063'),
		array('19103012','19103012'),
		array('62001009','62001009'),
		array('49001007','49001007'),
		array('20028013','20028013'),
		array('19119133','19119133'),
		array('20003005','20003006'),
		array('66020015','66020015'),
		array('53001008','53001009'),
		array('20022006','20022006'),
		array('49005022','49005025'),
		array('46007003','46007003'),
		array('45003020','45003020'),
		array('59002010','59002010'),
		array('48003024','48003024'),
		array('44002041','44002041'),
		array('58010025','58010025'),
		array('46011026','46011026'),
		array('23043011','23043011'),
		array('44004012','44004012'),
		array('43014006','43014006'),
		array('47012015','47012015'),
		array('50002001','50002004'),
		array('23026003','23026003'),
		array('50004006','50004007'),
		array('46010031','46010031'),
		array('51003023','51003023'),
		array('51003017','51003017'),
		array('40005023','40005024'),
		array('40018015','40018015'),
		array('44024016','44024016'),
		array('20027001','20027001'),
		array('47006002','47006002'),
		array('20029001','20029001'),
		array('49002008','49002009'),
		array('56003005','56003005'),
		array('55001009','55001009'),
		array('52004016','52004018'),
		array('56002011','56002013'),
		array('46010013','46010013'),
		array('59004007','59004007'),
		array('53003003','53003003'),
		array('40004019','40004019'),
		array('40028019','40028020'),
	);
	if($most_read && $essential){
		$list = array_merge($essential_list,$most_read_list);
		$rand_key = array_rand($list);
		$sv_ev = $list[$rand_key];
		$sv = $sv_ev[0];
		$ev = $sv_ev[1];
		$reference = amdbible_reference($sv,$ev);
		$data  = array("complete"=>array(),"passages"=>array());
	} else if($most_read){
		$rand_key = array_rand($most_read_list);
		$sv_ev = $most_read_list[$rand_key];
		$sv = $sv_ev[0];
		$ev = $sv_ev[1];
		$reference = amdbible_reference($sv,$ev);
		$data  = array("complete"=>array(),"passages"=>array());
	} else if($essential){
		$rand_key = array_rand($essential_list);
		$sv_ev = $essential_list[$rand_key];
		$sv = $sv_ev[0];
		$ev = $sv_ev[1];
		$reference = amdbible_reference($sv,$ev);
		$data  = array("complete"=>array(),"passages"=>array());
	} else {
		$book = $args['book'];
		$chapter = 0;
		if(!is_null($args['book'])){
			if(is_numeric($book) && $book>=1 && $book<=66){
				$book = str_pad(filter_var($book ,FILTER_VALIDATE_INT),2,'0',STR_PAD_LEFT);
			} else if(isset($books[$book])){
				$book = str_pad($books[$book],2,'0',STR_PAD_LEFT);
			} else {
				$book = null;
			}
			
		}
		/* 
		$show_type = esc_attr($args['show_type']);
		if($show_type!='chapter'){
			$show_type = 'verse';
		}
		*/ //possibly change this if expanded to allow random chapter.
		if(is_null($book)){
			$OT = filter_var($args['ot'] ,FILTER_VALIDATE_BOOLEAN);
			$NT = filter_var($args['nt'] ,FILTER_VALIDATE_BOOLEAN);
			if(($OT && $NT) || (!$OT && !$NT)){
				// ALL
				$filter = "BETWEEN 01001001 AND 66999999";
			} else if($OT){
				// O.T.
				$filter = "BETWEEN 01001001 AND 39999999";
			} else {
				// N.T.
				$filter = "BETWEEN 40001001 AND 66999999";
			}
		} else {
			if(!is_null($args['chapter']) && is_numeric($args['chapter'])){
				$chapter = str_pad(filter_var($args['chapter'] ,FILTER_VALIDATE_INT),3,'0',STR_PAD_LEFT);
				$filter = "BETWEEN ".$book.$chapter."001 AND ".$book.$chapter."999";
			} else {
				$filter = "BETWEEN ".$book."001001 AND ".$book."999999";
			}
		}
		$query = "
			SELECT id
			FROM {$wpdb->base_prefix}amdbible_kjv
			WHERE id $filter
			ORDER BY RAND()
			LIMIT 1
		";
		$sv = $wpdb->get_var($query);
		$reference = amdbible_reference($sv);
		$data  = array("complete"=>array(),"passages"=>array());	
		$ev = $sv;
	}
	$passage = amdbible_passage($sv,$ev);
	$data["passages"][] = array(
		"sv"=>$sv,
		"ev"=>$ev,
		"passage"=>$passage
	);
	$data["complete"] = $passage;
	$passages = $data["complete"];
	$references = "";
	foreach($data["passages"] as $passage){
		if(!empty($references)){
			$references .= '; ';
		}
		$references .= amdbible_reference($passage["sv"],$passage["ev"]);
	}
	if($reference_before){
		if($inline){
			$txt .= "<span class='amdbible_title'><em>".$references." KJV</em> &mdash; </span>";
			if($start_para){
				$txt .= "<p>";
			}
		} else {
			$txt .= "<p class='amdbible_title'>".$references." KJV</p>";
		}
	}
	$txt .= amdbible_format_passage($passages,$limit,$limit_type,$show_book,$show_chapt,$show_verse,$inline);
	if($reference_after){
		if($inline){
			$txt .= " <span class='amdbible_title'> &mdash; <em>".$references." KJV</em></span>";
			if($end_para){
				$txt .= "</p>";
			}
		} else {
			$txt .= "<p class='amdbible_title'>".$references." KJV</p>";
		}
	}
	return $txt;
	
	
}
add_shortcode( 'amd_bible_rand', 'amdbible_randomn_passage' );

add_action( 'widgets_init', function(){
     register_widget( 'amdbible_widget' );
});	
/**
 * Adds My_Widget widget.
 */
class amdbible_widget extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'amdbible_widget', // Base ID
			__('Daily Bible Snippet', 'text_domain'), // Name
			array( 'description' => __( 'Widget to Display Snippet of Daily Bible Reading', 'text_domain' ), ) // Args
		);
	}
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		//get the current daily passage
		$day = date_i18n('z',time())+1;
		if(isset($instance['plan'])){
			$plan = $instance['plan'];
		} else {
			$plan = amdbible_get_plan();
		}
		$passages = amdbible_daily_passage($day,$plan);
		$references = "";
		if(!get_option('amdbible_stand_cx_ref',1) && isset($plan['cx']) && $plan['cx']){ //check for standardize-cx_ref option 
			$references = amdbible_cx_reference($day,$plan);
		} else {
			foreach($passages["passages"] as $passage){
				$references .= amdbible_reference($passage["sv"],$passage["ev"])." ";
			}
		}
		if(isset($instance['ref_title'])){
			$ref_title = $instance['ref_title'];
		} else {
			$ref_title = '0';
		}
		if($ref_title=='1'){
			$instance['title'] = $references;
		}
		//start outputting content
     	echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		//check and get variables
		if(isset($instance['limit'])){
			$limit = $instance['limit'];
		} else {
			$limit = 50;
		}
		if(isset($instance['limit_type'])){
			$limit_type = $instance['limit_type'];
		} else {
			$limit_type = 'words';
		}
		if(isset($instance['read_more_text'])){
			$read_more_text = $instance['read_more_text'];
		} else {
			$read_more_text = __( 'Read More', 'text_domain' );
		}
		if(isset($instance['ref_start']) && $instance['ref_start']=='1'){
			echo "<strong>".$references.":</strong> ";
		}
		echo amdbible_format_passage($passages["complete"],$limit,$limit_type,false,false,false);
		if(isset($instance['full_page'])){
			echo '<a href="'.$instance['full_page'].'" >',$read_more_text,'</a>';
		}
		echo $args['after_widget'];
	}
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		// Check values
		if(isset($instance['plan'])){
			$plan = $instance['plan'];
		} else {
			$plan = amdbible_get_plan();
		}
		if(isset($instance['ref_start'])){
			$ref_start = esc_attr($instance['ref_start']);
		} else {
			$ref_start = '0';
		}
		if(isset($instance['ref_title'])){
			$ref_title = esc_attr($instance['ref_title']);
		} else {
			$ref_title = '0';
		}
		if(isset($instance['title'])){
			$title = esc_attr($instance['title']);
		} else {
			$title = __( 'New title', 'text_domain' );
		}
		if(isset($instance['limit_type'])){
			$limit_type = esc_attr($instance['limit_type']);
		} else {
			$limit_type = 'words';
		}
		if(isset($instance['limit'])){
			$limit = esc_attr($instance['limit']);
		} else {
			$limit = __( '50', 'text_domain' );
		}
		if(isset($instance['full_page'])){
			$full_page = esc_attr($instance['full_page']);
		} else {
			$full_page = '';
		}
		if(isset($instance['read_more_text'])){
			$read_more_text = esc_attr($instance['read_more_text']);
		} else {
			$read_more_text = __( 'Continue Reading', 'text_domain' );
		}
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('plan'); ?>"><?php _e('Select Plan:', 'wp_widget_plugin'); ?></label>
			<select name="<?php echo $this->get_field_name('plan'); ?>" id="<?php echo $this->get_field_id('plan'); ?>" class="widefat">
				<?php
					global $wpdb;
					$plans = $wpdb->get_results( "SELECT id,cx,n,d FROM {$wpdb->base_prefix}amdbible_plans_info" );
					foreach ($plans as $option) {
						echo '<option value="' . $option->id . '" id="' . $option->id . '"', $plan == $option->id ? ' selected="selected"' : '', 'title="', $option->d, '"', '>', $option->n, '</option>';
					}
				?>
			</select>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('ref_start'); ?>" name="<?php echo $this->get_field_name('ref_start'); ?>" type="checkbox" value="1" <?php checked( '1', $ref_start ); ?> />
			<label for="<?php echo $this->get_field_id('ref_start'); ?>"><?php _e('Scripture starts with Reference inline?', 'wp_widget_plugin'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('ref_title'); ?>" name="<?php echo $this->get_field_name('ref_title'); ?>" type="checkbox" value="1" <?php checked( '1', $ref_title ); ?> />
			<label for="<?php echo $this->get_field_id('ref_title'); ?>"><?php _e('Use Reference for Title?', 'wp_widget_plugin'); ?></label>
		</p>
		<p>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit_type'); ?>"><?php _e('Limit Type:', 'wp_widget_plugin'); ?></label>
			<select name="<?php echo $this->get_field_name('limit_type'); ?>" id="<?php echo $this->get_field_id('limit_type'); ?>" class="widefat">
				<?php
					$options = array('words', 'verses');
					foreach ($options as $option) {
						echo '<option value="' . $option . '" id="' . $option . '"', $limit_type == $option ? ' selected="selected"' : '', '>', $option, '</option>';
					}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit:', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo $limit; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('full_page'); ?>"><?php _e('Full Reading Page:', 'wp_widget_plugin'); ?></label>
			<select name="<?php echo $this->get_field_name('full_page'); ?>" id="<?php echo $this->get_field_id('full_page'); ?>" class="widefat">
				<option value="" ></option>
				<?php
					$pages = get_pages();
					foreach ($pages as $page) {
						echo '<option value="' . get_page_link($page->ID) . '" id="' . $page->ID . '"', $full_page == get_page_link($page->ID) ? ' selected="selected"' : '', '>', $page->post_title, '</option>';

					}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('read_more_text'); ?>"><?php _e('Read More Text:', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('read_more_text'); ?>" name="<?php echo $this->get_field_name('read_more_text'); ?>" type="text" value="<?php echo $read_more_text; ?>" />
		</p>
		<?php 
	}
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['plan'] = ( ! empty( $new_instance['plan'] ) ) ? amdbible_get_plan( strip_tags( $new_instance['plan'] ) ) : amdbible_get_plan();
		$instance['ref_start'] = ( ! empty( $new_instance['ref_start'] ) ) ? strip_tags( $new_instance['ref_start'] ) : '0';
		$instance['ref_title'] = ( ! empty( $new_instance['ref_title'] ) ) ? strip_tags( $new_instance['ref_title'] ) : '0';
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['limit_type'] = ( ! empty( $new_instance['limit_type'] ) ) ? strip_tags( $new_instance['limit_type'] ) : 'words';
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '50';
		$instance['full_page'] = ( ! empty( $new_instance['full_page'] ) ) ? strip_tags( $new_instance['full_page'] ) : '';
		$instance['read_more_text'] = ( ! empty( $new_instance['read_more_text'] ) ) ? strip_tags( $new_instance['read_more_text'] ) : __( 'Continue Reading', 'text_domain' );
		return $instance;
	}
} // class My_Widget

add_action('activated_plugin','save_error');
function save_error(){
    update_option('plugin_error',  ob_get_contents());
}


?>