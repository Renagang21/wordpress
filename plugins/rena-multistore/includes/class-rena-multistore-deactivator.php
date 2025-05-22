<?php

/**
 * Fired during plugin deactivation.
 */
class Rena_Multistore_Deactivator {

    /**
     * Plugin deactivation handler.
     */
    public static function deactivate() {
        // Cleanup tasks if needed
        self::cleanup_plugin_data();
    }

    /**
     * Clean up plugin data if necessary.
     */
    private static function cleanup_plugin_data() {
        // Example: Remove temporary data
        delete_transient('rena_multistore_cache');
        
        // Note: We don't delete tables or permanent data here
        // That should be handled by uninstall.php if needed
    }
} 