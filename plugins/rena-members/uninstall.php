
<?php
// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Define constants
define('RENA_MEMBERS_PATH', plugin_dir_path(__FILE__));

// Clean up options
$options = array(
    'rena_members_settings',
    'rena_members_version'
);

foreach ($options as $option) {
    delete_option($option);
}

// Remove user meta
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'rena_members_%'");

// Drop custom tables if any
// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}rena_members_table");

// Clear any cached data that has been removed
wp_cache_flush();