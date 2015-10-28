<?php // This code runs whenever in the wp-admin.

/********** MISC **********/

// Needed for retina icons in menu.
add_action( 'admin_enqueue_scripts', create_function( '',
	"wp_enqueue_style( 'pb_backupbuddy-wp-admin', '" . pb_backupbuddy::plugin_url() . "/css/wp-admin.css', array(), pb_backupbuddy::settings( 'version' ) );"
	)
);
global $wp_version;
if ( $wp_version >= 3.8 ) {
	add_action( 'admin_enqueue_scripts', create_function( '',
		"wp_enqueue_style( 'pb_backupbuddy-wp-admin-fonticon', '" . pb_backupbuddy::plugin_url() . "/css/wp-admin-fonticon.css', array(), pb_backupbuddy::settings( 'version' ) );"
		)
	);
}


pb_backupbuddy::load();



// Load backupbuddy class with helper functions.
if ( ! class_exists( 'backupbuddy_core' ) ) {
	require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
}



/* BEGIN HANDLING DATA STRUCTURE UPGRADE */

// 2.x -> 3.x
$options = get_site_option( 'pluginbuddy_backupbuddy' ); // Attempt to get 2.x options.
if ( is_multisite() ) { // Try to read site-specific settings in.
	$multisite_option = get_option( 'pluginbuddy_backupbuddy' );
	if ( $multisite_option ) {
		$options = $multisite_option;
	}
	unset( $multisite_option );
}



if ( $options !== false ) { // If options is not false then we need to upgrade.
	pb_backupbuddy::status( 'details', 'Migrating data structure. 2.x data discovered.' );
	backupbuddy_core::verify_directories();
	require_once( pb_backupbuddy::plugin_path() . '/controllers/activation.php' );
}
unset( $options );

// Check if data version is behind & run activation upgrades if needed.
$default_options = pb_backupbuddy::settings( 'default_options' );
if ( pb_backupbuddy::$options['data_version'] < $default_options['data_version'] ) {
	backupbuddy_core::verify_directories();
	pb_backupbuddy::status( 'details', 'Data structure version of `' . pb_backupbuddy::$options['data_version'] . '` behind current version of `' . $default_options['data_version'] . '`. Running activation upgrade.' );
	require_once( pb_backupbuddy::plugin_path() . '/controllers/activation.php' );
}

/* END HANDLING DATA STRUCTURE UPGRADE */



// Schedule daily housekeeping.
if ( false === wp_next_scheduled( pb_backupbuddy::cron_tag( 'housekeeping' ) ) ) { // if schedule does not exist...
	backupbuddy_core::schedule_event( time() + ( 60*60 * 2 ), 'daily', pb_backupbuddy::cron_tag( 'housekeeping' ), array() ); // Add schedule.
}











/********** ACTIONS (admin) **********/



// Set up reminders if enabled.
if ( pb_backupbuddy::$options['backup_reminders'] == '1' ) {
	pb_backupbuddy::add_action( array( 'load-update-core.php', 'wp_update_backup_reminder' ) );
	pb_backupbuddy::add_action( array( 'post_updated_messages', 'content_editor_backup_reminder_on_update' ) );
}

// Display warning to network activate if running in normal mode on a MultiSite Network.
if ( is_multisite() && !backupbuddy_core::is_network_activated() ) {
	pb_backupbuddy::add_action( array( 'all_admin_notices', 'multisite_network_warning' ) ); // BB should be network activated while on Multisite.
}



/********** AJAX (admin) **********/

// Backup process.
pb_backupbuddy::add_ajax( 'backup_status' ); // AJAX querying of backup status for manual backups.
pb_backupbuddy::add_ajax( 'stop_backup' ); // Button to stop backup.

// Migrate process.
pb_backupbuddy::add_ajax( 'migration_picker' ); // Remote destination picker.
pb_backupbuddy::add_ajax( 'migrate_status' ); // Magic migration status polling.

