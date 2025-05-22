<?php

/**
 * Fired during plugin activation.
 */
class Rena_Multistore_Activator {

    /**
     * Initialize plugin on activation.
     */
    public static function activate() {
        // Create necessary database tables
        self::create_tables();

        // Set default options
        self::set_default_options();
        
        // Create store seller role
        self::create_seller_role();
        
        // Register custom post types
        self::register_post_types();
        
        // Create plugin pages
        self::create_pages();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create necessary database tables.
     */
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Example table creation (customize as needed)
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rena_multistore_stores (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            store_name varchar(255) NOT NULL,
            store_address text,
            store_phone varchar(50),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Set default plugin options.
     */
    private static function set_default_options() {
        $default_options = array(
            'rena_multistore_version' => RENA_MULTISTORE_VERSION,
            'rena_multistore_settings' => array(
                'enabled' => true,
                'default_store' => 1
            )
        );

        foreach ($default_options as $option_name => $option_value) {
            if (get_option($option_name) === false) {
                add_option($option_name, $option_value);
            }
        }
    }

    /**
     * Create store seller role
     */
    private static function create_seller_role() {
        // Add 'store_seller' role if it doesn't exist
        if (!get_role('store_seller')) {
            add_role(
                'store_seller',
                __('Store Seller', 'rena-multistore'),
                array(
                    'read' => true,
                    'edit_posts' => true,
                    'delete_posts' => true,
                    'publish_posts' => true,
                    'upload_files' => true,
                )
            );
        }
    }

    /**
     * Register custom post types
     */
    private static function register_post_types() {
        // Register 'rena_store' post type
        register_post_type('rena_store', 
            array(
                'labels' => array(
                    'name' => __('Stores', 'rena-multistore'),
                    'singular_name' => __('Store', 'rena-multistore'),
                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
                'rewrite' => array('slug' => 'seller'),
                'menu_icon' => 'dashicons-store',
                'show_in_rest' => true,
            )
        );
    }

    /**
     * Create plugin pages
     */
    private static function create_pages() {
        $pages = array(
            'seller-dashboard' => array(
                'title' => __('판매자 대시보드', 'rena-multistore'),
                'content' => '[rena_seller_dashboard]',
                'status' => 'publish',
            ),
        );
        
        foreach ($pages as $slug => $page_data) {
            // Check if the page already exists
            $page_exists = get_page_by_path($slug);
            
            if (!$page_exists) {
                // Create the page
                $page_id = wp_insert_post(array(
                    'post_title' => $page_data['title'],
                    'post_name' => $slug,
                    'post_content' => $page_data['content'],
                    'post_status' => $page_data['status'],
                    'post_type' => 'page',
                    'comment_status' => 'closed'
                ));
                
                // Save the page ID in the plugin options for future reference
                $plugin_options = get_option('rena_multistore_settings', array());
                $plugin_options['pages'][$slug] = $page_id;
                update_option('rena_multistore_settings', $plugin_options);
            }
        }
    }
} 