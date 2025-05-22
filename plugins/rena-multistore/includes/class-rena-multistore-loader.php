<?php

/**
 * The core plugin loader class.
 */
class Rena_Multistore_Loader {

    /**
     * The array of actions registered with WordPress.
     */
    protected $actions;

    /**
     * The array of filters registered with WordPress.
     */
    protected $filters;

    /**
     * Initialize the collections used to maintain the actions and filters.
     */
    public function __construct() {
        $this->actions = array();
        $this->filters = array();

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
      *Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        // Admin area functionality
        require_once RENA_MULTISTORE_PLUGIN_DIR . 'admin/class-rena-multistore-admin.php';

        // Public area functionality
        require_once RENA_MULTISTORE_PLUGIN_DIR . 'public/class-rena-multistore-public.php';
    }

    /**
     * Register the filters and actions with WordPress.
     */
    public function run() {
        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }

        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->actions[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->filters[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );
    }

    /**
     * Define the admin area hooks
     */
    private function define_admin_hooks() {
        $plugin_admin = new Rena_Multistore_Admin();
        
        // Add admin hooks here
        $this->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->add_action('admin_menu', $plugin_admin, 'add_menu_pages');
    }

    /**
     * Define the public area hooks
     */
    private function define_public_hooks() {
        $plugin_public = new Rena_Multistore_Public();
        
        // Add public hooks here
        $this->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        
        // Add routing hooks
        $this->add_action('init', $plugin_public, 'register_rewrite_rules');
        $this->add_filter('query_vars', $plugin_public, 'add_query_vars');
        $this->add_action('template_redirect', $plugin_public, 'template_redirect');

        // Add seller dashboard hooks
        $this->add_action('init', function() {
            add_rewrite_rule(
                '^seller-dashboard/?$',
                'index.php?pagename=seller-dashboard',
                'top'
            );
        });
        
        $this->add_action('template_redirect', function() use ($plugin_public) {
            if (is_page('seller-dashboard')) {
                $plugin_public->display_seller_dashboard();
                exit;
            }
        });
    }
} 