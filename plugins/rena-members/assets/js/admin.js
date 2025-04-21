
jQuery(document).ready(function($) {
    // Settings page tabs
    if ($('.rena-members-settings-tabs').length) {
        $('.rena-members-settings-tabs a').on('click', function(e) {
            e.preventDefault();
            
            var target = $(this).attr('href');
            
            // Update active tab
            $('.rena-members-settings-tabs a').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Show target tab content
            $('.rena-members-settings-content').hide();
            $(target).show();
            
            // Update current tab in URL
            if (history.pushState) {
                history.pushState(null, null, target);
            }
        });
        
        // Show initial tab based on URL hash or default to first tab
        var currentTab = window.location.hash || $('.rena-members-settings-tabs a:first').attr('href');
        $('.rena-members-settings-tabs a[href="' + currentTab + '"]').click();
    }
});