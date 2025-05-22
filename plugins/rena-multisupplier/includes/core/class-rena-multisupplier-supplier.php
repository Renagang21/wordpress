<?php

/**
 * The supplier class.
 *
 * This class handles supplier operations.
 */
class Rena_Multisupplier_Supplier {
    
    /**
     * Supplier ID.
     */
    private $id;
    
    /**
     * Supplier data.
     */
    private $data;
    
    /**
     * Initialize the class and set its properties.
     *
     * @param int|WP_Post $supplier_id The supplier ID or post object.
     */
    public function __construct($supplier_id = 0) {
        if ($supplier_id instanceof WP_Post) {
            $this->id = $supplier_id->ID;
            $this->data = $supplier_id;
        } elseif (is_numeric($supplier_id) && $supplier_id > 0) {
            $this->id = $supplier_id;
            $this->data = get_post($this->id);
        } else {
            $this->id = 0;
            $this->data = null;
        }
    }
    
    /**
     * Get supplier ID.
     *
     * @return int
     */
    public function get_id() {
        return $this->id;
    }
    
    /**
     * Get supplier name.
     *
     * @return string
     */
    public function get_name() {
        return $this->data ? $this->data->post_title : '';
    }
    
    /**
     * Get supplier code.
     *
     * @return string
     */
    public function get_code() {
        return get_post_meta($this->id, '_supplier_code', true);
    }
    
    /**
     * Get supplier contact.
     *
     * @return string
     */
    public function get_contact() {
        return get_post_meta($this->id, '_supplier_contact', true);
    }
    
    /**
     * Get supplier email.
     *
     * @return string
     */
    public function get_email() {
        return get_post_meta($this->id, '_supplier_email', true);
    }
    
    /**
     * Get supplier phone.
     *
     * @return string
     */
    public function get_phone() {
        return get_post_meta($this->id, '_supplier_phone', true);
    }
    
    /**
     * Get supplier address.
     *
     * @return string
     */
    public function get_address() {
        return get_post_meta($this->id, '_supplier_address', true);
    }
    
    /**
     * Get supplier products.
     *
     * @param array $args Optional. Additional query arguments.
     * @return array
     */
    public function get_products($args = array()) {
        if (!$this->id) {
            return array();
        }
        
        global $wpdb;
        
        $default_args = array(
            'orderby' => 'product_name',
            'order' => 'ASC',
            'limit' => -1,
            'offset' => 0,
        );
        
        $args = wp_parse_args($args, $default_args);
        
        $query = "SELECT * FROM {$wpdb->prefix}rena_supplier_products WHERE supplier_id = %d";
        
        if (!empty($args['orderby'])) {
            $query .= " ORDER BY {$args['orderby']} {$args['order']}";
        }
        
        if ((int) $args['limit'] > 0) {
            $query .= " LIMIT %d";
            if ((int) $args['offset'] > 0) {
                $query .= " OFFSET %d";
                $query = $wpdb->prepare($query, $this->id, (int) $args['limit'], (int) $args['offset']);
            } else {
                $query = $wpdb->prepare($query, $this->id, (int) $args['limit']);
            }
        } else {
            $query = $wpdb->prepare($query, $this->id);
        }
        
        return $wpdb->get_results($query);
    }
    
