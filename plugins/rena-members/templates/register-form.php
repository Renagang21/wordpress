
<?php
// Don't show the form to logged-in users
if (is_user_logged_in()) {
    printf('<p>%s <a href="%s">%s</a></p>', 
        __('You are already logged in.', 'rena-members'),
        wp_logout_url(get_permalink()),
        __('Logout', 'rena-members')
    );
    return;
}

// Don't show the form if user registration is disabled
if (!get_option('users_can_register')) {
    printf('<p>%s</p>', __('User registration is currently disabled.', 'rena-members'));
    return;
}
?>

<div class="rena-members-form rena-members-register-form">
    <h2><?php _e('Register', 'rena-members'); ?></h2>
    
    <div class="rena-members-form-response"></div>
    
    <form id="rena-register-form" method="post">
        <div class="rena-members-field">
            <label for="username"><?php _e('Username', 'rena-members'); ?></label>
            <input type="text" name="username" id="username" class="rena-members-input" required />
        </div>
        
        <div class="rena-members-field">
            <label for="email"><?php _e('Email', 'rena-members'); ?></label>
            <input type="email" name="email" id="email" class="rena-members-input" required />
        </div>
        
        <div class="rena-members-field">
            <label for="password"><?php _e('Password', 'rena-members'); ?></label>
            <input type="password" name="password" id="password" class="rena-members-input" required />
        </div>
        
        <div class="rena-members-field">
            <label for="confirm_password"><?php _e('Confirm Password', 'rena-members'); ?></label>
            <input type="password" name="confirm_password" id="confirm_password" class="rena-members-input" required />
        </div>
        
        <?php do_action('rena_members_after_register_fields'); ?>
        
        <div class="rena-members-field">
            <input type="hidden" name="form_id" value="register" />
            <input type="hidden" name="action" value="rena_members_form_submit" />
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('rena-members-nonce'); ?>" />
            <button type="submit" class="rena-members-button"><?php _e('Register', 'rena-members'); ?></button>
        </div>
        
        <div class="rena-members-field">
            <?php printf(__('Already have an account? %s', 'rena-members'), '<a href="' . wp_login_url() . '">' . __('Login', 'rena-members') . '</a>'); ?>
        </div>
    </form>
</div>