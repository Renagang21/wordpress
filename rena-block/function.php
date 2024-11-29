<?php
/**
 * Common functions for Rena Block Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// Shortcode parsing function
function rena_parse_shortcode($content) {
    return do_shortcode($content);
}

// QR Code generation utility
function rena_generate_qr_code($content, $size = 300) {
    // QR code generation logic here
    // You might want to use a library like phpqrcode
    return $qr_code_data;
}

// Clipboard operations
function rena_sanitize_clipboard_content($content) {
    return wp_kses_post($content);
}

// Design settings
function rena_get_block_settings($block_name) {
    $defaults = [
        'backgroundColor' => '#ffffff',
        'borderStyle' => 'solid',
        'borderColor' => '#dddddd',
        'buttonStyle' => 'default',
    ];
    
    return apply_filters("rena_block_{$block_name}_settings", $defaults);
}

// Utility function for mobile detection
function rena_is_mobile() {
    return wp_is_mobile();
}

// Print layout helper
function rena_get_print_layout_settings() {
    return [
        'pageSize' => 'A4',
        'qrCodesPerRow' => 3,
        'qrCodeSize' => 50, // mm
        'margins' => [
            'top' => 10,
            'right' => 10,
            'bottom' => 10,
            'left' => 10,
        ]
    ];
}