    /**
     * Get supplier products count.
     *
     * @return int
     */
    public function get_products_count() {
        if (!$this->id) {
            return 0;
        }
        
        global $wpdb;
        
        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}rena_supplier_products WHERE supplier_id = %d",
                $this->id
            )
        );
    }
    
    /**
     * Add product to supplier.
     *
     * @param array $product_data Product data.
     * @return int|WP_Error
     */
    public function add_product($product_data) {
        if (!$this->id) {
            return new WP_Error('invalid_supplier', __('Invalid supplier', 'rena-multisupplier'));
        }
        
        if (empty($product_data['product_name'])) {
            return new WP_Error('missing_product_name', __('Product name is required', 'rena-multisupplier'));
        }
        
        if (empty($product_data['product_code'])) {
            return new WP_Error('missing_product_code', __('Product code is required', 'rena-multisupplier'));
        }
        
        global $wpdb;
        
        // Check if product code already exists
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}rena_supplier_products WHERE product_code = %s AND supplier_id != %d",
                $product_data['product_code'],
                $this->id
            )
        );
        
        if ($exists) {
            return new WP_Error('duplicate_product_code', __('Product code already exists', 'rena-multisupplier'));
        }
        
        $data = array(
            'supplier_id' => $this->id,
            'product_name' => $product_data['product_name'],
            'product_code' => $product_data['product_code'],
            'product_sku' => isset($product_data['product_sku']) ? $product_data['product_sku'] : '',
            'product_description' => isset($product_data['product_description']) ? $product_data['product_description'] : '',
            'product_price' => isset($product_data['product_price']) ? (float) $product_data['product_price'] : 0,
            'product_stock' => isset($product_data['product_stock']) ? (int) $product_data['product_stock'] : 0,
            'product_image' => isset($product_data['product_image']) ? $product_data['product_image'] : '',
        );
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'rena_supplier_products',
            $data,
            array(
                '%d', '%s', '%s', '%s', '%s', '%f', '%d', '%s'
            )
        );
        
        if ($result === false) {
            return new WP_Error('db_insert_error', $wpdb->last_error);
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update supplier product.
     *
     * @param int   $product_id   Product ID.
     * @param array $product_data Product data.
     * @return bool|WP_Error
     */
    public function update_product($product_id, $product_data) {
        if (!$this->id) {
            return new WP_Error('invalid_supplier', __('Invalid supplier', 'rena-multisupplier'));
        }
        
        global $wpdb;
        
        // Check if product exists
        $product = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}rena_supplier_products WHERE id = %d AND supplier_id = %d",
                $product_id,
                $this->id
            )
        );
        
        if (!$product) {
            return new WP_Error('invalid_product', __('Invalid product', 'rena-multisupplier'));
        }
        
        // Prepare data for update
        $data = array();
        $format = array();
        
        if (isset($product_data['product_name'])) {
            $data['product_name'] = $product_data['product_name'];
            $format[] = '%s';
        }
        
        if (isset($product_data['product_code'])) {
            // Check if new code exists for other products
            if ($product_data['product_code'] !== $product->product_code) {
                $exists = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT id FROM {$wpdb->prefix}rena_supplier_products WHERE product_code = %s AND id != %d",
                        $product_data['product_code'],
                        $product_id
                    )
                );
                
                if ($exists) {
                    return new WP_Error('duplicate_product_code', __('Product code already exists', 'rena-multisupplier'));
                }
            }
            
            $data['product_code'] = $product_data['product_code'];
            $format[] = '%s';
        }
        
        if (isset($product_data['product_sku'])) {
            $data['product_sku'] = $product_data['product_sku'];
            $format[] = '%s';
        }
        
        if (isset($product_data['product_description'])) {
            $data['product_description'] = $product_data['product_description'];
            $format[] = '%s';
        }
        
        if (isset($product_data['product_price'])) {
            $data['product_price'] = (float) $product_data['product_price'];
            $format[] = '%f';
        }
        
        if (isset($product_data['product_stock'])) {
            $data['product_stock'] = (int) $product_data['product_stock'];
            $format[] = '%d';
        }
        
        if (isset($product_data['product_image'])) {
            $data['product_image'] = $product_data['product_image'];
            $format[] = '%s';
        }
        
        if (empty($data)) {
            return true; // No data to update
        }
        
        // Update data
        $result = $wpdb->update(
            $wpdb->prefix . 'rena_supplier_products',
            $data,
            array('id' => $product_id),
            $format,
            array('%d')
        );
        
        if ($result === false) {
            return new WP_Error('db_update_error', $wpdb->last_error);
        }
        
        return true;
    }
    
    /**
     * Delete supplier product.
     *
     * @param int $product_id Product ID.
     * @return bool|WP_Error
     */
    public function delete_product($product_id) {
        if (!$this->id) {
            return new WP_Error('invalid_supplier', __('Invalid supplier', 'rena-multisupplier'));
        }
        
        global $wpdb;
        
        $result = $wpdb->delete(
            $wpdb->prefix . 'rena_supplier_products',
            array(
                'id' => $product_id,
                'supplier_id' => $this->id
            ),
            array('%d', '%d')
        );
        
        if ($result === false) {
            return new WP_Error('db_delete_error', $wpdb->last_error);
        }
        
        return true;
    }
    
    /**
     * Import products from CSV.
     *
     * @param string $file      File path.
     * @param array  $mapping   Column mapping.
     * @param bool   $has_header Whether the CSV has a header row.
     * @return array Array of success count, error count, and errors.
     */
    public function import_products_from_csv($file, $mapping, $has_header = true) {
        if (!$this->id) {
            return array(
                'success' => 0,
                'error' => 0,
                'errors' => array(
                    new WP_Error('invalid_supplier', __('Invalid supplier', 'rena-multisupplier'))
                )
            );
        }
        
        if (!file_exists($file)) {
            return array(
                'success' => 0,
                'error' => 0,
                'errors' => array(
                    new WP_Error('invalid_file', __('Invalid file', 'rena-multisupplier'))
                )
            );
        }
        
        $handle = fopen($file, 'r');
        
        if (!$handle) {
            return array(
                'success' => 0,
                'error' => 0,
                'errors' => array(
                    new WP_Error('file_open_error', __('Could not open file', 'rena-multisupplier'))
                )
            );
        }
        
        $results = array(
            'success' => 0,
            'error' => 0,
            'errors' => array()
        );
        
        $row_number = 0;
        
        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            $row_number++;
            
            // Skip header row
            if ($has_header && $row_number === 1) {
                continue;
            }
            
            $product_data = array();
            
            foreach ($mapping as $field => $column_index) {
                if (isset($data[$column_index])) {
                    $product_data[$field] = $data[$column_index];
                }
            }
            
            $result = $this->add_product($product_data);
            
            if (is_wp_error($result)) {
                $results['error']++;
                $results['errors'][] = array(
                    'row' => $row_number,
                    'error' => $result
                );
            } else {
                $results['success']++;
            }
        }
        
        fclose($handle);
        
        return $results;
    }
} 