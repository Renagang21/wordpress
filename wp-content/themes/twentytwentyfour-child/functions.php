<?php

/**
 * Twenty Twenty-Four Child Theme functions
 */

// Google OAuth 설정 페이지 추가
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

// 로그인 페이지에 Google 로그인 버튼 추가
function o4o_add_google_login_button()
{
    $google_auth_url = get_option('google_auth_url', '');
    if ($google_auth_url && !is_user_logged_in()) {
    ?>
        <div class="o4o-login-container">
            <div class="o4o-login-box">
                <h2 class="o4o-login-title"><?php _e('소셜 로그인', 'twentytwentyfour-child'); ?></h2>
                <a href="<?php echo esc_url($google_auth_url); ?>" class="o4o-google-login-button">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/google-icon.svg" alt="Google" width="18" height="18">
                    <?php _e('Google 계정으로 로그인', 'twentytwentyfour-child'); ?>
                </a>
            </div>
        </div>
<?php
    }
}
add_action('login_footer', 'o4o_add_google_login_button');

// Google OAuth 콜백 처리
function o4o_handle_google_callback()
{
    if (isset($_GET['code']) && isset($_GET['state'])) {
        try {
            require_once get_stylesheet_directory() . '/includes/google-auth.php';
            $google_auth = new GoogleAuthHandler();
            $user_data = $google_auth->handleCallback($_GET['code']);

            if ($user_data) {
                $user_id = o4o_get_or_create_user($user_data);
                if ($user_id) {
                    wp_set_auth_cookie($user_id);
                    wp_redirect(home_url('/wp-admin'));
                    exit();
                }
            }
        } catch (Exception $e) {
            wp_redirect(wp_login_url() . '?login=failed');
            exit();
        }
    }
}
add_action('template_redirect', 'o4o_handle_google_callback');

// Google 계정으로 로그인한 사용자 처리
function o4o_get_or_create_user($user_data)
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

// Google 아이콘 추가
function o4o_enqueue_google_icon()
{
    if (!file_exists(get_stylesheet_directory() . '/assets/images/google-icon.svg')) {
        wp_mkdir_p(get_stylesheet_directory() . '/assets/images');
        file_put_contents(
            get_stylesheet_directory() . '/assets/images/google-icon.svg',
            file_get_contents(get_template_directory() . '/assets/images/google-icon.svg')
        );
    }
}
