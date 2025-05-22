<?php

/**
 * The core plugin class.
 *
 * This is used to define admin-specific hooks and public-facing site hooks.
 */
class Rena_Multisupplier_Loader {

    /**
     * The array of actions registered with WordPress.
     */
    protected $actions;

    /**
     * The array of filters registered with WordPress.
     */
    protected $filters;

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    /**
     * WooCommerce REST API endpoint.
     */
    private $api_endpoint;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {
        $this->plugin_name = 'rena-multisupplier';
        $this->version = RENA_MULTISUPPLIER_VERSION;
        $this->actions = array();
        $this->filters = array();
        
        // WooCommerce REST API endpoint
        $this->api_endpoint = get_option('rena_multisupplier_wc_api_endpoint', 'https://example.com/wp-json/wc/v3');

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        // Admin classes
        require_once RENA_MULTISUPPLIER_PLUGIN_DIR . 'includes/admin/class-rena-multisupplier-admin.php';
        
        // Public classes
        require_once RENA_MULTISUPPLIER_PLUGIN_DIR . 'includes/frontend/class-rena-multisupplier-public.php';
        
        // Core functionality
        require_once RENA_MULTISUPPLIER_PLUGIN_DIR . 'includes/core/class-rena-multisupplier-supplier.php';
        require_once RENA_MULTISUPPLIER_PLUGIN_DIR . 'includes/core/class-rena-multisupplier-product.php';
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     */
    private function define_admin_hooks() {
        $plugin_admin = new Rena_Multisupplier_Admin($this->plugin_name, $this->version);

        // Admin assets
        $this->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        // Admin menus
        $this->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        
        // Register meta boxes
        $this->add_action('add_meta_boxes', $plugin_admin, 'register_meta_boxes');
        
        // Save meta boxes
        $this->add_action('save_post', $plugin_admin, 'save_meta_boxes', 10, 2);
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     */
    private function define_public_hooks() {
        $plugin_public = new Rena_Multisupplier_Public($this->plugin_name, $this->version, $this->api_endpoint);

        $this->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        
        // Register shortcodes
        add_shortcode('rena_supplier_products', array($plugin_public, 'supplier_products_shortcode'));
        
        // Template overrides
        $this->add_filter('template_include', $plugin_public, 'template_include');
        
        // Init hooks
        $this->add_action('init', $this, 'register_post_types');
        $this->add_action('init', $this, 'register_taxonomies');
        
        // REST API endpoints
        $this->add_action('rest_api_init', $plugin_public, 'register_rest_endpoints');
    }

    /**
     * Register the custom post types.
     */
    public function register_post_types() {
        // Supplier post type
        register_post_type('rena_supplier', 
            array(
                'labels' => array(
                    'name' => '공급업체',
                    'singular_name' => '공급업체',
                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
                'rewrite' => array('slug' => 'supplier'),
                'menu_icon' => 'dashicons-store',
                'show_in_rest' => true,
            )
        );
    }

    /**
     * Register the custom taxonomies.
     */
    public function register_taxonomies() {
        // Supplier category taxonomy
        register_taxonomy('supplier_category', 'rena_supplier', 
            array(
                'labels' => array(
                    'name' => '공급업체 카테고리',
                    'singular_name' => '공급업체 카테고리',
                ),
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'rewrite' => array('slug' => 'supplier-category'),
            )
        );
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @param string $hook          The name of the WordPress action that is being registered.
     * @param object $component     A reference to the instance of the object on which the action is defined.
     * @param string $callback      The name of the function definition on the $component.
     * @param int    $priority      Optional. The priority at which the function should be fired. Default is 10.
     * @param int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @param string $hook          The name of the WordPress filter that is being registered.
     * @param object $component     A reference to the instance of the object on which the filter is defined.
     * @param string $callback      The name of the function definition on the $component.
     * @param int    $priority      Optional. The priority at which the function should be fired. Default is 10.
     * @param int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @param array  $hooks         The collection of hooks that is being registered (that is, actions or filters).
     * @param string $hook          The name of the WordPress filter that is being registered.
     * @param object $component     A reference to the instance of the object on which the filter is defined.
     * @param string $callback      The name of the function definition on the $component.
     * @param int    $priority      The priority at which the function should be fired.
     * @param int    $accepted_args The number of arguments that should be passed to the $callback.
     *
     * @return array The collection of actions and filters registered with WordPress.
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        // Register all actions
        foreach ($this->actions as $hook) {
            add_action(
                $hook['hook'], 
                array($hook['component'], $hook['callback']), 
                $hook['priority'], 
                $hook['accepted_args']
            );
        }

        // Register all filters
        foreach ($this->filters as $hook) {
            add_filter(
                $hook['hook'], 
                array($hook['component'], $hook['callback']), 
                $hook['priority'], 
                $hook['accepted_args']
            );
        }
    }
} 