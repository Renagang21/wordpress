<?php

namespace RenaMembers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin class
 * 관리자 영역의 기능을 처리하는 클래스
 */
class Admin
{
    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_post_meta'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add admin menu pages
     */
    public function add_menu_pages()
    {
        // 메인 메뉴
        add_menu_page(
            __('Rena Members', 'rena-members'),
            __('Rena Members', 'rena-members'),
            'manage_options',
            'rena-members',
            array($this, 'main_page'),
            'dashicons-groups',
            30
        );

        // 대시보드 (메인 페이지)
        add_submenu_page(
            'rena-members',
            __('Dashboard', 'rena-members'),
            __('Dashboard', 'rena-members'),
            'manage_options',
            'rena-members',
            array($this, 'main_page')
        );

        // 사용자 관리
        add_submenu_page(
            'rena-members',
            __('Users', 'rena-members'),
            __('Users', 'rena-members'),
            'manage_options',
            'rena-members-users',
            array($this, 'users_page')
        );

        // 역할 관리
        add_submenu_page(
            'rena-members',
            __('Roles', 'rena-members'),
            __('Roles', 'rena-members'),
            'manage_options',
            'rena-members-roles',
            array($this, 'roles_page')
        );

        // 설정
        add_submenu_page(
            'rena-members',
            __('Settings', 'rena-members'),
            __('Settings', 'rena-members'),
            'manage_options',
            'rena-members-settings',
            array($this, 'settings_page')
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_scripts($hook)
    {
        if (strpos($hook, 'rena-members') !== false) {
            wp_enqueue_style(
                'rena-members-admin',
                RENA_MEMBERS_URL . 'assets/css/admin.css',
                array(),
                RENA_MEMBERS_VERSION
            );

            wp_enqueue_script(
                'rena-members-admin',
                RENA_MEMBERS_URL . 'assets/js/admin.js',
                array('jquery'),
                RENA_MEMBERS_VERSION,
                true
            );

            wp_localize_script('rena-members-admin', 'renaMembersAdmin', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('rena-members-admin')
            ));
        }
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes()
    {
        $post_types = get_post_types(array('public' => true));

        foreach ($post_types as $post_type) {
            add_meta_box(
                'rena_members_access',
                __('Access Control', 'rena-members'),
                array($this, 'render_access_meta_box'),
                $post_type,
                'side',
                'high'
            );
        }
    }

    /**
     * Render access control meta box
     */
    public function render_access_meta_box($post)
    {
        wp_nonce_field('rena_members_access_meta_box', 'rena_members_access_nonce');

        $restricted = get_post_meta($post->ID, '_rena_members_restricted', true);
        $allowed_roles = get_post_meta($post->ID, '_rena_members_allowed_roles', true);
        if (!is_array($allowed_roles)) {
            $allowed_roles = array();
        }

?>
        <p>
            <label>
                <input type="checkbox" name="rena_members_restricted" value="1"
                    <?php checked($restricted, '1'); ?>>
                <?php _e('Members Only Content', 'rena-members'); ?>
            </label>
        </p>

        <p><strong><?php _e('Allowed Roles:', 'rena-members'); ?></strong></p>
        <?php
        $roles = get_editable_roles();
        foreach ($roles as $role_id => $role) {
        ?>
            <label>
                <input type="checkbox" name="rena_members_allowed_roles[]"
                    value="<?php echo esc_attr($role_id); ?>"
                    <?php checked(in_array($role_id, $allowed_roles)); ?>>
                <?php echo esc_html($role['name']); ?>
            </label><br>
<?php
        }
    }

    /**
     * Save post meta
     */
    public function save_post_meta($post_id)
    {
        if (
            !isset($_POST['rena_members_access_nonce']) ||
            !wp_verify_nonce($_POST['rena_members_access_nonce'], 'rena_members_access_meta_box')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $restricted = isset($_POST['rena_members_restricted']) ? '1' : '';
        update_post_meta($post_id, '_rena_members_restricted', $restricted);

        $allowed_roles = isset($_POST['rena_members_allowed_roles']) ?
            array_map('sanitize_text_field', $_POST['rena_members_allowed_roles']) :
            array();
        update_post_meta($post_id, '_rena_members_allowed_roles', $allowed_roles);
    }

    /**
     * Register plugin settings
     */
    public function register_settings()
    {
        register_setting('rena_members_settings', 'rena_members_login_redirect');
        register_setting('rena_members_settings', 'rena_members_register_redirect');
        register_setting('rena_members_settings', 'rena_members_restricted_message');
        register_setting('rena_members_settings', 'rena_members_auto_login');
    }

    /**
     * Main admin page (Dashboard)
     */
    public function main_page()
    {
        require_once RENA_MEMBERS_PATH . 'templates/admin/dashboard.php';
    }

    /**
     * Users management page
     */
    public function users_page()
    {
        require_once RENA_MEMBERS_PATH . 'templates/admin/users.php';
    }

    /**
     * Roles management page
     */
    public function roles_page()
    {
        require_once RENA_MEMBERS_PATH . 'templates/admin/roles.php';
    }

    /**
     * Settings page
     */
    public function settings_page()
    {
        require_once RENA_MEMBERS_PATH . 'templates/admin/settings.php';
    }

    /**
     * Get recent users
     */
    private function get_recent_users($limit = 5)
    {
        $args = array(
            'number' => $limit,
            'orderby' => 'registered',
            'order' => 'DESC'
        );

        return get_users($args);
    }

    /**
     * Get user statistics
     */
    private function get_user_stats()
    {
        $stats = count_users();

        return array(
            'total' => $stats['total_users'],
            'roles' => $stats['avail_roles']
        );
    }

    /**
     * Format date for display
     */
    private function format_date($date_string)
    {
        $timestamp = strtotime($date_string);
        return array(
            'display' => date_i18n(get_option('date_format'), $timestamp),
            'relative' => human_time_diff($timestamp, current_time('timestamp')) . ' ' . __('ago', 'rena-members')
        );
    }
}
