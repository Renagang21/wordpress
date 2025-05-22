<?php

/**
 * The admin-specific functionality of the plugin.
 */
class Rena_Multisupplier_Admin {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        // AJAX 액션 등록
        add_action('wp_ajax_rena_test_wc_api', array($this, 'test_wc_api_connection'));
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            RENA_MULTISUPPLIER_PLUGIN_URL . 'admin/css/rena-multisupplier-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            RENA_MULTISUPPLIER_PLUGIN_URL . 'admin/js/rena-multisupplier-admin.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    /**
     * Add menu items to the admin menu.
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('Suppliers', 'rena-multisupplier'),
            __('Suppliers', 'rena-multisupplier'),
            'manage_options',
            'rena-multisupplier',
            array($this, 'display_dashboard_page'),
            'dashicons-store',
            56
        );

        // Suppliers submenu
        add_submenu_page(
            'rena-multisupplier',
            __('All Suppliers', 'rena-multisupplier'),
            __('All Suppliers', 'rena-multisupplier'),
            'manage_options',
            'edit.php?post_type=rena_supplier'
        );

        // Add supplier
        add_submenu_page(
            'rena-multisupplier',
            __('Add Supplier', 'rena-multisupplier'),
            __('Add Supplier', 'rena-multisupplier'),
            'manage_options',
            'post-new.php?post_type=rena_supplier'
        );

        // Products submenu
        add_submenu_page(
            'rena-multisupplier',
            __('Products', 'rena-multisupplier'),
            __('Products', 'rena-multisupplier'),
            'manage_options',
            'rena-supplier-products',
            array($this, 'display_products_page')
        );

        // Import/Export submenu
        add_submenu_page(
            'rena-multisupplier',
            __('Import/Export', 'rena-multisupplier'),
            __('Import/Export', 'rena-multisupplier'),
            'manage_options',
            'rena-supplier-import-export',
            array($this, 'display_import_export_page')
        );

        // Settings submenu
        add_submenu_page(
            'rena-multisupplier',
            __('Settings', 'rena-multisupplier'),
            __('Settings', 'rena-multisupplier'),
            'manage_options',
            'rena-supplier-settings',
            array($this, 'display_settings_page')
        );
    }

    /**
     * Display the dashboard admin page.
     */
    public function display_dashboard_page() {
        include RENA_MULTISUPPLIER_PLUGIN_DIR . 'admin/partials/rena-multisupplier-admin-dashboard.php';
    }

    /**
     * Display the products admin page.
     */
    public function display_products_page() {
        include RENA_MULTISUPPLIER_PLUGIN_DIR . 'admin/partials/rena-multisupplier-admin-products.php';
    }

    /**
     * Display the import/export admin page.
     */
    public function display_import_export_page() {
        include RENA_MULTISUPPLIER_PLUGIN_DIR . 'admin/partials/rena-multisupplier-admin-import-export.php';
    }

    /**
     * Display the settings admin page.
     */
    public function display_settings_page() {
        include RENA_MULTISUPPLIER_PLUGIN_DIR . 'admin/partials/rena-multisupplier-admin-settings.php';
    }

    /**
     * Register meta boxes.
     */
    public function register_meta_boxes() {
        add_meta_box(
            'rena_supplier_details',
            __('Supplier Details', 'rena-multisupplier'),
            array($this, 'render_supplier_details_meta_box'),
            'rena_supplier',
            'normal',
            'high'
        );

        add_meta_box(
            'rena_supplier_products',
            __('Supplier Products', 'rena-multisupplier'),
            array($this, 'render_supplier_products_meta_box'),
            'rena_supplier',
            'normal',
            'default'
        );
    }

    /**
     * Render the supplier details meta box.
     *
     * @param WP_Post $post Current post object.
     */
    public function render_supplier_details_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('rena_supplier_details', 'rena_supplier_details_nonce');

