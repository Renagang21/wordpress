<?php
namespace RenaMembers\Core;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Setup class for database tables and initial configuration
 */
class Setup {
    /**
     * Run setup tasks
     */
    public static function setup() {
        self::create_tables();
        self::set_default_options();
    }
    
    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Form table
        $forms_table = $wpdb->prefix . 'rena_members_forms';
        
        $sql = "CREATE TABLE $forms_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            form_key varchar(100) NOT NULL,
            form_title varchar(255) NOT NULL,
            form_mode varchar(20) NOT NULL,
            form_type varchar(20) NOT NULL,
            form_fields longtext NOT NULL,
            form_settings longtext NOT NULL,
            date_created datetime NOT NULL,
            date_updated datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY form_key (form_key)
        ) $charset_collate;";
        
        // User meta table
        $meta_table = $wpdb->prefix . 'rena_members_user_meta';
        
        $sql .= "CREATE TABLE $meta_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            meta_key varchar(255) NOT NULL,
            meta_value longtext NOT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY meta_key (meta_key)
        ) $charset_collate;";
        
        // Include WordPress database upgrade functions
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        // Plugin version
        add_option('rena_members_version', RENA_MEMBERS_VERSION);
        
        // Default settings
        add_option('rena_members_login_redirect', '');
        add_option('rena_members_registration_redirect', '');
        add_option('rena_members_default_role', 'subscriber');
        add_option('rena_members_auto_login', 1);
        
        // Create default forms if they don't exist
        self::create_default_forms();
    }
    
    /**
     * Create default forms
     */
    private static function create_default_forms() {
        global $wpdb;
        $forms_table = $wpdb->prefix . 'rena_members_forms';
        
        // Check if default login form exists
        $login_form = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $forms_table WHERE form_key = %s", 'login')
        );
        
        // Create default login form if it doesn't exist
        if (!$login_form) {
            $wpdb->insert(
                $forms_table,
                array(
                    'form_key' => 'login',
                    'form_title' => 'Login Form',
                    'form_mode' => 'default',
                    'form_type' => 'login',
                    'form_fields' => json_encode(self::get_default_login_fields()),
                    'form_settings' => json_encode(self::get_default_form_settings('login')),
                    'date_created' => current_time('mysql'),
                    'date_updated' => current_time('mysql')
                )
            );
        }
        
        // Check if default registration form exists
        $register_form = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $forms_table WHERE form_key = %s", 'register')
        );
        
        // Create default registration form if it doesn't exist
        if (!$register_form) {
            $wpdb->insert(
                $forms_table,
                array(
                    'form_key' => 'register',
                    'form_title' => 'Registration Form',
                    'form_mode' => 'default',
                    'form_type' => 'register',
                    'form_fields' => json_encode(self::get_default_register_fields()),
                    'form_settings' => json_encode(self::get_default_form_settings('register')),
                    'date_created' => current_time('mysql'),
                    'date_updated' => current_time('mysql')
                )
            );
        }
        
        // Check if default profile form exists
        $profile_form = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $forms_table WHERE form_key = %s", 'profile')
        );
        
        // Create default profile form if it doesn't exist
        if (!$profile_form) {
            $wpdb->insert(
                $forms_table,
                array(
                    'form_key' => 'profile',
                    'form_title' => 'Profile Form',
                    'form_mode' => 'default',
                    'form_type' => 'profile',
                    'form_fields' => json_encode(self::get_default_profile_fields()),
                    'form_settings' => json_encode(self::get_default_form_settings('profile')),
                    'date_created' => current_time('mysql'),
                    'date_updated' => current_time('mysql')
                )
            );
        }
    }
    
    /**
     * Get default login form fields
     * 
     * @return array
     */
    private static function get_default_login_fields() {
        return array(
            'username' => array(
                'type' => 'text',
                'label' => 'Username or Email',
                'required' => 1,
                'public' => 1,
                'editable' => 0,
                'position' => 1
            ),
            'password' => array(
                'type' => 'password',
                'label' => 'Password',
                'required' => 1,
                'public' => 1,
                'editable' => 0,
                'position' => 2
            ),
            'remember' => array(
                'type' => 'checkbox',
                'label' => 'Remember Me',
                'required' => 0,
                'public' => 1,
                'editable' => 0,
                'position' => 3
            )
        );
    }
    
    /**
     * Get default registration form fields
     * 
     * @return array
     */
    private static function get_default_register_fields() {
        return array(
            'username' => array(
                'type' => 'text',
                'label' => 'Username',
                'required' => 1,
                'public' => 1,
                'editable' => 0,
                'position' => 1
            ),
            'email' => array(
                'type' => 'email',
                'label' => 'Email',
                'required' => 1,
                'public' => 1,
                'editable' => 1,
                'position' => 2
            ),
            'password' => array(
                'type' => 'password',
                'label' => 'Password',
                'required' => 1,
                'public' => 1,
                'editable' => 1,
                'position' => 3
            ),
            'confirm_password' => array(
                'type' => 'password',
                'label' => 'Confirm Password',
                'required' => 1,
                'public' => 1,
                'editable' => 0,
                'position' => 4
            )
        );
    }
    
    /**
     * Get default profile form fields
     * 
     * @return array
     */
    private static function get_default_profile_fields() {
        return array(
            'display_name' => array(
                'type' => 'text',
                'label' => 'Display Name',
                'required' => 1,
                'public' => 1,
                'editable' => 1,
                'position' => 1
            ),
            'user_email' => array(
                'type' => 'email',
                'label' => 'Email',
                'required' => 1,
                'public' => 1,
                'editable' => 1,
                'position' => 2
            ),
            'user_url' => array(
                'type' => 'url',
                'label' => 'Website',
                'required' => 0,
                'public' => 1,
                'editable' => 1,
                'position' => 3
            ),
            'description' => array(
                'type' => 'textarea',
                'label' => 'Biographical Info',
                'required' => 0,
                'public' => 1,
                'editable' => 1,
                'position' => 4
            )
        );
    }
    
    /**
     * Get default form settings
     * 
     * @param string $form_type
     * @return array
     */
    private static function get_default_form_settings($form_type) {
        $settings = array(
            'form_title' => 1,
            'form_css_class' => '',
            'form_style' => 'default'
        );
        
        switch ($form_type) {
            case 'login':
                $settings['redirect_url'] = '';
                $settings['show_rememberme'] = 1;
                $settings['show_forgot_password'] = 1;
                $settings['show_register_link'] = 1;
                break;
                
            case 'register':
                $settings['redirect_url'] = '';
                $settings['auto_login'] = 1;
                $settings['role'] = 'subscriber';
                $settings['show_login_link'] = 1;
                break;
                
            case 'profile':
                $settings['allow_account_deletion'] = 0;
                break;
        }
        
        return $settings;
    }
}