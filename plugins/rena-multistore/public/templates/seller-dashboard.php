<?php
/**
 * Template for displaying seller dashboard
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// 로그인 상태 확인
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(home_url('seller-dashboard')));
    exit;
}

// 판매자 권한 확인
$user = wp_get_current_user();
$is_seller = in_array('store_seller', (array) $user->roles);

if (!$is_seller) {
    // 일반 사용자가 접근한 경우 판매자 등록 안내 메시지 표시
    get_header();
    ?>
    <div class="rena-store-dashboard">
        <div class="container">
            <h2><?php esc_html_e('판매자 대시보드', 'rena-multistore'); ?></h2>
            <div class="notice notice-info">
                <p><?php esc_html_e('판매자 권한이 필요합니다. 판매자로 등록해주세요.', 'rena-multistore'); ?></p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=rena-multistore&action=register_seller')); ?>" class="button button-primary">
                    <?php esc_html_e('판매자 등록하기', 'rena-multistore'); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
    get_footer();
    exit;
}

get_header();
?>

<div class="rena-store-dashboard">
    <div class="container">
        <h2><?php esc_html_e('내 스토어 관리', 'rena-multistore'); ?></h2>

        <?php if ($store) : ?>
            <div class="store-info">
                <table class="store-details">
                    <tr>
                        <th><?php esc_html_e('스토어명:', 'rena-multistore'); ?></th>
                        <td><?php echo esc_html($store->post_title); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('슬러그:', 'rena-multistore'); ?></th>
                        <td><?php echo esc_html($store->post_name); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('등록일:', 'rena-multistore'); ?></th>
                        <td><?php echo esc_html(get_the_date('', $store->ID)); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('상태:', 'rena-multistore'); ?></th>
                        <td><?php echo esc_html(get_post_status($store->ID)); ?></td>
                    </tr>
                </table>

                <div class="store-actions">
                    <a href="<?php echo esc_url(get_edit_post_link($store->ID)); ?>" class="button button-primary">
                        <?php esc_html_e('스토어 정보 수정', 'rena-multistore'); ?>
                    </a>
                    <a href="<?php echo esc_url(get_permalink($store->ID)); ?>" class="button" target="_blank">
                        <?php esc_html_e('스토어 보기', 'rena-multistore'); ?>
                    </a>
                </div>
            </div>

            <!-- Product Import Section -->
            <div class="product-import-section">
                <h3><?php esc_html_e('상품 가져오기', 'rena-multistore'); ?></h3>
                
                <?php
                // API 클래스가 있는지 확인
                if (class_exists('Rena_API_Client')) {
                    $api_client = new Rena_API_Client();
                    $products = $api_client->get_products();
                    
                    if (!is_wp_error($products) && !empty($products)) : ?>
                        <div class="product-grid">
                            <?php foreach ($products as $product) : ?>
                                <div class="product-card">
                                    <?php if (!empty($product['images'][0]['src'])) : ?>
                                        <div class="product-image">
                                            <img src="<?php echo esc_url($product['images'][0]['src']); ?>" 
                                                alt="<?php echo esc_attr($product['name']); ?>">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="product-info">
                                        <h4><?php echo esc_html($product['name']); ?></h4>
                                        <p class="price">
                                            <?php echo esc_html(number_format($product['price'])); ?>원
                                        </p>
                                        
                                        <form method="post" action="">
                                            <?php wp_nonce_field('import_product', 'product_nonce'); ?>
                                            <input type="hidden" name="product_id" value="<?php echo esc_attr($product['id']); ?>">
                                            <input type="submit" name="import_product" 
                                                value="<?php esc_attr_e('내 스토어에 등록', 'rena-multistore'); ?>" 
                                                class="button button-primary">
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p><?php esc_html_e('가져올 수 있는 상품이 없습니다.', 'rena-multistore'); ?></p>
                    <?php endif;
                } else { ?>
                    <p><?php esc_html_e('API 클라이언트가 활성화되지 않았습니다.', 'rena-multistore'); ?></p>
                <?php } ?>
            </div>
        <?php else : ?>
            <div class="no-store-message">
                <p><?php esc_html_e('아직 등록된 스토어가 없습니다.', 'rena-multistore'); ?></p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=rena-multistore&action=create_store')); ?>" class="button button-primary">
                    <?php esc_html_e('스토어 등록하기', 'rena-multistore'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer(); 