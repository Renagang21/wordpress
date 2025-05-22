<?php

/**
 * Fired during plugin deactivation.
 */
class Rena_Multisupplier_Deactivator {

    /**
     * Short Description. (use period)
     */
    public static function deactivate() {
        // Flush rewrite rules on deactivation
        flush_rewrite_rules();
    }
}