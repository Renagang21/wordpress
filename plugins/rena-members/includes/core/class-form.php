
<?php
namespace RenaMembers\Core;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Form handling class
 */
class Form {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_ajax_rena_members_form_submit', array($this, 'form_submit_handler'));
        add_action('wp_ajax_nopriv_rena_members_form_submit', array($this, 'form_submit_handler'));
    }
    
    /**
     * Initialize form hooks
     */
    public function init() {
        // Set up form hooks
    }
    
    /**
     * Handle form submissions
     */
    public function form_submit_handler() {
        // Verify nonce
        check_ajax_referer('rena-members-nonce', 'nonce');
        
        $response = array(
            'success' => false,
            'message' => __('An error occurred.', 'rena-members')
        );
        
        $form_id = isset($_POST['form_id']) ? sanitize_text_field($_POST['form_id']) : '';
        
        switch ($form_id) {
            case 'login':
                $response = $this->process_login_form();
                break;
                
            case 'register':
                $response = $this->process_registration_form();
                break;
                
            case 'profile':
                $response = $this->process_profile_form();
                break;
                
            default:
                $response['message'] = __('Invalid form.', 'rena-members');
                break;
        }
        
        wp_send_json($response);
    }
    
    /**
     * Process login form
     * 
     * @return array
     */
    private function process_login_form() {
        $response = array(
            'success' => false,
            'message' => __('Login failed.', 'rena-members')
        );
        
        $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $remember = isset($_POST['remember']) ? (bool) $_POST['remember'] : false;
        
        if (empty($username) || empty($password)) {
            $response['message'] = __('Username and password are required.', 'rena-members');
            return $response;
        }
        
        $credentials = array(
            'user_login' => $username,
            'user_password' => $password,
            'remember' => $remember
        );
        
        $user = wp_signon($credentials, is_ssl());
        
        if (is_wp_error($user)) {
            $response['message'] = $user->get_error_message();
            return $response;
        }
        
        $response['success'] = true;
        $response['message'] = __('Login successful. Redirecting...', 'rena-members');
        $response['redirect'] = home_url();
        
        return $response;
    }
    
    /**
     * Process registration form
     * 
     * @return array
     */
    private function process_registration_form() {
        $response = array(
            'success' => false,
            'message' => __('Registration failed.', 'rena-members')
        );
        
        // Basic form validation
        $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        if (empty($username) || empty($email) || empty($password)) {
            $response['message'] = __('All fields are required.', 'rena-members');
            return $response;
        }
        
        if (!is_email($email)) {
            $response['message'] = __('Invalid email address.', 'rena-members');
            return $response;
        }
        
        if (username_exists($username)) {
            $response['message'] = __('Username already exists.', 'rena-members');
            return $response;
        }
        
        if (email_exists($email)) {
            $response['message'] = __('Email already exists.', 'rena-members');
            return $response;
        }
        
        // Create user
        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            $response['message'] = $user_id->get_error_message();
            return $response;
        }
        
        // Set user role
        $user = new \WP_User($user_id);
        $user->set_role('subscriber');
        
        // Save additional fields
        foreach ($_POST as $key => $value) {
            if (!in_array($key, array('username', 'email', 'password', 'form_id', 'action', 'nonce'))) {
                update_user_meta($user_id, $key, sanitize_text_field($value));
            }
        }
        
        // Auto login user
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        
        $response['success'] = true;
        $response['message'] = __('Registration successful. Redirecting...', 'rena-members');
        $response['redirect'] = home_url();
        
        return $response;
    }
    
    /**
     * Process profile form
     * 
     * @return array
     */
    private function process_profile_form() {
        $response = array(
            'success' => false,
            'message' => __('Profile update failed.', 'rena-members')
        );
        
        // Check if user is logged in
        if (!is_user_logged_in()) {
            $response['message'] = __('You must be logged in to update your profile.', 'rena-members');
            return $response;
        }
        
        $user_id = get_current_user_id();
        $data = array();
        
        // Process form fields
        foreach ($_POST as $key => $value) {
            if (!in_array($key, array('form_id', 'action', 'nonce'))) {
                $data[$key] = sanitize_text_field($value);
            }
        }
        
        // Update user
        $user = Init::instance()->user;
        $updated = $user->update_profile($user_id, $data);
        
        if ($updated) {
            $response['success'] = true;
            $response['message'] = __('Profile updated successfully.', 'rena-members');
        }
        
        return $response;
    }
}