<?php

namespace RenaMembers\Core;

if (!defined('ABSPATH')) {
    exit;
}

class Access
{
    private static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('template_redirect', array($this, 'check_page_access'));
        add_filter('the_content', array($this, 'filter_restricted_content'));
        add_shortcode('members_only', array($this, 'members_only_shortcode'));
        add_shortcode('role_required', array($this, 'role_required_shortcode'));
    }

    public function check_page_access()
    {
        global $post;

        if (!is_singular()) {
            return;
        }

        $restricted = get_post_meta($post->ID, '_rena_members_restricted', true);
        $allowed_roles = get_post_meta($post->ID, '_rena_members_allowed_roles', true);

        if ($restricted && !is_user_logged_in()) {
            wp_redirect(wp_login_url(get_permalink()));
            exit;
        }

        if ($allowed_roles && !empty($allowed_roles)) {
            $user = wp_get_current_user();
            $has_access = false;

            foreach ($allowed_roles as $role) {
                if (in_array($role, (array) $user->roles)) {
                    $has_access = true;
                    break;
                }
            }

            if (!$has_access) {
                wp_redirect(home_url());
                exit;
            }
        }
    }

    public function filter_restricted_content($content)
    {
        global $post;

        $restricted = get_post_meta($post->ID, '_rena_members_restricted', true);

        if ($restricted && !is_user_logged_in()) {
            return $this->get_restricted_content_message();
        }

        return $content;
    }

    public function members_only_shortcode($atts, $content = null)
    {
        if (!is_user_logged_in()) {
            return $this->get_restricted_content_message();
        }
        return do_shortcode($content);
    }

    public function role_required_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'role' => 'subscriber'
        ), $atts);

        if (!is_user_logged_in()) {
            return $this->get_restricted_content_message();
        }

        $user = wp_get_current_user();
        if (!in_array($atts['role'], (array) $user->roles)) {
            return sprintf(
                '<p class="rena-members-restricted">이 콘텐츠를 보려면 %s 권한이 필요합니다.</p>',
                esc_html($atts['role'])
            );
        }

        return do_shortcode($content);
    }

    private function get_restricted_content_message()
    {
        $message = get_option('rena_members_restricted_message', '');
        if (empty($message)) {
            $message = '이 콘텐츠는 회원 전용입니다. <a href="%s">로그인</a>하거나 <a href="%s">회원가입</a>하세요.';
        }
        return sprintf(
            '<div class="rena-members-restricted">%s</div>',
            sprintf($message, wp_login_url(get_permalink()), wp_registration_url())
        );
    }
}
