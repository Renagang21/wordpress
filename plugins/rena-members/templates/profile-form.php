
<?php
// Only show the form to logged-in users
if (!is_user_logged_in()) {
    printf('<p>%s <a href="%s">%s</a></p>', 
        __('You must be logged in to view your profile.', 'rena-members'),
        wp_login_url(get_permalink()),
        __('Login', 'rena-members')
    );
    return;
}

// Get current user data
$user_id = get_current_user_id();
$user_data = get_userdata($user_id);
?>

<div class="rena-members-form rena-members-profile-form">
    <h2><?php _e('Edit Profile', 'rena-members'); ?></h2>
    
    <div class="rena-members-form-response"></div>
    
    <form id="rena-profile-form" method="post">
        <div class="rena-members-field">
            <label for="display_name"><?php _e('Display Name', 'rena-members'); ?></label>
            <input type="text" name="display_name" id="display_name" class="rena-members-input" value="<?php echo esc_attr($user_data->display_name); ?>" required />
        </div>
        
        <div class="rena-members-field">
            <label for="user_email"><?php _e('Email', 'rena-members'); ?></label>
            <input type="email" name="user_email" id="user_email" class="rena-members-input" value="<?php echo esc_attr($user_data->user_email); ?>" required />
        </div>
        
        <div class="rena-members-field">
            <label for="user_url"><?php _e('Website', 'rena-members'); ?></label>
            <input type="url" name="user_url" id="user_url" class="rena-members-input" value="<?php echo esc_attr($user_data->user_url); ?>" />
        </div>
        
        <div class="rena-members-field">
            <label for="description"><?php _e('Biographical Info', 'rena-members'); ?></label>
            <textarea name="description" id="description" class="rena-members-textarea"><?php echo esc_textarea(get_user_meta($user_id, 'description', true)); ?></textarea>
        </div>
        
        <?php do_action('rena_members_after_profile_fields', $user_id); ?>
        
        <div class="rena-members-field">
            <input type="hidden" name="form_id" value="profile" />
            <input type="hidden" name="action" value="rena_members_form_submit" />
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('rena-members-nonce'); ?>" />
            <button type="submit" class="rena-members-button"><?php _e('Update Profile', 'rena-members'); ?></button>
        </div>
    </form>
</div>