// Remote destinations system.
pb_backupbuddy::add_ajax( 'remote_test' ); // Remote destination testing.
pb_backupbuddy::add_ajax( 'remote_save' ); // Remote destination saving.
pb_backupbuddy::add_ajax( 'remote_send' ); // Remote destination picker.
pb_backupbuddy::add_ajax( 'remote_delete' ); // Remote destination deletion.
pb_backupbuddy::add_ajax( 'destination_picker' ); // Remote destination picker.

// Server Info Page.
pb_backupbuddy::add_ajax( 'db_check' ); // Check db integrity of a table.
pb_backupbuddy::add_ajax( 'db_repair' ); // Repair db integrity of a table.
pb_backupbuddy::add_ajax( 'refresh_zip_methods' ); // Server info page available zip methods update.
pb_backupbuddy::add_ajax( 'refresh_site_size' ); // Server info page site size update.
pb_backupbuddy::add_ajax( 'refresh_site_size_excluded' ); // Server info page site size (sans exclusions) update.
pb_backupbuddy::add_ajax( 'refresh_site_objects' ); // Server info page site objects file count update.
pb_backupbuddy::add_ajax( 'refresh_site_objects_excluded' ); // Server info page site objects file count (sans exclusions) update.
pb_backupbuddy::add_ajax( 'refresh_database_size' ); // Server info page database size update.
pb_backupbuddy::add_ajax( 'refresh_database_size_excluded' ); // Server info page site size (sans exclusions) update.
pb_backupbuddy::add_ajax( 'phpinfo' ); // Server info page extended PHPinfo thickbox.
pb_backupbuddy::add_ajax( 'icicle' ); // Server info page icicle for GUI file listing.
pb_backupbuddy::add_ajax( 'php_max_runtime_test' ); // Tests ACTUAL PHP maximum runtime.
pb_backupbuddy::add_ajax( 'site_size_listing' ); // Display site size listing in table.

// MISC.
pb_backupbuddy::add_ajax( 'exclude_tree' ); // Directory exclusions picker for settings page.
pb_backupbuddy::add_ajax( 'download_archive' ); // Directory exclusions picker for settings page.
pb_backupbuddy::add_ajax( 'set_backup_note' ); // Used for setting a note on a backup archive in the backup listing.
pb_backupbuddy::add_ajax( 'integrity_status' ); // Display backup integrity status.
pb_backupbuddy::add_ajax( 'view_status_log' ); // Display status log in thickbox for recent backups section.
pb_backupbuddy::add_ajax( 'importbuddy' ); // ImportBuddy download link.
pb_backupbuddy::add_ajax( 'repairbuddy' ); // RepairBuddy download link.
pb_backupbuddy::add_ajax( 'hash' ); // Obtain MD5 hash of a backup file.
pb_backupbuddy::add_ajax( 'ajax_controller_callback_function' ); // Tell WordPress about this AJAX callback.
pb_backupbuddy::add_ajax( 'disalert' ); // Dismissable alert saving. Currently framework does NOT auto-load this AJAX ability to save disalerts.
pb_backupbuddy::add_ajax( 'importexport_settings' ); // Popup thickbox for importing and exporting settings.
pb_backupbuddy::add_ajax( 'file_tree' ); // Display file listing of zip.
pb_backupbuddy::add_ajax( 'restore_file_view' ); // File viewer (view content only) in the file restore page.
pb_backupbuddy::add_ajax( 'restore_file_restore' ); // File restorer (actual unzip/restore) in the file restore page.
//pb_backupbuddy::add_ajax( 'quickstart_stash_test' ); // Getting Started Quick Start Stash auth testing.
pb_backupbuddy::add_ajax( 'quickstart_skip' ); // Quick Start form saving.
pb_backupbuddy::add_ajax( 'quickstart_form' ); // Quick Start form saving.
pb_backupbuddy::add_ajax( 'quickstart' ); // Quick Start form modal.
pb_backupbuddy::add_ajax( 'profile_settings' ); // Settings page backup profile editing.
pb_backupbuddy::add_ajax( 'email_error_test' ); // Test email error notification.
pb_backupbuddy::add_ajax( 'remotesend_details' ); // Display backup integrity status.
pb_backupbuddy::add_ajax( 'remotesend_abort' ); // Abort an in-process remote destination send.
pb_backupbuddy::add_ajax( 'destination_ftp_pathpicker' ); // FTP destination path picker.

