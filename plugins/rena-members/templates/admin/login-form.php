
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
?>

<div class="rena-members-form rena-members-login-form">
    <h2><?php _e('Login', 'rena-members'); ?></h2>
    
    <div class="rena-members-form-response"></div>
    
    <form id="rena-login-form" method="post">
        <div class="rena-members-field">
            <label for="username"><?php _e('Username or Email', 'rena-members'); ?></label>
            <input type="text" name="username" id="username" class="rena-members-input" required />
        </div>
        
        <div class="rena-members-field">
            <label for="password"><?php _e('Password', 'rena-members'); ?></label>
            <input type="password" name="password" id="password" class="rena-members-input" required />
        </div>
        
        <div class="rena-members-field">
            <label>
                <input type="checkbox" name="remember" value="1" />
                <?php _e('Remember me', 'rena-members'); ?>
            </label>
        </div>
        
        <div class="rena-members-field">
            <input type="hidden" name="form_id" value="login" />
            <input type="hidden" name="action" value="rena_members_form_submit" />
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('rena-members-nonce'); ?>" />
            <button type="submit" class="rena-members-button"><?php _e('Login', 'rena-members'); ?></button>
        </div>
        
        <div class="rena-members-field">
            <a href="<?php echo wp_lostpassword_url(); ?>"><?php _e('Forgot your password?', 'rena-members'); ?></a>
            <?php
            // Show register link if user registration is enabled
            if (get_option('users_can_register')) {
                echo ' | <a href="' . wp_registration_url() . '">' . __('Register', 'rena-members') . '</a>';
            }
            ?>
        </div>
    </form>
</div>