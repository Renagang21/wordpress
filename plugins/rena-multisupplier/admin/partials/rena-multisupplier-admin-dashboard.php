<?php
/**
 * Admin dashboard template.
 */

// If accessed directly, exit
if (!defined('ABSPATH')) {
    exit;
}

// Get counts
global $wpdb;
$supplier_count = wp_count_posts('rena_supplier')->publish;
$product_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}rena_supplier_products");
?>

<div class="wrap rena-multisupplier-admin">
    <h1>공급업체 관리</h1>
    
    <div class="rena-admin-notices">
        <?php
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            ?>
            <div class="notice notice-warning">
                <p>REST API 연동을 위한 WooCommerce 설정이 필요합니다.</p>
            </div>
            <?php
        }
        ?>
    </div>
    
    <div class="rena-admin-dashboard">
        <div class="rena-admin-cards">
            <div class="rena-admin-card">
                <div class="rena-admin-card-header">
                    <h2>공급업체</h2>
                </div>
                <div class="rena-admin-card-body">
                    <div class="rena-admin-card-count"><?php echo esc_html($supplier_count); ?></div>
                    <p>전체 공급업체</p>
                </div>
                <div class="rena-admin-card-footer">
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=rena_supplier')); ?>" class="button button-primary">
                        전체보기
                    </a>
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=rena_supplier')); ?>" class="button">
                        신규등록
                    </a>
                </div>
            </div>
            
            <div class="rena-admin-card">
                <div class="rena-admin-card-header">
                    <h2>제품</h2>
                </div>
                <div class="rena-admin-card-body">
                    <div class="rena-admin-card-count"><?php echo esc_html($product_count); ?></div>
                    <p>전체 제품</p>
                </div>
                <div class="rena-admin-card-footer">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=rena-supplier-products')); ?>" class="button button-primary">
                        전체보기
                    </a>
                </div>
            </div>
        </div>
        
        <div class="rena-admin-recent">
            <div class="rena-admin-recent-suppliers">
                <h2>최근 등록 공급업체</h2>
                
                <?php
                $recent_suppliers = get_posts(array(
                    'post_type' => 'rena_supplier',
                    'posts_per_page' => 5,
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));
                
                if (!empty($recent_suppliers)) :
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>이름</th>
                            <th>코드</th>
                            <th>담당자</th>
                            <th>등록일</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_suppliers as $supplier_post) : 
                            $supplier = new Rena_Multisupplier_Supplier($supplier_post);
                        ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url(get_edit_post_link($supplier->get_id())); ?>">
                                        <?php echo esc_html($supplier->get_name()); ?>
                                    </a>
                                </td>
                                <td><?php echo esc_html($supplier->get_code()); ?></td>
                                <td><?php echo esc_html($supplier->get_contact()); ?></td>
                                <td><?php echo get_the_date('', $supplier->get_id()); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else : ?>
                <p>등록된 공급업체가 없습니다.</p>
                <?php endif; ?>
            </div>
            
            <div class="rena-admin-recent-products">
                <h2>최근 등록 제품</h2>
                
                <?php
                $recent_products = $wpdb->get_results(
                    "SELECT * FROM {$wpdb->prefix}rena_supplier_products ORDER BY created_at DESC LIMIT 5"
                );
                
                if (!empty($recent_products)) :
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>제품명</th>
                            <th>코드</th>
                            <th>공급업체</th>
                            <th>가격</th>
                            <th>재고</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_products as $product_data) : 
                            $product = new Rena_Multisupplier_Product($product_data->id);
                            $supplier = new Rena_Multisupplier_Supplier($product->get_supplier_id());
                        ?>
                            <tr>
                                <td><?php echo esc_html($product->get_name()); ?></td>
                                <td><?php echo esc_html($product->get_code()); ?></td>
                                <td>
                                    <a href="<?php echo esc_url(get_edit_post_link($supplier->get_id())); ?>">
                                        <?php echo esc_html($supplier->get_name()); ?>
                                    </a>
                                </td>
                                <td><?php echo number_format($product->get_price(), 2); ?></td>
                                <td><?php echo esc_html($product->get_stock()); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else : ?>
                <p>등록된 제품이 없습니다.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 