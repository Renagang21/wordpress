<?php

/**
 * The product class.
 *
 * This class handles product operations.
 */
class Rena_Multisupplier_Product {
    
    /**
     * Product ID.
     */
    private $id;
    
    /**
     * Product data.
     */
    private $data;
    
    /**
     * Initialize the class and set its properties.
     *
     * @param int $product_id The product ID.
     */
    public function __construct($product_id = 0) {
        global $wpdb;
        
        $this->id = 0;
        $this->data = null;
        
        if (is_numeric($product_id) && $product_id > 0) {
            $this->data = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}rena_supplier_products WHERE id = %d",
                    $product_id
                )
            );
            
            if ($this->data) {
                $this->id = $product_id;
            }
        }
    }
    
    /**
     * Check if product exists.
     *
     * @return bool
     */
    public function exists() {
        return (bool) $this->id;
    }
    
    /**
     * Get product ID.
     *
     * @return int
     */
    public function get_id() {
        return $this->id;
    }
    
    /**
     * Get supplier ID.
     *
     * @return int
     */
    public function get_supplier_id() {
        return isset($this->data->supplier_id) ? (int) $this->data->supplier_id : 0;
    }
    
    /**
     * Get product name.
     *
     * @return string
     */
    public function get_name() {
        return isset($this->data->product_name) ? $this->data->product_name : '';
    }
    
    /**
     * Get product code.
     *
     * @return string
     */
    public function get_code() {
        return isset($this->data->product_code) ? $this->data->product_code : '';
    }
    
    /**
     * Get product SKU.
     *
     * @return string
     */
    public function get_sku() {
        return isset($this->data->product_sku) ? $this->data->product_sku : '';
    }
    
    /**
     * Get product description.
     *
     * @return string
     */
    public function get_description() {
        return isset($this->data->product_description) ? $this->data->product_description : '';
    }
    
    /**
     * Get product price.
     *
     * @return float
     */
    public function get_price() {
        return isset($this->data->product_price) ? (float) $this->data->product_price : 0;
    }
    
    /**
     * Get product stock.
     *
     * @return int
     */
    public function get_stock() {
        return isset($this->data->product_stock) ? (int) $this->data->product_stock : 0;
    }
    
    /**
     * Get product image.
     *
     * @return string
     */
    public function get_image() {
        return isset($this->data->product_image) ? $this->data->product_image : '';
    }
    
    /**
     * Get product created date.
     *
     * @return string
     */
    public function get_created_at() {
        return isset($this->data->created_at) ? $this->data->created_at : '';
    }
    
    /**
     * Get product updated date.
     *
     * @return string
     */
    public function get_updated_at() {
        return isset($this->data->updated_at) ? $this->data->updated_at : '';
    }
    
    /**
     * Get product supplier object.
     *
     * @return Rena_Multisupplier_Supplier|null
     */
    public function get_supplier() {
        $supplier_id = $this->get_supplier_id();
        
        if ($supplier_id) {
            return new Rena_Multisupplier_Supplier($supplier_id);
        }
        
        return null;
    }
    
    /**
     * Update product data.
     *
     * @param array $data Product data.
     * @return bool|WP_Error
     */
    public function update($data) {
        if (!$this->id) {
            return new WP_Error('invalid_product', __('Invalid product', 'rena-multisupplier'));
        }
        
        global $wpdb;
        
        // Prepare data for update
        $update_data = array();
        $format = array();
        
        // Update name
        if (isset($data['product_name'])) {
            if (empty($data['product_name'])) {
                return new WP_Error('missing_product_name', __('Product name is required', 'rena-multisupplier'));
            }
            
            $update_data['product_name'] = $data['product_name'];
            $format[] = '%s';
        }
        
        // Update code
        if (isset($data['product_code'])) {
            if (empty($data['product_code'])) {
                return new WP_Error('missing_product_code', __('Product code is required', 'rena-multisupplier'));
            }
            
            // Check if code exists
            if ($data['product_code'] !== $this->get_code()) {
                $exists = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT id FROM {$wpdb->prefix}rena_supplier_products WHERE product_code = %s AND id != %d",
                        $data['product_code'],
                        $this->id
                    )
                );
                
                if ($exists) {
                    return new WP_Error('duplicate_product_code', __('Product code already exists', 'rena-multisupplier'));
                }
            }
            
            $update_data['product_code'] = $data['product_code'];
            $format[] = '%s';
        }
        
        // Update SKU
        if (isset($data['product_sku'])) {
            $update_data['product_sku'] = $data['product_sku'];
            $format[] = '%s';
        }
        
        // Update description
        if (isset($data['product_description'])) {
            $update_data['product_description'] = $data['product_description'];
            $format[] = '%s';
        }
        
        // Update price
        if (isset($data['product_price'])) {
            $update_data['product_price'] = (float) $data['product_price'];
            $format[] = '%f';
        }
        
        // Update stock
        if (isset($data['product_stock'])) {
            $update_data['product_stock'] = (int) $data['product_stock'];
            $format[] = '%d';
        }
        
        // Update image
        if (isset($data['product_image'])) {
            $update_data['product_image'] = $data['product_image'];
            $format[] = '%s';
        }
        
        if (empty($update_data)) {
            return true; // No data to update
        }
        
        $result = $wpdb->update(
            $wpdb->prefix . 'rena_supplier_products',
            $update_data,
            array('id' => $this->id),
            $format,
            array('%d')
        );
        
        if ($result === false) {
            return new WP_Error('db_update_error', $wpdb->last_error);
        }
        
        // Refresh data
        $this->data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}rena_supplier_products WHERE id = %d",
                $this->id
            )
        );
        
        return true;
    }
    
    /**
     * Delete product.
     *
     * @return bool|WP_Error
     */
    public function delete() {
        if (!$this->id) {
            return new WP_Error('invalid_product', __('Invalid product', 'rena-multisupplier'));
        }
        
        global $wpdb;
        
        $result = $wpdb->delete(
            $wpdb->prefix . 'rena_supplier_products',
            array('id' => $this->id),
            array('%d')
        );
        
        if ($result === false) {
            return new WP_Error('db_delete_error', $wpdb->last_error);
        }
        
        $this->id = 0;
        $this->data = null;
        
        return true;
    }
    
    /**
     * Get a product by code.
     *
     * @param string $code Product code.
     * @return Rena_Multisupplier_Product|null
     */
    public static function get_by_code($code) {
        global $wpdb;
        
        $product_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}rena_supplier_products WHERE product_code = %s",
                $code
            )
        );
        
        if ($product_id) {
            return new self($product_id);
        }
        
        return null;
    }
    
    /**
     * Search products.
     *
     * @param array $args Search arguments.
     * @return array
     */
    public static function search($args = array()) {
        global $wpdb;
        
        $default_args = array(
            'supplier_id' => 0,
            'search' => '',
            'orderby' => 'product_name',
            'order' => 'ASC',
            'limit' => -1,
            'offset' => 0,
        );
        
        $args = wp_parse_args($args, $default_args);
        
        $query = "SELECT * FROM {$wpdb->prefix}rena_supplier_products WHERE 1=1";
        $query_args = array();
        
        if (!empty($args['supplier_id'])) {
            $query .= " AND supplier_id = %d";
            $query_args[] = $args['supplier_id'];
        }
        
        if (!empty($args['search'])) {
            $query .= " AND (product_name LIKE %s OR product_code LIKE %s OR product_sku LIKE %s)";
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $query_args[] = $search;
            $query_args[] = $search;
            $query_args[] = $search;
        }
        
        if (!empty($args['orderby'])) {
            $query .= " ORDER BY {$args['orderby']} {$args['order']}";
        }
        
        if ($args['limit'] > 0) {
            $query .= " LIMIT %d";
            $query_args[] = $args['limit'];
            
            if ($args['offset'] > 0) {
                $query .= " OFFSET %d";
                $query_args[] = $args['offset'];
            }
        }
        
        if (!empty($query_args)) {
            $query = $wpdb->prepare($query, $query_args);
        }
        
        $results = $wpdb->get_results($query);
        
        $products = array();
        
        if ($results) {
            foreach ($results as $result) {
                $product = new self($result->id);
                $products[] = $product;
            }
        }
        
        return $products;
    }
    
    /**
     * Count products.
     *
     * @param array $args Search arguments.
     * @return int
     */
    public static function count($args = array()) {
        global $wpdb;
        
        $default_args = array(
            'supplier_id' => 0,
            'search' => '',
        );
        
        $args = wp_parse_args($args, $default_args);
        
        $query = "SELECT COUNT(*) FROM {$wpdb->prefix}rena_supplier_products WHERE 1=1";
        $query_args = array();
        
        if (!empty($args['supplier_id'])) {
            $query .= " AND supplier_id = %d";
            $query_args[] = $args['supplier_id'];
        }
        
        if (!empty($args['search'])) {
            $query .= " AND (product_name LIKE %s OR product_code LIKE %s OR product_sku LIKE %s)";
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $query_args[] = $search;
            $query_args[] = $search;
            $query_args[] = $search;
        }
        
        if (!empty($query_args)) {
            $query = $wpdb->prepare($query, $query_args);
        }
        
        return (int) $wpdb->get_var($query);
    }
} 