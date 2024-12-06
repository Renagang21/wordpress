<?php
/**
 * Shortcode processing functions for Rena Block Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Parse content for shortcodes and return processed content
 *
 * @param string $content The content to process
 * @return string Processed content with shortcodes executed
 */
function rena_parse_content_shortcodes($content) {
    // Remove potential auto-p formatting and process shortcodes
    $content = preg_replace('/<p>\s*(\[.*?\])\s*<\/p>/s', '$1', $content);
    $content = do_shortcode($content);

    return $content;
}

/**
 * Process block content before save
 *
 * @param array $attributes Block attributes
 * @return array Modified attributes
 */
function rena_process_block_content($attributes) {
    if (empty($attributes['content'])) {
        return $attributes;
    }

    // Parse shortcodes if enabled
    if (!empty($attributes['allowShortcode']) && $attributes['allowShortcode']) {
        $attributes['parsedContent'] = rena_parse_content_shortcodes($attributes['content']);
    } else {
        $attributes['parsedContent'] = $attributes['content'];
    }

    return $attributes;
}

/**
 * Sanitize block content
 *
 * @param string $content The content to sanitize
 * @return string Sanitized content
 */
function rena_sanitize_block_content($content) {
    // Allow specific HTML tags and attributes
    $allowed_html = array(
        'a' => array(
            'href' => array(),
            'title' => array(),
            'target' => array()
        ),
        'br' => array(),
        'em' => array(),
        'strong' => array(),
        'code' => array(),
        'pre' => array(),
        'span' => array(
            'class' => array()
        )
    );

    // Sanitize the content
    $content = wp_kses($content, $allowed_html);

    return $content;
}

/**
 * Register block processing hooks
 */
function rena_register_block_processing() {
    // Filter for processing block attributes
    add_filter('rena_block_copy_clipboard_attributes', 'rena_process_block_content');
    
    // Filter for sanitizing content
    add_filter('rena_block_content_save', 'rena_sanitize_block_content');
}
add_action('init', 'rena_register_block_processing');

/**
 * Get processed content for display
 *
 * @param array $attributes Block attributes
 * @return string Processed content ready for display
 */
function rena_get_display_content($attributes) {
    $content = !empty($attributes['parsedContent']) ? 
               $attributes['parsedContent'] : 
               $attributes['content'];

    return wp_kses_post($content);
}
