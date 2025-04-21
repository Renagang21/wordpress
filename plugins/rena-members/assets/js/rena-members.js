
jQuery(document).ready(function($) {
    // Handle form submissions
    $('.rena-members-form form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $response = $form.siblings('.rena-members-form-response');
        
        // Show loading indicator
        $form.find('button[type="submit"]').prop('disabled', true).text('Processing...');
        
        // Clear previous messages
        $response.removeClass('success error').hide();
        
        // Send AJAX request
        $.ajax({
            url: renaMembers.ajaxurl,
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $response.addClass('success').text(response.message).show();
                    
                    // Reset form if it's a login or registration form
                    if ($form.find('input[name="form_id"]').val() !== 'profile') {
                        $form[0].reset();
                    }
                    
                    // Redirect if provided
                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1000);
                    }
                } else {
                    $response.addClass('error').text(response.message).show();
                }
            },
            error: function() {
                $response.addClass('error').text('An error occurred. Please try again.').show();
            },
            complete: function() {
                // Re-enable submit button
                $form.find('button[type="submit"]').prop('disabled', false).text(function() {
                    switch ($form.find('input[name="form_id"]').val()) {
                        case 'login':
                            return 'Login';
                        case 'register':
                            return 'Register';
                        case 'profile':
                            return 'Update Profile';
                        default:
                            return 'Submit';
                    }
                });
            }
        });
    });
    
    // Password confirmation validation
    $('#rena-register-form').on('submit', function(e) {
        var password = $('#password').val();
        var confirm_password = $('#confirm_password').val();
        
        if (password !== confirm_password) {
            e.preventDefault();
            $(this).siblings('.rena-members-form-response')
                .addClass('error')
                .text('Passwords do not match.')
                .show();
            return false;
        }
    });
});