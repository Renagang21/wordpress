<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Include required files
 */
function rena_members_includes() {
    // Core includes
    require_once RENA_MEMBERS_PATH . 'includes/core/class-setup.php';
    require_once RENA_MEMBERS_PATH . 'includes/core/class-init.php';
    require_once RENA_MEMBERS_PATH . 'includes/core/class-user.php';
    require_once RENA_MEMBERS_PATH . 'includes/core/class-form.php';
    require_once RENA_MEMBERS_PATH . 'includes/core/class-access.php';
    require_once RENA_MEMBERS_PATH . 'includes/core/class-fields.php';
    
    // Admin includes
    if (is_admin()) {
        require_once RENA_MEMBERS_PATH . 'includes/admin/class-admin.php';
    }
    
    // Frontend includes
    if (!is_admin()) {
        require_once RENA_MEMBERS_PATH . 'includes/frontend/class-frontend.php';
    }
}

// rena_members_init() 함수 제거 - 중복 선언 문제 해결