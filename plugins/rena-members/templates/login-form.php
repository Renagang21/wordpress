<?php
// Don't show the form to logged-in users
if (is_user_logged_in()) {
    echo '<p>You are already logged in. <a href="' . wp_logout_url(get_permalink()) . '">Logout</a></p>';
    return;
}
?>

<div class="rena-members-form">
    <h2><?php _e('Login', 'rena-members'); ?></h2>
    
    <?php
    wp_login_form(array(
        'redirect' => isset($atts['redirect']) ? $atts['redirect'] : '',
        'form_id' => 'rena-login-form',
        'label_username' => __('Username or Email', 'rena-members'),
        'label_password' => __('Password', 'rena-members'),
        'label_remember' => __('Remember Me', 'rena-members'),
        'label_log_in' => __('Log In', 'rena-members'),
    ));
    ?>
    
    <p>
        <a href="<?php echo wp_lostpassword_url(); ?>"><?php _e('Forgot your password?', 'rena-members'); ?></a>
        <?php
        if (get_option('users_can_register')) {
            echo ' | <a href="' . wp_registration_url() . '">' . __('Register', 'rena-members') . '</a>';
        }
        ?>
    </p>
</div>
