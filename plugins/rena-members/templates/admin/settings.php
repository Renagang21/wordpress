
<div class="wrap">
    <h1><?php _e('Rena Members Settings', 'rena-members'); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('rena_members_settings');
        do_settings_sections('rena_members_settings');
        ?>
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Login Redirect URL', 'rena-members'); ?></th>
                <td>
                    <input type="text" name="rena_members_login_redirect" value="<?php echo esc_attr(get_option('rena_members_login_redirect')); ?>" class="regular-text" />
                    <p class="description"><?php _e('URL to redirect users after login. Leave blank for default behavior.', 'rena-members'); ?></p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><?php _e('Registration Redirect URL', 'rena-members'); ?></th>
                <td>
                    <input type="text" name="rena_members_registration_redirect" value="<?php echo esc_attr(get_option('rena_members_registration_redirect')); ?>" class="regular-text" />
                    <p class="description"><?php _e('URL to redirect users after registration. Leave blank for default behavior.', 'rena-members'); ?></p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><?php _e('Default User Role', 'rena-members'); ?></th>
                <td>
                    <select name="rena_members_default_role">
                        <?php
                        $roles = get_editable_roles();
                        $default_role = get_option('rena_members_default_role', 'subscriber');
                        
                        foreach ($roles as $role_id => $role) {
                            printf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr($role_id),
                                selected($default_role, $role_id, false),
                                esc_html($role['name'])
                            );
                        }
                        ?>
                    </select>
                    <p class="description"><?php _e('Default role for new users.', 'rena-members'); ?></p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><?php _e('Auto Login After Registration', 'rena-members'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="rena_members_auto_login" value="1" <?php checked(get_option('rena_members_auto_login'), 1); ?> />
                        <?php _e('Automatically log in users after registration', 'rena-members'); ?>
                    </label>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
</div>