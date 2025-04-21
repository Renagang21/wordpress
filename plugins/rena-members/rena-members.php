<?php

/**
 * Plugin Name: Rena Members
 * Plugin URI: https://github.com/Renagang21/rena-members
 * Description: Advanced membership plugin for WordPress
 * Version: 0.1.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: 서철환
 * Author URI: https://github.com/Renagang21
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rena-members
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('RENA_MEMBERS_VERSION', '0.1.0');
define('RENA_MEMBERS_PATH', plugin_dir_path(__FILE__));
define('RENA_MEMBERS_URL', plugin_dir_url(__FILE__));
define('RENA_MEMBERS_BASENAME', plugin_basename(__FILE__));

// Hook for activation
register_activation_hook(__FILE__, 'rena_members_activation');
function rena_members_activation()
{
    // Activation tasks
    flush_rewrite_rules();
}

// Hook for deactivation
register_deactivation_hook(__FILE__, 'rena_members_deactivation');
function rena_members_deactivation()
{
    // Deactivation tasks
    flush_rewrite_rules();
}

// Initialize plugin
function rena_members_init()
{
    // Load textdomain
    load_plugin_textdomain('rena-members', false, dirname(RENA_MEMBERS_BASENAME) . '/languages');

    // Add admin menu
    if (is_admin()) {
        add_action('admin_menu', 'rena_members_admin_menu');
    }
}
add_action('plugins_loaded', 'rena_members_init');

// Add admin menu
function rena_members_admin_menu()
{
    // 메인 메뉴
    add_menu_page(
        'Rena Members',
        'Rena Members',
        'manage_options',
        'rena-members',
        'rena_members_dashboard_page',
        'dashicons-groups',
        30
    );

    // 대시보드 (메인 페이지)
    add_submenu_page(
        'rena-members',
        '대시보드',
        '대시보드',
        'manage_options',
        'rena-members',
        'rena_members_dashboard_page'
    );

    // 사용자 목록
    add_submenu_page(
        'rena-members',
        '사용자 목록',
        '사용자 목록',
        'manage_options',
        'rena-members-users',
        'rena_members_users_page'
    );

    // 역할 관리
    add_submenu_page(
        'rena-members',
        '역할',
        '역할',
        'manage_options',
        'rena-members-roles',
        'rena_members_roles_page'
    );

    // 설정
    add_submenu_page(
        'rena-members',
        '설정',
        '설정',
        'manage_options',
        'rena-members-settings',
        'rena_members_settings_page'
    );
}

// 대시보드 페이지
function rena_members_dashboard_page()
{
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div class="rena-members-dashboard">
            <div class="rena-members-stats">
                <h2>사용자 통계</h2>
                <p>총 사용자 수: <?php echo count_users()['total_users']; ?></p>
                <p>최근 가입: <?php echo get_recent_user(); ?></p>
            </div>
        </div>
    </div>
<?php
}

// 사용자 목록 페이지
function rena_members_users_page()
{
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div class="rena-members-users">
            <?php
            // WP_User_Query를 사용하여 사용자 목록 표시
            $users_query = new WP_User_Query([
                'orderby' => 'registered',
                'order' => 'DESC'
            ]);

            $users = $users_query->get_results();
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>사용자명</th>
                        <th>이메일</th>
                        <th>역할</th>
                        <th>가입일</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo esc_html($user->display_name); ?></td>
                            <td><?php echo esc_html($user->user_email); ?></td>
                            <td><?php echo esc_html(implode(', ', $user->roles)); ?></td>
                            <td><?php echo esc_html($user->user_registered); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}

// 역할 관리 페이지
function rena_members_roles_page()
{
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div class="rena-members-roles">
            <?php
            global $wp_roles;
            $roles = $wp_roles->roles;
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>역할</th>
                        <th>표시명</th>
                        <th>권한</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $role_key => $role): ?>
                        <tr>
                            <td><?php echo esc_html($role_key); ?></td>
                            <td><?php echo esc_html($role['name']); ?></td>
                            <td>
                                <?php
                                $capabilities = array_keys(array_filter($role['capabilities']));
                                echo esc_html(implode(', ', array_slice($capabilities, 0, 5)));
                                if (count($capabilities) > 5) {
                                    echo ' ...';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}

// 설정 페이지
function rena_members_settings_page()
{
    echo '<div class="wrap">';
    echo '<h1>설정</h1>';

    echo '<form method="post" action="options.php">';

    // settings_fields() 함수 호출
    settings_fields('rena_members_settings');

    echo '<table class="form-table">';

    // 로그인 리다이렉트 설정
    echo '<tr>';
    echo '<th scope="row"><label for="rena_login_redirect">로그인 후 리다이렉트</label></th>';
    echo '<td>';
    echo '<input type="text" id="rena_login_redirect" name="rena_members_login_redirect" value="' . esc_attr(get_option('rena_members_login_redirect', '')) . '" class="regular-text">';
    echo '<p class="description">로그인 후 이동할 URL을 입력하세요. 비워두면 현재 페이지로 리다이렉트됩니다.</p>';
    echo '</td>';
    echo '</tr>';

    // 회원가입 리다이렉트 설정
    echo '<tr>';
    echo '<th scope="row"><label for="rena_register_redirect">회원가입 후 리다이렉트</label></th>';
    echo '<td>';
    echo '<input type="text" id="rena_register_redirect" name="rena_members_register_redirect" value="' . esc_attr(get_option('rena_members_register_redirect', '')) . '" class="regular-text">';
    echo '<p class="description">회원가입 후 이동할 URL을 입력하세요. 비워두면 홈페이지로 이동합니다.</p>';
    echo '</td>';
    echo '</tr>';

    // 자동 로그인 설정
    echo '<tr>';
    echo '<th scope="row">자동 로그인</th>';
    echo '<td>';
    echo '<label for="rena_auto_login">';
    echo '<input type="checkbox" id="rena_auto_login" name="rena_members_auto_login" value="1" ' . checked(1, get_option('rena_members_auto_login', 1), false) . '>';
    echo ' 회원가입 후 자동으로 로그인하기';
    echo '</label>';
    echo '</td>';
    echo '</tr>';

    echo '</table>';

    echo '<p class="submit">';
    echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="설정 저장">';
    echo '</p>';

    echo '</form>';
    echo '</div>';
}

// Register plugin settings
function rena_members_register_settings()
{
    register_setting('rena_members_settings', 'rena_members_login_redirect');
    register_setting('rena_members_settings', 'rena_members_register_redirect');
    register_setting('rena_members_settings', 'rena_members_auto_login');
}
add_action('admin_init', 'rena_members_register_settings');

// 최근 가입 사용자 가져오기 헬퍼 함수
function get_recent_user()
{
    $recent_user = get_users([
        'number' => 1,
        'orderby' => 'registered',
        'order' => 'DESC'
    ]);

    if (!empty($recent_user)) {
        $user = $recent_user[0];
        return sprintf(
            '%s (%s)',
            $user->display_name,
            human_time_diff(strtotime($user->user_registered), current_time('timestamp')) . ' 전'
        );
    }

    return '없음';
}

// Enqueue admin scripts and styles
function rena_members_admin_enqueue_scripts($hook)
{
    if (strpos($hook, 'rena-members') !== false) {
        wp_enqueue_style('rena-members-admin', RENA_MEMBERS_URL . 'assets/css/admin.css', array(), RENA_MEMBERS_VERSION);
        wp_enqueue_script('rena-members-admin', RENA_MEMBERS_URL . 'assets/js/admin.js', array('jquery'), RENA_MEMBERS_VERSION, true);
    }
}
add_action('admin_enqueue_scripts', 'rena_members_admin_enqueue_scripts');
