<?php
/**
 * Plugin Name: Rena Block
 * Description: Copy and QR Code blocks for Gutenberg
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: rena-block
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('RENA_BLOCK_PATH', plugin_dir_path(__FILE__));
define('RENA_BLOCK_URL', plugin_dir_url(__FILE__));
define('RENA_BLOCK_VERSION', '1.0.0');

// Load functions
require_once RENA_BLOCK_PATH . 'includes/functions.php';

// 프론트엔드 스크립트 등록
// Define plugin constants
define('RENA_BLOCK_PATH', plugin_dir_path(__FILE__));
define('RENA_BLOCK_URL', plugin_dir_url(__FILE__));
define('RENA_BLOCK_VERSION', '1.0.0');

// Load functions
require_once RENA_BLOCK_PATH . 'includes/functions.php';

// 프론트엔드 스크립트 등록
function enqueue_copy_clipboard_script() {
    wp_enqueue_script(
        'rena-copy-clipboard',
        plugins_url('blocks/copy-to-clipboard/frontend.js', __FILE__),
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_copy_clipboard_script');

add_action('wp_enqueue_scripts', 'enqueue_copy_clipboard_script');
class Rena_Block_Plugin {
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
    }

    public function init() {
        $this->register_blocks();
        load_plugin_textdomain('rena-block', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    private function register_blocks() {
        // Register copy block
        register_block_type(RENA_BLOCK_PATH . 'blocks/copy-block');
        
        // Register QR block
        register_block_type(RENA_BLOCK_PATH . 'blocks/qr-block');
    }

    public function enqueue_editor_assets() {
        // QR Code library
        wp_enqueue_script(
            'qrcode-lib',
            RENA_BLOCK_URL . 'assets/js/qrcode.min.js',
            array(),
            RENA_BLOCK_VERSION
        );
    }
}

// Initialize the plugin
new Rena_Block_Plugin();