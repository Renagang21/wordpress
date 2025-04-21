
<?php
namespace RenaMembers\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Frontend class
 */
class Frontend {
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('init', array($this, 'register_shortcodes'));
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Register styles
        wp_register_style('rena-members', RENA_MEMBERS_URL . 'assets/css/rena-members.css', array(), RENA_MEMBERS_VERSION);
        
        // Register scripts
        wp_register_script('rena-members', RENA_MEMBERS_URL . 'assets/js/rena-members.js', array('jquery'), RENA_MEMBERS_VERSION, true);
        
        // Localize script
        wp_localize_script('rena-members', 'renaMembers', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rena-members-nonce')
        ));
    }
    
    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('rena_login', array($this, 'login_shortcode'));
        add_shortcode('rena_register', array($this, 'register_shortcode'));
        add_shortcode('rena_profile', array($this, 'profile_shortcode'));
        add_shortcode('rena_account', array($this, 'account_shortcode'));
    }
    
    /**
     * Login form shortcode
     * 
     * @param array $atts
     * @return string
     */
    public function login_shortcode($atts) {
        // Enqueue necessary styles and scripts
        wp_enqueue_style('rena-members');
        wp_enqueue_script('rena-members');
        
        // Parse attributes
        $atts = shortcode_atts(array(
            'redirect' => '',
            'form_id' => 'login'
        ), $atts);
        
        // Start output buffering
        ob_start();
        
        // Include template
        include RENA_MEMBERS_PATH . 'templates/login-form.php';
        
        // Return the buffered content
        return ob_get_clean();
    }
    
    /**
     * Registration form shortcode
     * 
     * @param array $atts
     * @return string
     */
    public function register_shortcode($atts) {
        // Enqueue necessary styles and scripts
        wp_enqueue_style('rena-members');
        wp_enqueue_script('rena-members');
        
        // Parse attributes
        $atts = shortcode_atts(array(
            'redirect' => '',
            'form_id' => 'register'
        ), $atts);
        
        // Start output buffering
        ob_start();
        
        // Include template
        include RENA_MEMBERS_PATH . 'templates/register-form.php';
        
        // Return the buffered content
        return ob_get_clean();
    }
    
    /**
     * Profile form shortcode
     * 
     * @param array $atts
     * @return string
     */
    public function profile_shortcode($atts) {
        // Enqueue necessary styles and scripts
        wp_enqueue_style('rena-members');
        wp_enqueue_script('rena-members');
        
        // Parse attributes
        $atts = shortcode_atts(array(
            'form_id' => 'profile'
        ), $atts);
        
        // Start output buffering
        ob_start();
        
        // Include template
        include RENA_MEMBERS_PATH . 'templates/profile-form.php';
        
        // Return the buffered content
        return ob_get_clean();
    }
    
    /**
     * Account page shortcode
     * 
     * @param array $atts
     * @return string
     */
    public function account_shortcode($atts) {
        // Enqueue necessary styles and scripts
        wp_enqueue_style('rena-members');
        wp_enqueue_script('rena-members');
        
        // Parse attributes
        $atts = shortcode_atts(array(), $atts);
        
        // Start output buffering
        ob_start();
        
        // If user is not logged in, show login form
        if (!is_user_logged_in()) {
            include RENA_MEMBERS_PATH . 'templates/login-form.php';
        } else {
            // Include account template
            include RENA_MEMBERS_PATH . 'templates/account.php';
        }
        
        // Return the buffered content
        return ob_get_clean();
    }
}

// Initialize frontend
new Frontend();