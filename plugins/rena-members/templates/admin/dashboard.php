
<div class="wrap">
    <h1><?php _e('Rena Members Dashboard', 'rena-members'); ?></h1>
    
    <div class="rena-members-admin-wrapper">
        <div class="rena-members-box">
            <h2><?php _e('User Statistics', 'rena-members'); ?></h2>
            
            <?php
            $user_count = count_users();
            $total_users = $user_count['total_users'];
            $user_roles = $user_count['avail_roles'];
            ?>
            
            <p><?php printf(__('Total users: %d', 'rena-members'), $total_users); ?></p>
            
            <ul>
                <?php foreach ($user_roles as $role => $count) : ?>
                    <li><?php printf(__('%s: %d', 'rena-members'), ucfirst($role), $count); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="rena-members-box">
            <h2><?php _e('Latest Members', 'rena-members'); ?></h2>
            
            <?php
            $args = array(
                'number' => 5,
                'orderby' => 'registered',
                'order' => 'DESC'
            );
            
            $users = get_users($args);
            ?>
            
            <?php if (!empty($users)) : ?>
                <ul>
                    <?php foreach ($users as $user) : ?>
                        <li>
                            <?php echo esc_html($user->display_name); ?> 
                            <span class="rena-members-user-email">(<?php echo esc_html($user->user_email); ?>)</span>
                            <span class="rena-members-user-date"><?php echo date_i18n(get_option('date_format'), strtotime($user->user_registered)); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p><?php _e('No users found.', 'rena-members'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>