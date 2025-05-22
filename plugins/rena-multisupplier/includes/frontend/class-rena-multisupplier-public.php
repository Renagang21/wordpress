<?php

/**
 * The public-facing functionality of the plugin.
 */
class Rena_Multisupplier_Public {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * WooCommerce REST API endpoint.
     */
    private $api_endpoint;

    /**
     * WooCommerce REST API credentials.
     */
    private $api_key;
    private $api_secret;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     * @param string $api_endpoint The WooCommerce REST API endpoint.
     */
    public function __construct($plugin_name, $version, $api_endpoint) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->api_endpoint = $api_endpoint;
        $this->api_key = get_option('rena_multisupplier_wc_api_key', '');
        $this->api_secret = get_option('rena_multisupplier_wc_api_secret', '');
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            RENA_MULTISUPPLIER_PLUGIN_URL . 'public/css/rena-multisupplier-public.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            RENA_MULTISUPPLIER_PLUGIN_URL . 'public/js/rena-multisupplier-public.js',
            array('jquery'),
            $this->version,
            false
        );

        // Add script variables for AJAX
        wp_localize_script($this->plugin_name, 'rena_multisupplier', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rena_multisupplier_nonce')
        ));
    }

    /**
     * Template override for single supplier pages.
     *
     * @param string $template The path of the template to include.
     * @return string
     */
    public function template_include($template) {
        if (is_singular('rena_supplier')) {
            $template = RENA_MULTISUPPLIER_PLUGIN_DIR . 'public/templates/single-supplier.php';
        }
        
        return $template;
    }

    /**
     * Shortcode to display supplier products.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function supplier_products_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'supplier_id' => 0,
                'limit' => 10,
                'columns' => 3,
                'orderby' => 'product_name',
                'order' => 'ASC',
            ),
            $atts,
            'rena_supplier_products'
        );
        
        // Get supplier
        $supplier_id = (int) $atts['supplier_id'];
        
        if (!$supplier_id) {
            // If supplier ID is not provided, try to get from current post
            if (is_singular('rena_supplier')) {
                $supplier_id = get_the_ID();
            }
        }
        
        if (!$supplier_id) {
            return '<p>공급업체가 지정되지 않았습니다.</p>';
        }
        
        $supplier = new Rena_Multisupplier_Supplier($supplier_id);
        
        if (!$supplier->get_id()) {
            return '<p>유효하지 않은 공급업체입니다.</p>';
        }
        
        // Get products
        $products = $supplier->get_products(array(
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'limit' => (int) $atts['limit'],
        ));
        
        if (empty($products)) {
            return '<p>상품이 없습니다.</p>';
        }
        
        // Start output buffer
        ob_start();
        
        // Include template
        include RENA_MULTISUPPLIER_PLUGIN_DIR . 'public/templates/supplier-products.php';
        
        // Return the output
        return ob_get_clean();
    }

    /**
     * Register REST API endpoints.
     */
    public function register_rest_endpoints() {
        register_rest_route('rena-multisupplier/v1', '/suppliers', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_suppliers'),
            'permission_callback' => array($this, 'check_api_permission')
        ));

        register_rest_route('rena-multisupplier/v1', '/supplier/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_supplier'),
            'permission_callback' => array($this, 'check_api_permission')
        ));

        register_rest_route('rena-multisupplier/v1', '/supplier/(?P<id>\d+)/products', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_supplier_products'),
            'permission_callback' => array($this, 'check_api_permission')
        ));

        register_rest_route('rena-multisupplier/v1', '/products', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_products'),
            'permission_callback' => array($this, 'check_api_permission')
        ));

        register_rest_route('rena-multisupplier/v1', '/product/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_product'),
            'permission_callback' => array($this, 'check_api_permission')
        ));

        register_rest_route('rena-multisupplier/v1', '/sync-to-woocommerce', array(
            'methods' => 'POST',
            'callback' => array($this, 'sync_to_woocommerce'),
            'permission_callback' => array($this, 'check_api_permission')
        ));
    }

    /**
     * Check API permission.
     *
     * @return bool
     */
    public function check_api_permission() {
        // Simple permission check for now
        return current_user_can('manage_options');
    }

    /**
     * Get all suppliers.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_suppliers($request) {
        $args = array(
            'post_type' => 'rena_supplier',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        
        $suppliers = get_posts($args);
        $data = array();
        
        foreach ($suppliers as $supplier_post) {
            $supplier = new Rena_Multisupplier_Supplier($supplier_post);
            $data[] = array(
                'id' => $supplier->get_id(),
                'name' => $supplier->get_name(),
                'code' => $supplier->get_code(),
                'contact' => $supplier->get_contact(),
                'email' => $supplier->get_email(),
                'phone' => $supplier->get_phone(),
                'address' => $supplier->get_address()
            );
        }
        
        return rest_ensure_response($data);
    }

    /**
     * Get single supplier.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_supplier($request) {
        $supplier_id = $request['id'];
        $supplier = new Rena_Multisupplier_Supplier($supplier_id);
        
        if (!$supplier->get_id()) {
            return new WP_Error('invalid_supplier', '유효하지 않은 공급업체입니다.', array('status' => 404));
        }
        
        $data = array(
            'id' => $supplier->get_id(),
            'name' => $supplier->get_name(),
            'code' => $supplier->get_code(),
            'contact' => $supplier->get_contact(),
            'email' => $supplier->get_email(),
            'phone' => $supplier->get_phone(),
            'address' => $supplier->get_address()
        );
        
        return rest_ensure_response($data);
    }

    /**
     * Get supplier products.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_supplier_products($request) {
        $supplier_id = $request['id'];
        $supplier = new Rena_Multisupplier_Supplier($supplier_id);
        
        if (!$supplier->get_id()) {
            return new WP_Error('invalid_supplier', '유효하지 않은 공급업체입니다.', array('status' => 404));
        }
        
        $products = $supplier->get_products();
        $data = array();
        
        foreach ($products as $product_data) {
            $product = new Rena_Multisupplier_Product($product_data->id);
            $data[] = array(
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'code' => $product->get_code(),
                'sku' => $product->get_sku(),
                'description' => $product->get_description(),
                'price' => $product->get_price(),
                'stock' => $product->get_stock(),
                'image' => $product->get_image()
            );
        }
        
        return rest_ensure_response($data);
    }

    /**
     * Get all products.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_products($request) {
        $search = isset($request['search']) ? $request['search'] : '';
        $limit = isset($request['limit']) ? (int) $request['limit'] : -1;
        
        $products = Rena_Multisupplier_Product::search(array(
            'search' => $search,
            'limit' => $limit
        ));
        
        $data = array();
        
        foreach ($products as $product) {
            $data[] = array(
                'id' => $product->get_id(),
                'supplier_id' => $product->get_supplier_id(),
                'name' => $product->get_name(),
                'code' => $product->get_code(),
                'sku' => $product->get_sku(),
                'description' => $product->get_description(),
                'price' => $product->get_price(),
                'stock' => $product->get_stock(),
                'image' => $product->get_image()
            );
        }
        
        return rest_ensure_response($data);
    }

    /**
     * Get single product.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_product($request) {
        $product_id = $request['id'];
        $product = new Rena_Multisupplier_Product($product_id);
        
        if (!$product->exists()) {
            return new WP_Error('invalid_product', '유효하지 않은 상품입니다.', array('status' => 404));
        }
        
        $data = array(
            'id' => $product->get_id(),
            'supplier_id' => $product->get_supplier_id(),
            'name' => $product->get_name(),
            'code' => $product->get_code(),
            'sku' => $product->get_sku(),
            'description' => $product->get_description(),
            'price' => $product->get_price(),
            'stock' => $product->get_stock(),
            'image' => $product->get_image()
        );
        
        return rest_ensure_response($data);
    }

    /**
     * Sync products to WooCommerce via REST API.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function sync_to_woocommerce($request) {
        $product_ids = isset($request['product_ids']) ? $request['product_ids'] : array();
        
        if (empty($product_ids)) {
            return new WP_Error('no_products', '선택된 상품이 없습니다.', array('status' => 400));
        }
        
        $results = array(
            'success' => array(),
            'error' => array()
        );
        
        foreach ($product_ids as $product_id) {
            $product = new Rena_Multisupplier_Product($product_id);
            
            if (!$product->exists()) {
                $results['error'][] = array(
                    'id' => $product_id,
                    'message' => '상품을 찾을 수 없습니다.'
                );
                continue;
            }
            
            $result = $this->create_woocommerce_product($product);
            
            if (is_wp_error($result)) {
                $results['error'][] = array(
                    'id' => $product_id,
                    'message' => $result->get_error_message()
                );
            } else {
                $results['success'][] = array(
                    'id' => $product_id,
                    'wc_id' => $result
                );
            }
        }
        
        return rest_ensure_response($results);
    }
    
    /**
     * Create a WooCommerce product via REST API.
     *
     * @param Rena_Multisupplier_Product $product Product object.
     * @return int|WP_Error WooCommerce product ID or error.
     */
    private function create_woocommerce_product($product) {
        // WooCommerce API endpoint
        $endpoint = trailingslashit($this->api_endpoint) . 'products';
        
        // Product data
        $data = array(
            'name' => $product->get_name(),
            'type' => 'simple',
            'regular_price' => (string) $product->get_price(),
            'description' => $product->get_description(),
            'short_description' => substr($product->get_description(), 0, 100),
            'sku' => $product->get_sku(),
            'manage_stock' => true,
            'stock_quantity' => $product->get_stock(),
            'stock_status' => $product->get_stock() > 0 ? 'instock' : 'outofstock',
            'meta_data' => array(
                array(
                    'key' => '_rena_supplier_id',
                    'value' => $product->get_supplier_id(),
                ),
                array(
                    'key' => '_rena_product_id',
                    'value' => $product->get_id(),
                ),
                array(
                    'key' => '_rena_product_code',
                    'value' => $product->get_code(),
                ),
            ),
        );
        
        // Set up request arguments
        $args = array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->api_key . ':' . $this->api_secret)
            ),
            'body' => json_encode($data),
            'cookies' => array(),
        );
        
        // Make the request
        $response = wp_remote_request($endpoint, $args);
        
        // Check for errors
        if (is_wp_error($response)) {
            return $response;
        }
        
        // Check response code
        $response_code = wp_remote_retrieve_response_code($response);
        
        if ($response_code !== 201) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if (isset($data['message'])) {
                return new WP_Error('api_error', $data['message']);
            }
            
            return new WP_Error('api_error', '상품 생성 실패 (코드: ' . $response_code . ')');
        }
        
        // Get product ID from response
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['id'])) {
            return $data['id'];
        }
        
        return new WP_Error('unknown_error', '알 수 없는 오류가 발생했습니다.');
    }
} 