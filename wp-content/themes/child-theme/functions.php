<?php

/**
 * Twenty Twenty-Four Child Theme functions
 */

// 사이트별 고유 식별자 가져오기
function get_site_identifier()
{
    return sanitize_title(get_bloginfo('name'));
}

// Google OAuth 설정 페이지 추가
function add_google_oauth_settings_page()
{
    $site_id = get_site_identifier();
    add_options_page(
        'Google OAuth 설정',
        'Google OAuth',
        'manage_options',
        'google-oauth-settings',
        'render_google_oauth_settings_page'
    );

    register_setting(
        'google_oauth_settings',
        "google_oauth_client_id_{$site_id}",
        array('type' => 'string')
    );
    register_setting(
        'google_oauth_settings',
        "google_oauth_client_secret_{$site_id}",
        array('type' => 'string')
    );
    register_setting(
        'google_oauth_settings',
        "google_oauth_redirect_uri_{$site_id}",
        array('type' => 'string')
    );
}
add_action('admin_menu', 'add_google_oauth_settings_page');

// 설정 페이지 콘텐츠
function render_google_oauth_settings_page()
{
    if (!current_user_can('manage_options')) {
        wp_die('권한이 없습니다.');
    }

    $site_id = get_site_identifier();
    $client_id = get_option("google_oauth_client_id_{$site_id}");
    $client_secret = get_option("google_oauth_client_secret_{$site_id}");
    $redirect_uri = get_option("google_oauth_redirect_uri_{$site_id}");

?>
    <div class="wrap">
        <h2>Google OAuth 설정</h2>
        <form method="post" action="options.php">
            <?php settings_fields('google_oauth_settings'); ?>
            <?php do_settings_sections('google_oauth_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Client ID</th>
                    <td>
                        <input type="text" name="google_oauth_client_id_<?php echo esc_attr($site_id); ?>"
                            value="<?php echo esc_attr($client_id); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Client Secret</th>
                    <td>
                        <input type="password" name="google_oauth_client_secret_<?php echo esc_attr($site_id); ?>"
                            value="<?php echo esc_attr($client_secret); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Redirect URI</th>
                    <td>
                        <input type="text" name="google_oauth_redirect_uri_<?php echo esc_attr($site_id); ?>"
                            value="<?php echo esc_attr($redirect_uri); ?>" class="regular-text">
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// 로그인 페이지에 Google 로그인 버튼 추가
function o4o_add_google_login_button()
{
    $site_id = get_site_identifier();
    $google_auth_url = get_option('google_auth_url_' . $site_id, '');

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
            error_log('Google OAuth 오류 (' . get_bloginfo('name') . '): ' . $e->getMessage());
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

        // Google 계정 메타데이터 저장 (사이트별 구분)
        $site_id = get_site_identifier();
        update_user_meta($user_id, 'google_user_id_' . $site_id, $user_data['id']);
        update_user_meta($user_id, 'google_access_token_' . $site_id, $user_data['accessToken']);

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
