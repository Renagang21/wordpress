<?php

/**
 * Plugin Name: Rene Login
 * Plugin URI: https://github.com/Renagang21
 * Description: WordPress 사이트의 로그인 페이지를 커스터마이징하고 리다이렉션을 관리하는 플러그인
 * Version: 1.0.0
 * Author: Rene
 * Author URI: https://github.com/Renagang21
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rene-login
 */

// 보안을 위해 직접 접근 방지
if (!defined('ABSPATH')) {
    exit;
}

// 플러그인 클래스 정의
class Rene_Login
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        // 관리자 메뉴 추가
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // 설정 등록
        add_action('admin_init', array($this, 'register_settings'));

        // 로그인 페이지 리다이렉트
        add_action('login_init', array($this, 'redirect_login_page'));

        // 로그인 후 리다이렉트
        add_filter('login_redirect', array($this, 'login_redirect'), 10, 3);
    }

    // 관리자 메뉴 추가
    public function add_admin_menu()
    {
        add_options_page(
            __('커스텀 로그인 설정', 'rene-login'),
            __('커스텀 로그인', 'rene-login'),
            'manage_options',
            'rene-login-settings',
            array($this, 'render_settings_page')
        );
    }

    // 설정 페이지 렌더링
    public function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('이 페이지에 접근할 권한이 없습니다.', 'rene-login'));
        }
?>
        <div class="wrap">
            <h1><?php echo esc_html__('커스텀 로그인 설정', 'rene-login'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('rene_login_options');
                do_settings_sections('rene-login-settings');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }

    // 설정 등록
    public function register_settings()
    {
        register_setting('rene_login_options', 'rene_login_redirect_url', array(
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('rene_login_options', 'rene_login_page_url', array(
            'sanitize_callback' => 'esc_url_raw'
        ));
        register_setting('rene_login_options', 'rene_login_after_url', array(
            'sanitize_callback' => 'esc_url_raw'
        ));

        add_settings_section(
            'rene_login_main_section',
            __('로그인 설정', 'rene-login'),
            array($this, 'section_description'),
            'rene-login-settings'
        );

        add_settings_field(
            'rene_login_page_url',
            __('커스텀 로그인 페이지 URL', 'rene-login'),
            array($this, 'render_text_field'),
            'rene-login-settings',
            'rene_login_main_section',
            array(
                'field' => 'rene_login_page_url',
                'description' => __('사용자 정의 로그인 페이지의 전체 URL을 입력하세요.', 'rene-login')
            )
        );

        add_settings_field(
            'rene_login_redirect_url',
            __('기본 리다이렉트 URL', 'rene-login'),
            array($this, 'render_text_field'),
            'rene-login-settings',
            'rene_login_main_section',
            array(
                'field' => 'rene_login_redirect_url',
                'description' => __('로그인이 필요한 페이지 접근 시 리다이렉트될 URL입니다.', 'rene-login')
            )
        );

        add_settings_field(
            'rene_login_after_url',
            __('로그인 성공 후 URL', 'rene-login'),
            array($this, 'render_text_field'),
            'rene-login-settings',
            'rene_login_main_section',
            array(
                'field' => 'rene_login_after_url',
                'description' => __('로그인 성공 후 이동할 페이지의 URL입니다.', 'rene-login')
            )
        );
    }

    // 섹션 설명
    public function section_description()
    {
        echo '<p>' . esc_html__('WordPress 로그인 페이지와 리다이렉션을 커스터마이징하기 위한 설정입니다.', 'rene-login') . '</p>';
    }

    // 텍스트 필드 렌더링
    public function render_text_field($args)
    {
        $field = $args['field'];
        $value = get_option($field);
        $description = isset($args['description']) ? $args['description'] : '';

        echo '<input type="url" name="' . esc_attr($field) . '" value="' . esc_attr($value) . '" class="regular-text">';
        if (!empty($description)) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }

    // 로그인 페이지 리다이렉트
    public function redirect_login_page()
    {
        $login_page = get_option('rene_login_page_url');
        $redirect_url = get_option('rene_login_redirect_url');

        if (empty($login_page) || empty($redirect_url)) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['redirect_to'])) {
                $redirect_to = $_GET['redirect_to'];
                // wp-admin 페이지는 기본 로그인 사용
                if (strpos($redirect_to, 'wp-admin') !== false) {
                    return;
                }
            }

            if (!is_user_logged_in()) {
                wp_safe_redirect($login_page);
                exit;
            }
        }
    }

    // 로그인 후 리다이렉트
    public function login_redirect($redirect_to, $requested_redirect_to, $user)
    {
        $after_url = get_option('rene_login_after_url');
        if (!empty($after_url)) {
            return $after_url;
        }
        return $redirect_to;
    }
}

// 플러그인 초기화
function rene_login_init()
{
    Rene_Login::get_instance();
}
add_action('plugins_loaded', 'rene_login_init');