/********** DASHBOARD (admin) **********/



// Display stats in Dashboard.
//if ( pb_backupbuddy::$options['dashboard_stats'] == '1' ) {
	if ( ( !is_multisite() ) || ( is_multisite() && is_network_admin() ) ) { // Only show if standalon OR in main network admin.
		pb_backupbuddy::add_dashboard_widget( 'stats', 'BackupBuddy', 'godmode' );
	}
//}

/********** FILTERS (admin) **********/
pb_backupbuddy::add_filter( 'plugin_row_meta', 10, 2 );


/********** PAGES (admin) **********/

$icon = '';

if ( is_multisite() && backupbuddy_core::is_network_activated() && !defined( 'PB_DEMO_MODE' ) ) { // Multisite installation.
	if ( defined( 'PB_BACKUPBUDDY_MULTISITE_EXPERIMENT' ) && ( PB_BACKUPBUDDY_MULTISITE_EXPERIMENT == TRUE ) ) { // comparing with bool but loose so string is acceptable.
		
		if ( is_network_admin() ) { // Network Admin pages
			pb_backupbuddy::add_page( '', 'backup', array( pb_backupbuddy::settings( 'name' ), __( 'Backup', 'it-l10n-backupbuddy' ) ), 'manage_network', $icon );
			pb_backupbuddy::add_page( 'backup', 'migrate_restore', __( 'Migrate, Restore', 'it-l10n-backupbuddy' ), 'manage_network' );
			pb_backupbuddy::add_page( 'backup', 'destinations', __( 'Remote Destinations', 'it-l10n-backupbuddy' ), pb_backupbuddy::$options['role_access'] );
			pb_backupbuddy::add_page( 'backup', 'multisite_import', __( 'MS Import (beta)', 'it-l10n-backupbuddy' ), 'manage_network' );
			pb_backupbuddy::add_page( 'backup', 'server_tools', __( 'Server Tools', 'it-l10n-backupbuddy' ), 'manage_network' );
			pb_backupbuddy::add_page( 'backup', 'malware_scan', __( 'Malware Scan', 'it-l10n-backupbuddy' ), 'manage_network' );
			pb_backupbuddy::add_page( 'backup', 'scheduling', __( 'Schedules', 'it-l10n-backupbuddy' ), 'manage_network' );
			pb_backupbuddy::add_page( 'backup', 'settings', __( 'Settings', 'it-l10n-backupbuddy' ), 'manage_network' );
		} else { // Subsite pages.
			// TODO: Make the following work so the network admin ALWAYS can export even if admin exports are not enabled. Problem: current_user_can() is not available this early. Not sure best fix yet.
			//if ( current_user_can( 'manage_network' ) || ( ( current_user_can( 'activate_plugins' ) ) && ( pb_backupbuddy::$options[ 'multisite_export' ] == '1' ) ) ) { // Add export menus if: is network admin _OR_ ( is an admin AND exporting is enabled ).
			
			$export_note = '';
			
			$options = get_site_option( 'pb_' . pb_backupbuddy::settings( 'slug' ) );
			$multisite_export = $options[ 'multisite_export' ];
			unset( $options );

			if ( $multisite_export == '1' ) { // Settings enable admins to export. Set capability to admin and higher only.
				$capability = pb_backupbuddy::$options['role_access'];
				$export_title = '<span title="Note: Enabled for both subsite Admins and Network Superadmins based on BackupBuddy settings">' . __( 'MS Export (experimental)', 'it-l10n-backupbuddy' ) . '</span>';
			} else { // Settings do NOT allow admins to export; set capability for superadmins only.
				$capability = 'manage_network';
				$export_title = '<span title="Note: Enabled for Network Superadmins only based on BackupBuddy settings">' . __( 'MS Export SA (experimental)', 'it-l10n-backupbuddy' ) . '</span>';
			}
			
			//pb_backupbuddy::add_page( '', 'getting_started', array( pb_backupbuddy::settings( 'name' ), 'Getting Started' . $export_note ), $capability );
			pb_backupbuddy::add_page( '', 'multisite_export', $export_title, $capability, $icon );
			pb_backupbuddy::add_page( 'multisite_export', 'malware_scan', __( 'Malware Scan', 'it-l10n-backupbuddy' ), $capability );
		}
		
	} else { // PB_BACKUPBUDDY_MULTISITE_EXPERIMENT not in wp-config / set to TRUE.
		pb_backupbuddy::status( 'error', 'Multisite detected but PB_BACKUPBUDDY_MULTISITE_EXPERIMENT definition not found in wp-config.php / not defined to boolean TRUE.' );
	}
} else { // Standalone site.
	pb_backupbuddy::add_page( '', 'backup', array( pb_backupbuddy::settings( 'name' ), __( 'Backup', 'it-l10n-backupbuddy' ) ), pb_backupbuddy::$options['role_access'], $icon );
	pb_backupbuddy::add_page( 'backup', 'migrate_restore', __( 'Restore / Migrate', 'it-l10n-backupbuddy' ), pb_backupbuddy::$options['role_access'] );
	pb_backupbuddy::add_page( 'backup', 'destinations', __( 'Remote Destinations', 'it-l10n-backupbuddy' ), pb_backupbuddy::$options['role_access'] );
	pb_backupbuddy::add_page( 'backup', 'server_tools', __( 'Server Tools', 'it-l10n-backupbuddy' ), pb_backupbuddy::$options['role_access'] );
	pb_backupbuddy::add_page( 'backup', 'malware_scan', __( 'Malware Scan', 'it-l10n-backupbuddy' ), pb_backupbuddy::$options['role_access'] );
	pb_backupbuddy::add_page( 'backup', 'scheduling', __( 'Schedules', 'it-l10n-backupbuddy' ), pb_backupbuddy::$options['role_access'] );
	pb_backupbuddy::add_page( 'backup', 'settings', __( 'Settings', 'it-l10n-backupbuddy' ), pb_backupbuddy::$options['role_access'] );
}


