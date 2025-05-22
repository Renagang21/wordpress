<?php
/**
 * Plugin Name: Rena Multistore
 * Plugin URI: https://rena-retail.com
 * Description: Rena Retail Multistore Management Plugin
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Rena Retail
 * Author URI: https://rena-retail.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: rena-multistore
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin version
define('RENA_MULTISTORE_VERSION', '1.0.0');

// Plugin directory paths
define('RENA_MULTISTORE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RENA_MULTISTORE_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The core plugin class loader
 */
require_once RENA_MULTISTORE_PLUGIN_DIR . 'includes/class-rena-multistore-loader.php';

/**
 * Begins execution of the plugin.
 */
function run_rena_multistore() {
    $plugin = new Rena_Multistore_Loader();
    $plugin->run();
}

// Hook for plugin activation
register_activation_hook(__FILE__, function() {
    require_once RENA_MULTISTORE_PLUGIN_DIR . 'includes/class-rena-multistore-activator.php';
    Rena_Multistore_Activator::activate();
});

// Hook for plugin deactivation
register_deactivation_hook(__FILE__, function() {
    require_once RENA_MULTISTORE_PLUGIN_DIR . 'includes/class-rena-multistore-deactivator.php';
    Rena_Multistore_Deactivator::deactivate();
});

// Start the plugin
run_rena_multistore(); 