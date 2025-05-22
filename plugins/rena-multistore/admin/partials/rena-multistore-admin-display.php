<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$admin = new Rena_Multistore_Admin();
$stores = $admin->get_stores();
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="rena-multistore-admin-content">
        <!-- Store Creation Form -->
        <div class="store-creation-section">
            <h2><?php esc_html_e('새 판매자 등록', 'rena-multistore'); ?></h2>
            <form method="post" action="">
                <?php wp_nonce_field('create_rena_store', 'rena_store_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="seller_name"><?php esc_html_e('판매자명', 'rena-multistore'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="seller_name" id="seller_name" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="seller_slug"><?php esc_html_e('슬러그', 'rena-multistore'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="seller_slug" id="seller_slug" class="regular-text">
                            <p class="description">
                                <?php esc_html_e('입력하지 않으면 판매자명을 기반으로 자동 생성됩니다.', 'rena-multistore'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="seller_desc"><?php esc_html_e('설명', 'rena-multistore'); ?></label>
                        </th>
                        <td>
                            <textarea name="seller_desc" id="seller_desc" rows="3" class="large-text"></textarea>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="create_seller" class="button button-primary" value="<?php esc_attr_e('판매자 등록', 'rena-multistore'); ?>">
                </p>
            </form>
        </div>

        <!-- Store List -->
        <div class="store-list-section">
            <h2><?php esc_html_e('Store List', 'rena-multistore'); ?></h2>
            
            <?php if (!empty($stores)) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Store Name', 'rena-multistore'); ?></th>
                            <th><?php esc_html_e('Address', 'rena-multistore'); ?></th>
                            <th><?php esc_html_e('Phone', 'rena-multistore'); ?></th>
                            <th><?php esc_html_e('Status', 'rena-multistore'); ?></th>
                            <th><?php esc_html_e('Actions', 'rena-multistore'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stores as $store) : 
                            $meta = $admin->get_store_meta($store->ID);
                        ?>
                            <tr>
                                <td>
                                    <strong>
                                        <a href="<?php echo esc_url(get_edit_post_link($store->ID)); ?>">
                                            <?php echo esc_html($store->post_title); ?>
                                        </a>
                                    </strong>
                                </td>
                                <td><?php echo esc_html($meta['address']); ?></td>
                                <td><?php echo esc_html($meta['phone']); ?></td>
                                <td><?php echo esc_html($meta['status']); ?></td>
                                <td>
                                    <a href="<?php echo esc_url(get_edit_post_link($store->ID)); ?>" class="button button-small">
                                        <?php esc_html_e('Edit', 'rena-multistore'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(get_permalink($store->ID)); ?>" class="button button-small" target="_blank">
                                        <?php esc_html_e('View', 'rena-multistore'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e('No stores found.', 'rena-multistore'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div> 