/********** LIBRARIES & CLASSES (admin) **********/



/********** OTHER (admin) **********/
add_filter( 'contextual_help', 'pb_backupbuddy_contextual_help', 10, 3 );
function pb_backupbuddy_contextual_help( $contextual_help, $screen_id, $screen ) { // Loads help from file in controllers/help/:PAGENAME:.php
	
	// WordPress pre-v3.3 so no contextual help.
	if ( ! method_exists( $screen, 'add_help_tab' ) ) {
		return $contextual_help;
	}
	
	// Not a backupbuddy page.
	if ( false === stristr( $screen_id, 'backupbuddy' ) ) {
		return $contextual_help;
	}
	
	// Load page-specific help.
	$page = str_replace( 'pb_backupbuddy_', '', str_replace( 'toplevel_page_', '', str_replace( 'backupbuddy_page_pb_backupbuddy_', '', $screen_id ) ) );
	$helpFile = dirname( __FILE__ ) . '/controllers/help/' . $page . '.php';
	if ( file_exists( $helpFile ) ) {
		include( $helpFile );
	}
	
	// Global help.
	$screen->add_help_tab(
	array(
	'id'      => 'pb_backupbuddy_additionalhelp',
	'title'   => __( 'Tutorials & Support', 'it-l10n-backupbuddy' ),
	'content' => '<p>
					<a href="http://ithemes.com/publishing/getting-started-with-backupbuddy/" target="_new">' . __( 'Getting Started eBook', 'it-l10n-backupbuddy' ) . '</a>
					<br>
					<a href="http://ithemes.tv/category/backupbuddy/" target="_new">' . __( 'Getting Started Videos', 'it-l10n-backupbuddy' ) . '</a>
					<br>
					<a href="http://ithemes.com/codex/" target="_new">' . __( 'Knowledge Base & Tutorials', 'it-l10n-backupbuddy' ) . '</a>
					<br>
					<a href="http://ithemes.com/support/" target="_new"><b>' . __( 'Support Forum', 'it-l10n-backupbuddy' ) . '</b></a>
				</p>',
	));
	
	return $contextual_help;
	
} // End pb_backupbuddy_contextual_help().


?>