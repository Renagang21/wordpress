<?php
/**
 * Template for displaying store pages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="rena-store-page">
    <div class="store-header">
        <h1><?php echo esc_html($store->post_title); ?></h1>
    </div>

    <div class="store-content">
        <?php echo wp_kses_post($store->post_content); ?>
    </div>

    <?php
    // Store meta information
    $store_address = get_post_meta($store->ID, '_store_address', true);
    $store_phone = get_post_meta($store->ID, '_store_phone', true);
    ?>

    <div class="store-info">
        <?php if ($store_address) : ?>
            <div class="store-address">
                <strong><?php esc_html_e('Address:', 'rena-multistore'); ?></strong>
                <?php echo esc_html($store_address); ?>
            </div>
        <?php endif; ?>

        <?php if ($store_phone) : ?>
            <div class="store-phone">
                <strong><?php esc_html_e('Phone:', 'rena-multistore'); ?></strong>
                <?php echo esc_html($store_phone); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer(); 