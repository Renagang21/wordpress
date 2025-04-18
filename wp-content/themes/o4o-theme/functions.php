<?php

/**
 * O4O Theme functions and definitions
 */

// 테마 설정
function o4o_theme_setup()
{
    // 테마 지원 기능
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('post-thumbnails');

    // 메뉴 등록
    register_nav_menus(array(
        'primary' => __('주 메뉴', 'o4o-theme'),
        'footer' => __('푸터 메뉴', 'o4o-theme'),
    ));
}
add_action('after_setup_theme', 'o4o_theme_setup');

// 스타일과 스크립트 등록
function o4o_theme_scripts()
{
    // 기본 스타일시트
    wp_enqueue_style('o4o-theme-style', get_stylesheet_uri());

    // 로그인 페이지 스타일
    if (is_page_template('page-login.php')) {
        wp_enqueue_style('o4o-login-style', get_template_directory_uri() . '/assets/css/login.css');
    }
}
add_action('wp_enqueue_scripts', 'o4o_theme_scripts');

// Google OAuth URL 설정 페이지 추가
function o4o_theme_admin_menu()
{
    add_options_page(
        'O4O 로그인 설정',
        'O4O 로그인',
        'manage_options',
        'o4o-login-settings',
        'o4o_theme_settings_page'
    );
}
add_action('admin_menu', 'o4o_theme_admin_menu');

// 설정 페이지 콘텐츠
function o4o_theme_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    // 설정 저장
    if (isset($_POST['google_auth_url'])) {
        update_option('google_auth_url', esc_url_raw($_POST['google_auth_url']));
        echo '<div class="notice notice-success"><p>설정이 저장되었습니다.</p></div>';
    }

    $google_auth_url = get_option('google_auth_url', '');
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="google_auth_url">Google OAuth URL</label>
                    </th>
                    <td>
                        <input type="url" name="google_auth_url" id="google_auth_url"
                            value="<?php echo esc_attr($google_auth_url); ?>"
                            class="regular-text">
                        <p class="description">
                            Google OAuth 인증 URL을 입력하세요.
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button('설정 저장'); ?>
        </form>
    </div>
<?php
}

// 로그인 페이지 리다이렉트
function o4o_theme_login_page_redirect()
{
    // wp-login.php로의 접근을 커스텀 로그인 페이지로 리다이렉트
    if ($GLOBALS['pagenow'] === 'wp-login.php' && !isset($_GET['action']) && !is_user_logged_in()) {
        wp_redirect(home_url('/login'));
        exit();
    }
}
add_action('init', 'o4o_theme_login_page_redirect');

// 로그인 실패 시 처리
function o4o_theme_login_failed()
{
    wp_redirect(home_url('/login?login=failed'));
    exit();
}
add_action('wp_login_failed', 'o4o_theme_login_failed');

// Google OAuth 콜백 처리
function o4o_theme_handle_google_callback()
{
    if (isset($_GET['code']) && isset($_GET['state'])) {
        // Google OAuth 콜백 처리 로직
        // common-core/auth/backend의 Google 프로바이더 사용
        try {
            require_once get_template_directory() . '/includes/google-auth.php';
            $google_auth = new GoogleAuthHandler();
            $user_data = $google_auth->handleCallback($_GET['code']);

            if ($user_data) {
                // 사용자 로그인 또는 계정 생성
                $user_id = o4o_theme_get_or_create_user($user_data);
                if ($user_id) {
                    wp_set_auth_cookie($user_id);
                    wp_redirect(home_url('/dashboard'));
                    exit();
                }
            }
        } catch (Exception $e) {
            wp_redirect(home_url('/login?error=google_auth_failed'));
            exit();
        }
    }
}
add_action('template_redirect', 'o4o_theme_handle_google_callback');

// Google 계정으로 로그인한 사용자 처리
function o4o_theme_get_or_create_user($user_data)
{
    $user = get_user_by('email', $user_data['email']);

    if (!$user) {
        // 새 사용자 생성
        $username = sanitize_user(current(explode('@', $user_data['email'])), true);
        $count = 1;
        $original_username = $username;

        while (username_exists($username)) {
            $username = $original_username . $count;
            $count++;
        }

        $user_id = wp_insert_user([
            'user_login' => $username,
            'user_email' => $user_data['email'],
            'user_pass' => wp_generate_password(),
            'display_name' => $user_data['name'],
            'role' => 'subscriber'
        ]);

        if (is_wp_error($user_id)) {
            return false;
        }

        // Google 계정 메타데이터 저장
        update_user_meta($user_id, 'google_user_id', $user_data['id']);
        update_user_meta($user_id, 'google_access_token', $user_data['accessToken']);

        return $user_id;
    }

    return $user->ID;
}
