
<?php
// Only show the form to logged-in users
if (!is_user_logged_in()) {
    return;
}
?>

<div class="rena-members-form rena-members-change-password-form">
    <h3><?php _e('Change Password', 'rena-members'); ?></h3>
    
    <div class="rena-members-form-response"></div>
    
    <form id="rena-change-password-form" method="post">
        <div class="rena-members-field">
            <label for="current_password"><?php _e('Current Password', 'rena-members'); ?></label>
            <input type="password" name="current_password" id="current_password" class="rena-members-input" required />
        </div>
        
        <div class="rena-members-field">
            <label for="new_password"><?php _e('New Password', 'rena-members'); ?></label>
            <input type="password" name="new_password" id="new_password" class="rena-members-input" required />
        </div>
        
        <div class="rena-members-field">
            <label for="confirm_new_password"><?php _e('Confirm New Password', 'rena-members'); ?></label>
            <input type="password" name="confirm_new_password" id="confirm_new_password" class="rena-members-input" required />
        </div>
        
        <div class="rena-members-field">
            <input type="hidden" name="form_id" value="change_password" />
            <input type="hidden" name="action" value="rena_members_form_submit" />
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('rena-members-nonce'); ?>" />
            <button type="submit" class="rena-members-button"><?php _e('Update Password', 'rena-members'); ?></button>
        </div>
    </form>
</div>