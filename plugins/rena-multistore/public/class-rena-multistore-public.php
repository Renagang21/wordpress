<?php
   /**
    * The public-facing functionality of the plugin.
    */
   class Rena_Multistore_Public {

       public function __construct() {
           // 공개 영역 초기화 코드
           
           // 판매자 대시보드 숏코드 등록
           add_shortcode('rena_seller_dashboard', array($this, 'seller_dashboard_shortcode'));
       }

       public function enqueue_styles() {
           wp_enqueue_style(
               'rena-multistore-public',
               RENA_MULTISTORE_PLUGIN_URL . 'public/css/rena-multistore-public.css',
               array(),
               RENA_MULTISTORE_VERSION,
               'all'
           );
       }

       public function enqueue_scripts() {
           wp_enqueue_script(
               'rena-multistore-public',
               RENA_MULTISTORE_PLUGIN_URL . 'public/js/rena-multistore-public.js',
               array('jquery'),
               RENA_MULTISTORE_VERSION,
               false
           );
       }

       /**
        * Register rewrite rules for store pages
        */
       public function register_rewrite_rules() {
           add_rewrite_rule(
               '^seller/([^/]+)/?$',
               'index.php?rena_store_slug=$matches[1]',
               'top'
           );
           
           // 판매자 대시보드 URL 규칙 추가
           add_rewrite_rule(
               '^seller-dashboard/?$',
               'index.php?pagename=seller-dashboard',
               'top'
           );
       }

       /**
        * Add custom query vars
        */
       public function add_query_vars($vars) {
           $vars[] = 'rena_store_slug';
           return $vars;
       }

       /**
        * Handle template redirect for store pages
        */
       public function template_redirect() {
           $slug = get_query_var('rena_store_slug');
           if ($slug) {
               $store = get_page_by_path($slug, OBJECT, 'rena_store');
               if ($store) {
                   include RENA_MULTISTORE_PLUGIN_DIR . 'public/templates/store-page.php';
                   exit;
               }
           }
           
           // 판매자 대시보드 페이지 처리
           if (is_page('seller-dashboard')) {
               $this->display_seller_dashboard();
               exit;
           }
       }

       /**
        * Get current user's store
        * 
        * @return WP_Post|null Store post object or null if not found
        */
       public function get_current_user_store() {
           if (!is_user_logged_in()) {
               return null;
           }

           $args = array(
               'post_type' => 'rena_store',
               'post_status' => 'publish',
               'author' => get_current_user_id(),
               'posts_per_page' => 1
           );

           $stores = get_posts($args);
           return !empty($stores) ? $stores[0] : null;
       }

       /**
        * Check if current user is a store seller
        * 
        * @return bool
        */
       public function is_store_seller() {
           if (!is_user_logged_in()) {
               return false;
           }

           $user = wp_get_current_user();
           return in_array('store_seller', (array) $user->roles);
       }

       /**
        * Display seller dashboard
        */
       public function display_seller_dashboard() {
           if (!$this->is_store_seller()) {
               wp_die(__('You do not have permission to access this page.', 'rena-multistore'));
           }

           $store = $this->get_current_user_store();
           include RENA_MULTISTORE_PLUGIN_DIR . 'public/templates/seller-dashboard.php';
       }

       public function handle_product_import() {
           if (!isset($_POST['import_product']) || !isset($_POST['product_nonce'])) {
               return;
           }

           if (!wp_verify_nonce($_POST['product_nonce'], 'import_product')) {
               wp_die(__('보안 검증에 실패했습니다.', 'rena-multistore'));
           }

           $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
           if (!$product_id) {
               wp_die(__('잘못된 상품 ID입니다.', 'rena-multistore'));
           }

           // Get current user's store
           $store = $this->get_user_store();
           if (!$store) {
               wp_die(__('스토어가 등록되어 있지 않습니다.', 'rena-multistore'));
           }

           // Get product data from API
           $api_client = new Rena_API_Client();
           $product_data = $api_client->get_product($product_id);

           if (is_wp_error($product_data)) {
               wp_die($product_data->get_error_message());
           }

           // Create local product
           $args = array(
               'post_title'    => $product_data['name'],
               'post_content'  => $product_data['description'],
               'post_status'   => 'publish',
               'post_type'     => 'product',
               'post_author'   => get_current_user_id(),
           );

           $local_product_id = wp_insert_post($args);

           if (is_wp_error($local_product_id)) {
               wp_die($local_product_id->get_error_message());
           }

           // Set product meta
           update_post_meta($local_product_id, '_price', $product_data['price']);
           update_post_meta($local_product_id, '_regular_price', $product_data['regular_price']);
           update_post_meta($local_product_id, '_sale_price', $product_data['sale_price']);
           update_post_meta($local_product_id, '_store_id', $store->ID);
           update_post_meta($local_product_id, '_original_product_id', $product_id);

           // Set product type
           wp_set_object_terms($local_product_id, 'simple', 'product_type');

           // Import product image if exists
           if (!empty($product_data['images'][0]['src'])) {
               $image_url = $product_data['images'][0]['src'];
               $upload = media_sideload_image($image_url, $local_product_id, '', 'id');
               
               if (!is_wp_error($upload)) {
                   set_post_thumbnail($local_product_id, $upload);
               }
           }

           wp_redirect(add_query_arg('imported', '1', wp_get_referer()));
           exit;
       }

       private function get_user_store() {
           $user_id = get_current_user_id();
           if (!$user_id) {
               return false;
           }

           $stores = get_posts(array(
               'post_type' => 'store',
               'author'    => $user_id,
               'posts_per_page' => 1,
           ));

           return !empty($stores) ? $stores[0] : false;
       }

       /**
        * Shortcode for seller dashboard
        */
       public function seller_dashboard_shortcode($atts) {
           ob_start();
           $this->display_seller_dashboard();
           return ob_get_clean();
       }
   }