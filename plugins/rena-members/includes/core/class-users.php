<?php
namespace RenaMembers\Core;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * User management class
 */
class User {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    /**
     * Initialize user functions
     */
    public function init() {
        // Set up user hooks
    }
    
    /**
     * Get user data
     * 
     * @param int $user_id
     * @return array|false
     */
    public function get_user_data($user_id) {
        $user_data = get_userdata($user_id);
        if (!$user_data) {
            return false;
        }
        
        return array(
            'ID' => $user_id,
            'user_login' => $user_data->user_login,
            'user_email' => $user_data->user_email,
            'display_name' => $user_data->display_name,
            // Add more fields as needed
        );
    }
    
    /**
     * Update user profile
     * 
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    public function update_profile($user_id, $data) {
        if (!$this->can_edit_profile($user_id)) {
            return false;
        }
        
        // Filter data to update
        $update_data = array();
        if (isset($data['display_name'])) {
            $update_data['display_name'] = sanitize_text_field($data['display_name']);
        }
        
        if (isset($data['user_email']) && is_email($data['user_email'])) {
            $update_data['user_email'] = sanitize_email($data['user_email']);
        }
        
        // Update user
        if (!empty($update_data)) {
            $update_data['ID'] = $user_id;
            $result = wp_update_user($update_data);
            
            if (is_wp_error($result)) {
                return false;
            }
            
            // Update custom meta fields
            foreach ($data as $key => $value) {
                if (!in_array($key, array('display_name', 'user_email', 'ID'))) {
                    update_user_meta($user_id, $key, $value);
                }
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if current user can edit profile
     * 
     * @param int $user_id
     * @return bool
     */
    public function can_edit_profile($user_id) {
        $current_user_id = get_current_user_id();
        
        // Allow user to edit their own profile
        if ($current_user_id === $user_id) {
            return true;
        }
        
        // Allow admins to edit any profile
        if (current_user_can('edit_users')) {
            return true;
        }
        
        return false;
    }
}