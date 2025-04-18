<?php

/**
 * Template Name: Custom Login Page
 * Description: O4O 플랫폼 통합 로그인 페이지
 */

get_header();

// 이미 로그인한 사용자는 대시보드로 리다이렉트
if (is_user_logged_in()) {
    wp_redirect(home_url('/dashboard'));
    exit;
}

// Google OAuth URL 가져오기
$google_auth_url = get_option('google_auth_url', '');
?>

<div class="o4o-login-container">
    <div class="o4o-login-box">
        <div class="o4o-login-logo">
            <?php
            $custom_logo_id = get_theme_mod('custom_logo');
            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
            if ($logo) {
                echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '">';
            } else {
                echo '<h1>' . get_bloginfo('name') . '</h1>';
            }
            ?>
        </div>

        <h2 class="o4o-login-title"><?php _e('로그인', 'o4o-theme'); ?></h2>

        <?php
        // 에러 메시지 표시
        if (isset($_GET['login']) && $_GET['login'] == 'failed') {
            echo '<div class="o4o-login-error">' . __('로그인에 실패했습니다. 다시 시도해 주세요.', 'o4o-theme') . '</div>';
        }
        ?>

        <!-- 기본 WordPress 로그인 폼 -->
        <form class="o4o-login-form" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="post">
            <div class="form-group">
                <label for="user_login"><?php _e('아이디 또는 이메일', 'o4o-theme'); ?></label>
                <input type="text" name="log" id="user_login" class="input" required>
            </div>

            <div class="form-group">
                <label for="user_pass"><?php _e('비밀번호', 'o4o-theme'); ?></label>
                <input type="password" name="pwd" id="user_pass" class="input" required>
            </div>

            <div class="form-group remember-me">
                <input type="checkbox" name="rememberme" id="rememberme" value="forever">
                <label for="rememberme"><?php _e('로그인 상태 유지', 'o4o-theme'); ?></label>
            </div>

            <input type="submit" name="wp-submit" class="button button-primary" value="<?php esc_attr_e('로그인', 'o4o-theme'); ?>">
            <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/dashboard')); ?>">
        </form>

        <div class="o4o-login-divider">
            <span><?php _e('또는', 'o4o-theme'); ?></span>
        </div>

        <!-- Google 로그인 버튼 -->
        <?php if ($google_auth_url): ?>
            <a href="<?php echo esc_url($google_auth_url); ?>" class="o4o-google-login-button">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/google-icon.svg" alt="Google">
                <?php _e('Google 계정으로 로그인', 'o4o-theme'); ?>
            </a>
        <?php endif; ?>

        <div class="o4o-login-links">
            <a href="<?php echo wp_lostpassword_url(); ?>"><?php _e('비밀번호를 잊으셨나요?', 'o4o-theme'); ?></a>
            <?php if (get_option('users_can_register')): ?>
                <a href="<?php echo wp_registration_url(); ?>"><?php _e('회원가입', 'o4o-theme'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>