
<?php
// Only show the form to logged-in users
if (!is_user_logged_in()) {
    return;
}

$user_id = get_current_user_id();
$privacy_settings = get_user_meta($user_id, 'rena_members_privacy_settings', true);
$privacy_settings = is_array($privacy_settings) ? $privacy_settings : array();

// Default settings
$defaults = array(
    'show_profile' => 'public',
    'show_email' => 'private',
    'show_activity' => 'members'
);

// Merge with defaults
$privacy_settings = wp_parse_args($privacy_settings, $defaults);
?>

<div class="rena-members-form rena-members-privacy-form">
    <h3><?php _e('Privacy Settings', 'rena-members'); ?></h3>
    
    <div class="rena-members-form-response"></div>
    
    <form id="rena-privacy-form" method="post">
        <div class="rena-members-field">
            <label for="show_profile"><?php _e('Profile Visibility', 'rena-members'); ?></label>
            <select name="privacy_settings[show_profile]" id="show_profile" class="rena-members-select">
                <option value="public" <?php selected($privacy_settings['show_profile'], 'public'); ?>><?php _e('Public', 'rena-members'); ?></option>
                <option value="members" <?php selected($privacy_settings['show_profile'], 'members'); ?>><?php _e('Members Only', 'rena-members'); ?></option>
                <option value="private" <?php selected($privacy_settings['show_profile'], 'private'); ?>><?php _e('Private', 'rena-members'); ?></option>
            </select>
            <p class="description"><?php _e('Who can see your profile information.', 'rena-members'); ?></p>
        </div>
        
        <div class="rena-members-field">
            <label for="show_email"><?php _e('Email Visibility', 'rena-members'); ?></label>
            <select name="privacy_settings[show_email]" id="show_email" class="rena-members-select">
                <option value="public" <?php selected($privacy_settings['show_email'], 'public'); ?>><?php _e('Public', 'rena-members'); ?></option>
                <option value="members" <?php selected($privacy_settings['show_email'], 'members'); ?>><?php _e('Members Only', 'rena-members'); ?></option>
                <option value="private" <?php selected($privacy_settings['show_email'], 'private'); ?>><?php _e('Private', 'rena-members'); ?></option>
            </select>
            <p class="description"><?php _e('Who can see your email address.', 'rena-members'); ?></p>
        </div>
        
        <div class="rena-members-field">
            <label for="show_activity"><?php _e('Activity Visibility', 'rena-members'); ?></label>
            <select name="privacy_settings[show_activity]" id="show_activity" class="rena-members-select">
                <option value="public" <?php selected($privacy_settings['show_activity'], 'public'); ?>><?php _e('Public', 'rena-members'); ?></option>
                <option value="members" <?php selected($privacy_settings['show_activity'], 'members'); ?>><?php _e('Members Only', 'rena-members'); ?></option>
                <option value="private" <?php selected($privacy_settings['show_activity'], 'private'); ?>><?php _e('Private', 'rena-members'); ?></option>
            </select>
            <p class="description"><?php _e('Who can see your activity on the site.', 'rena-members'); ?></p>
        </div>
        
        <div class="rena-members-field">
            <label>
                <input type="checkbox" name="gdpr_delete_request" value="1" />
                <?php _e('Request account data deletion', 'rena-members'); ?>
            </label>
            <p class="description"><?php _e('Check this box to request deletion of your account and all associated data. This action cannot be undone.', 'rena-members'); ?></p>
        </div>
        
        <div class="rena-members-field">
            <input type="hidden" name="form_id" value="privacy" />
            <input type="hidden" name="action" value="rena_members_form_submit" />
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('rena-members-nonce'); ?>" />
            <button type="submit" class="rena-members-button"><?php _e('Save Settings', 'rena-members'); ?></button>
        </div>
    </form>
</div>