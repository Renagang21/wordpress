<?php
// 네임스페이스 선언은 첫 번째 줄에 와야 합니다 (declare 구문 이후)
namespace RenaMembers\Core;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main initialization class
 */
class Init {
    /**
     * Instance variable
     * @var Init
     */
    private static $instance = null;
    
    /**
     * Form handler instance
     * @var Form
     */
    public $form;
    
    /**
     * User handler instance
     * @var User
     */
    public $user;
    
    /**
     * Access controller instance
     * @var Access
     */
    public $access;
    
    /**
     * Admin instance
     * @var \RenaMembers\Admin\Admin
     */
    public $admin;
    
    /**
     * Get the singleton instance
     * @return Init
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Set up components
        $this->setup_globals();
        $this->includes();
        $this->setup_components();
        $this->setup_hooks();
    }
    
    /**
     * Setup global variables
     */
    private function setup_globals() {
        // Define global variables here
    }
    
    /**
     * Include required files
     */
    private function includes() {
        // Include admin files if in admin area
        if (is_admin()) {
            require_once RENA_MEMBERS_PATH . 'includes/admin/class-admin.php';
        }
    }
    
    /**
     * Initialize components
     */
    private function setup_components() {
        $this->form = new Form();
        $this->user = new User();
        $this->access = new Access();
        
        // Initialize admin if in admin area
        if (is_admin()) {
            $this->admin = new \RenaMembers\Admin\Admin();
        }
    }
    
    /**
     * Setup WordPress hooks
     */
    private function setup_hooks() {
        // Register activation hooks
        register_activation_hook(RENA_MEMBERS_PATH . 'rena-members.php', array($this, 'activation'));
        
        // Add init hook
        add_action('init', array($this, 'init'), 0);
        
        // Add assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Plugin activation
     */
    public function activation() {
        // Activation tasks
    }
    
    /**
     * WordPress init hook
     */
    public function init() {
        // Register post types
        $this->register_post_types();
        
        // Register shortcodes
        $this->register_shortcodes();
    }
    
    /**
     * Register custom post types
     */
    private function register_post_types() {
        // Register custom post types if needed
    }
    
    /**
     * Register shortcodes
     */
    private function register_shortcodes() {
        add_shortcode('rena_login', array($this, 'login_shortcode'));
        add_shortcode('rena_register', array($this, 'register_shortcode'));
        add_shortcode('rena_profile', array($this, 'profile_shortcode'));
    }
    
    /**
     * Login form shortcode
     */
    public function login_shortcode($atts) {
        // Return login form HTML
        ob_start();
        include RENA_MEMBERS_PATH . 'templates/login-form.php';
        return ob_get_clean();
    }
    
    /**
     * Registration form shortcode
     */
    public function register_shortcode($atts) {
        // Return registration form HTML
        ob_start();
        include RENA_MEMBERS_PATH . 'templates/register-form.php';
        return ob_get_clean();
    }
    
    /**
     * Profile form shortcode
     */
    public function profile_shortcode($atts) {
        // Return profile form HTML
        ob_start();
        include RENA_MEMBERS_PATH . 'templates/profile-form.php';
        return ob_get_clean();
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Register and enqueue styles
        wp_register_style('rena-members', RENA_MEMBERS_URL . 'assets/css/rena-members.css', array(), RENA_MEMBERS_VERSION);
        wp_enqueue_style('rena-members');
        
        // Register and enqueue scripts
        wp_register_script('rena-members', RENA_MEMBERS_URL . 'assets/js/rena-members.js', array('jquery'), RENA_MEMBERS_VERSION, true);
        wp_enqueue_script('rena-members');
        
        // Localize script
        wp_localize_script('rena-members', 'renaMembers', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rena-members-nonce')
        ));
    }
}