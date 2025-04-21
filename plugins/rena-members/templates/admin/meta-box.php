
<p>
    <label>
        <input type="checkbox" name="_rena_members_restricted" value="1" <?php checked($restricted, 1); ?> />
        <?php _e('Restrict content to specific roles', 'rena-members'); ?>
    </label>
</p>

<div id="rena-members-roles" <?php echo $restricted ? '' : 'style="display:none;"'; ?>>
    <p><?php _e('Allow access to:', 'rena-members'); ?></p>
    
    <?php foreach ($roles as $role_id => $role) : ?>
        <p>
            <label>
                <input type="checkbox" name="_rena_members_allowed_roles[]" value="<?php echo esc_attr($role_id); ?>" <?php checked(in_array($role_id, $allowed_roles), true); ?> />
                <?php echo esc_html($role['name']); ?>
            </label>
        </p>
    <?php endforeach; ?>
</div>

<script>
jQuery(document).ready(function($) {
    $('input[name="_rena_members_restricted"]').change(function() {
        if ($(this).is(':checked')) {
            $('#rena-members-roles').show();
        } else {
            $('#rena-members-roles').hide();
        }
    });
});
</script>