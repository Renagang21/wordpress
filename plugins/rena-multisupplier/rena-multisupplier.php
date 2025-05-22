<?php
/**
 * Plugin Name: Rena Multisupplier
 * Plugin URI: https://rena-retail.com
 * Description: 다중 공급업체 관리 플러그인 (WooCommerce REST API 연동)
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Rena Retail
 * Author URI: https://rena-retail.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: rena-multisupplier
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin version
define('RENA_MULTISUPPLIER_VERSION', '1.0.0');

// Plugin directory paths
define('RENA_MULTISUPPLIER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RENA_MULTISUPPLIER_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The core plugin class loader
 */
require_once RENA_MULTISUPPLIER_PLUGIN_DIR . 'includes/class-rena-multisupplier-loader.php';

/**
 * Begins execution of the plugin.
 */
function run_rena_multisupplier() {
    $plugin = new Rena_Multisupplier_Loader();
    $plugin->run();
}

// Hook for plugin activation
register_activation_hook(__FILE__, function() {
    require_once RENA_MULTISUPPLIER_PLUGIN_DIR . 'includes/class-rena-multisupplier-activator.php';
    Rena_Multisupplier_Activator::activate();
});

// Hook for plugin deactivation
register_deactivation_hook(__FILE__, function() {
    require_once RENA_MULTISUPPLIER_PLUGIN_DIR . 'includes/class-rena-multisupplier-deactivator.php';
    Rena_Multisupplier_Deactivator::deactivate();
});

// Start the plugin
run_rena_multisupplier(); 