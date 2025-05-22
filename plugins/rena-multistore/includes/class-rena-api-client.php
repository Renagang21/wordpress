<?php
/**
 * WooCommerce API Client
 */
class Rena_API_Client {
    private $api_url;
    private $consumer_key;
    private $consumer_secret;

    public function __construct() {
        $this->api_url = get_option('rena_woo_api_url', '');
        $this->consumer_key = get_option('rena_woo_consumer_key', '');
        $this->consumer_secret = get_option('rena_woo_consumer_secret', '');
    }

    /**
     * Get products from WooCommerce API
     * 
     * @param array $args Query arguments
     * @return array|WP_Error Products or error
     */
    public function get_products($args = array()) {
        $defaults = array(
            'per_page' => 12,
            'page' => 1,
            'status' => 'publish'
        );

        $args = wp_parse_args($args, $defaults);
        $endpoint = '/wp-json/wc/v3/products';
        
        $url = add_query_arg($args, $this->api_url . $endpoint);
        
        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($this->consumer_key . ':' . $this->consumer_secret)
            )
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $products = json_decode($body, true);

        return $products;
    }

    /**
     * Import product to local store
     * 
     * @param array $product_data Product data from API
     * @return int|WP_Error Post ID or error
     */
    public function import_product($product_data) {
        if (!is_user_logged_in()) {
            return new WP_Error('not_logged_in', __('User must be logged in to import products.', 'rena-multistore'));
        }

        $post_data = array(
            'post_title' => sanitize_text_field($product_data['name']),
            'post_content' => wp_kses_post($product_data['description']),
            'post_status' => 'publish',
            'post_type' => 'rena_store_product',
            'post_author' => get_current_user_id()
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        // Save product meta
        update_post_meta($post_id, '_original_product_id', $product_data['id']);
        update_post_meta($post_id, '_product_price', $product_data['price']);
        
        // Save product image
        if (!empty($product_data['images'][0]['src'])) {
            $image_url = $product_data['images'][0]['src'];
            $this->save_product_image($post_id, $image_url);
        }

        return $post_id;
    }

    /**
     * Save product image from URL
     * 
     * @param int $post_id Post ID
     * @param string $image_url Image URL
     * @return int|false Attachment ID or false
     */
    private function save_product_image($post_id, $image_url) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $image_id = media_sideload_image($image_url, $post_id, '', 'id');
        
        if (!is_wp_error($image_id)) {
            set_post_thumbnail($post_id, $image_id);
            return $image_id;
        }

        return false;
    }
} 