<?php

/**
 * The admin-specific functionality of the plugin.
 */
class Rena_Multistore_Admin {

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {
        // Constructor
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'rena-multistore-admin',
            RENA_MULTISTORE_PLUGIN_URL . 'admin/css/rena-multistore-admin.css',
            array(),
            RENA_MULTISTORE_VERSION,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'rena-multistore-admin',
            RENA_MULTISTORE_PLUGIN_URL . 'admin/js/rena-multistore-admin.js',
            array('jquery'),
            RENA_MULTISTORE_VERSION,
            false
        );
    }

    /**
     * Add menu items to the admin area.
     */
    public function add_menu_pages() {
        add_menu_page(
            __('Rena Multistore', 'rena-multistore'),
            __('Rena Multistore', 'rena-multistore'),
            'manage_options',
            'rena-multistore',
            array($this, 'display_plugin_main_page'),
            'dashicons-store',
            56
        );
    }

    /**
     * Display the main plugin admin page.
     */
    public function display_plugin_main_page() {
        // Handle form submission
        $this->handle_store_creation();
        
        require_once RENA_MULTISTORE_PLUGIN_DIR . 'admin/partials/rena-multistore-admin-display.php';
    }

    /**
     * Get list of stores
     * 
     * @return array List of store posts
     */
    public function get_stores() {
        $args = array(
            'post_type' => 'rena_store',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        );

        $stores = get_posts($args);
        return $stores;
    }

    /**
     * Get store meta information
     * 
     * @param int $store_id Store post ID
     * @return array Store meta information
     */
    public function get_store_meta($store_id) {
        return array(
            'address' => get_post_meta($store_id, '_store_address', true),
            'phone' => get_post_meta($store_id, '_store_phone', true),
            'status' => get_post_status($store_id)
        );
    }

    /**
     * Handle store creation form submission
     */
    private function handle_store_creation() {
        if (isset($_POST['create_seller']) && current_user_can('manage_options')) {
            // Verify nonce
            if (!isset($_POST['rena_store_nonce']) || !wp_verify_nonce($_POST['rena_store_nonce'], 'create_rena_store')) {
                wp_die(__('Security check failed', 'rena-multistore'));
            }

            $title = sanitize_text_field($_POST['seller_name']);
            $slug = sanitize_title($_POST['seller_slug']);
            $desc = sanitize_textarea_field($_POST['seller_desc']);

            // Ensure unique slug
            $slug = wp_unique_post_slug($slug, 0, 'publish', 'rena_store', 0);

            $new_id = wp_insert_post(array(
                'post_type' => 'rena_store',
                'post_title' => $title,
                'post_name' => $slug,
                'post_status' => 'publish',
                'post_content' => $desc,
                'post_author' => get_current_user_id()
            ));

            if ($new_id) {
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-success is-dismissible"><p>' . 
                         esc_html__('판매자가 등록되었습니다.', 'rena-multistore') . 
                         '</p></div>';
                });
            } else {
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-error is-dismissible"><p>' . 
                         esc_html__('판매자 등록에 실패했습니다.', 'rena-multistore') . 
                         '</p></div>';
                });
            }
        }
    }
} 