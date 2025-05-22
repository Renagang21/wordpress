<?php

/**
 * Fired during plugin activation.
 */
class Rena_Multisupplier_Activator {

    /**
     * Initialize plugin on activation.
     */
    public static function activate() {
        // Create necessary database tables
        self::create_tables();

        // Set default options
        self::set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create necessary database tables.
     */
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Suppliers table
        $sql_suppliers = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rena_suppliers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            supplier_name varchar(255) NOT NULL,
            supplier_code varchar(50) NOT NULL,
            supplier_contact varchar(255),
            supplier_email varchar(255),
            supplier_phone varchar(50),
            supplier_address text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY supplier_code (supplier_code)
        ) $charset_collate;";

        // Products table
        $sql_products = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rena_supplier_products (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            supplier_id bigint(20) NOT NULL,
            product_name varchar(255) NOT NULL,
            product_code varchar(50) NOT NULL,
            product_sku varchar(50),
            product_description text,
            product_price decimal(10,2) NOT NULL DEFAULT 0.00,
            product_stock int(11) NOT NULL DEFAULT 0,
            product_image varchar(255),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY product_code (product_code),
            KEY supplier_id (supplier_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_suppliers);
        dbDelta($sql_products);
    }

    /**
     * Set default plugin options.
     */
    private static function set_default_options() {
        $default_options = array(
            'rena_multisupplier_version' => RENA_MULTISUPPLIER_VERSION,
            'rena_multisupplier_settings' => array(
                'enabled' => true,
                'default_supplier' => '',
                'auto_import' => false,
                'sync_frequency' => 'daily'
            )
        );

        foreach ($default_options as $option_name => $option_value) {
            if (get_option($option_name) === false) {
                add_option($option_name, $option_value);
            }
        }
    }
} 