<?php
// Get current user
$user_id = get_current_user_id();
$user_data = get_userdata($user_id);

// Tabs
$tabs = array(
    'profile' => __('Profile', 'rena-members'),
    'password' => __('Change Password', 'rena-members'),
    'notifications' => __('Notifications', 'rena-members'),
    'privacy' => __('Privacy', 'rena-members')
);

// Get current tab
$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'profile';

// Ensure tab is valid
if (!array_key_exists($current_tab, $tabs)) {
    $current_tab = 'profile';
}

// Apply filter to tabs
$tabs = apply_filters('rena_members_account_tabs', $tabs);
?>

<div class="rena-members-account">
    <h2><?php _e('My Account', 'rena-members'); ?></h2>
    
    <div class="rena-members-account-header">
        <div class="rena-members-account-avatar">
            <?php echo get_avatar($user_id, 100); ?>
        </div>
        
        <div class="rena-members-account-name">
            <h3><?php echo esc_html($user_data->display_name); ?></h3>
            <p><?php echo esc_html($user_data->user_email); ?></p>
        </div>
    </div>
    
    <div class="rena-members-account-tabs">
        <div class="rena-members-account-tabs-menu">
            <ul>
                <?php foreach ($tabs as $tab_key => $tab_label) : ?>
                    <li class="<?php echo $current_tab === $tab_key ? 'active' : ''; ?>">
                        <a href="<?php echo esc_url(add_query_arg('tab', $tab_key)); ?>"><?php echo esc_html($tab_label); ?></a>
                    </li>
                <?php endforeach; ?>
                
                <li>
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>"><?php _e('Logout', 'rena-members'); ?></a>
                </li>
            </ul>
        </div>
        
        <div class="rena-members-account-tabs-content">
            <?php
            switch ($current_tab) {
                case 'profile':
                    include RENA_MEMBERS_PATH . 'templates/profile-form.php';
                    break;
                    
                case 'password':
                    include RENA_MEMBERS_PATH . 'templates/change-password-form.php';
                    break;
                    
                case 'notifications':
                    include RENA_MEMBERS_PATH . 'templates/notifications-form.php';
                    break;
                    
                case 'privacy':
                    include RENA_MEMBERS_PATH . 'templates/privacy-form.php';
                    break;
                    
                default:
                    do_action('rena_members_account_tab_' . $current_tab);
                    break;
            }
            ?>
        </div>
    </div>
</div>