        // Get current values
        $supplier_code = get_post_meta($post->ID, '_supplier_code', true);
        $supplier_contact = get_post_meta($post->ID, '_supplier_contact', true);
        $supplier_email = get_post_meta($post->ID, '_supplier_email', true);
        $supplier_phone = get_post_meta($post->ID, '_supplier_phone', true);
        $supplier_address = get_post_meta($post->ID, '_supplier_address', true);

        // Include template
        include RENA_MULTISUPPLIER_PLUGIN_DIR . 'admin/partials/meta-boxes/supplier-details.php';
    }

    /**
     * Render the supplier products meta box.
     *
     * @param WP_Post $post Current post object.
     */
    public function render_supplier_products_meta_box($post) {
        // Get products for this supplier
        global $wpdb;
        $supplier_id = $post->ID;
        
        $products = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}rena_supplier_products WHERE supplier_id = %d ORDER BY product_name ASC",
                $supplier_id
            )
        );

        // Include template
        include RENA_MULTISUPPLIER_PLUGIN_DIR . 'admin/partials/meta-boxes/supplier-products.php';
    }

    /**
     * Save meta box data.
     *
     * @param int     $post_id The post ID.
     * @param WP_Post $post    Post object.
     */
    public function save_meta_boxes($post_id, $post) {
        // Check if our nonce is set
        if (!isset($_POST['rena_supplier_details_nonce'])) {
            return;
        }

        // Verify the nonce
        if (!wp_verify_nonce($_POST['rena_supplier_details_nonce'], 'rena_supplier_details')) {
            return;
        }

        // If this is an autosave, we don't want to do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save supplier details
        if (isset($_POST['supplier_code'])) {
            update_post_meta($post_id, '_supplier_code', sanitize_text_field($_POST['supplier_code']));
        }
        
        if (isset($_POST['supplier_contact'])) {
            update_post_meta($post_id, '_supplier_contact', sanitize_text_field($_POST['supplier_contact']));
        }
        
        if (isset($_POST['supplier_email'])) {
            update_post_meta($post_id, '_supplier_email', sanitize_email($_POST['supplier_email']));
        }
        
        if (isset($_POST['supplier_phone'])) {
            update_post_meta($post_id, '_supplier_phone', sanitize_text_field($_POST['supplier_phone']));
        }
        
        if (isset($_POST['supplier_address'])) {
            update_post_meta($post_id, '_supplier_address', sanitize_textarea_field($_POST['supplier_address']));
        }
    }

    /**
     * WooCommerce REST API 연결 테스트
     */
    public function test_wc_api_connection() {
        // 보안 체크
        check_ajax_referer('rena_test_api', 'nonce');
        
        // API 설정 가져오기
        $api_endpoint = get_option('rena_multisupplier_wc_api_endpoint', '');
        $api_key = get_option('rena_multisupplier_wc_api_key', '');
        $api_secret = get_option('rena_multisupplier_wc_api_secret', '');
        
        // 필수 설정 체크
        if (empty($api_endpoint) || empty($api_key) || empty($api_secret)) {
            wp_send_json_error(array('message' => 'API 설정이 완료되지 않았습니다.'));
            return;
        }
        
        // 엔드포인트 URL 정리
        $api_endpoint = trailingslashit($api_endpoint);
        $test_url = $api_endpoint . 'products?per_page=1';
        
        // API 요청 준비
        $args = array(
            'method' => 'GET',
            'timeout' => 30,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($api_key . ':' . $api_secret)
            ),
            'cookies' => array()
        );
        
        // API 요청 실행
        $response = wp_remote_request($test_url, $args);
        
        // 응답 처리
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
            return;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($response_code === 200) {
            $products = json_decode($body, true);
            $count = count($products);
            wp_send_json_success(array('message' => '제품 ' . $count . '개를 성공적으로 가져왔습니다.'));
        } else {
            $error = json_decode($body, true);
            $error_message = isset($error['message']) ? $error['message'] : '응답 코드: ' . $response_code;
            wp_send_json_error(array('message' => $error_message));
        }
    }
} 