<?php
/**
 * Template for displaying supplier products.
 */

// If accessed directly, exit
if (!defined('ABSPATH')) {
    exit;
}

// Get variables from shortcode
$supplier = isset($supplier) ? $supplier : null;
$products = isset($products) ? $products : array();
$columns = isset($atts['columns']) ? (int) $atts['columns'] : 3;
?>

<div class="supplier-products-container columns-<?php echo esc_attr($columns); ?>">
    <?php if (!empty($products)) : ?>
        <div class="supplier-products-grid">
            <?php foreach ($products as $product_data) : 
                $product = new Rena_Multisupplier_Product($product_data->id);
            ?>
                <div class="supplier-product-item">
                    <div class="supplier-product-inner">
                        <?php if ($product->get_image()) : ?>
                            <div class="supplier-product-image">
                                <img src="<?php echo esc_url($product->get_image()); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="supplier-product-content">
                            <h3 class="supplier-product-title"><?php echo esc_html($product->get_name()); ?></h3>
                            
                            <?php if ($product->get_code()) : ?>
                                <p class="supplier-product-code">
                                    <span class="label">코드:</span>
                                    <span class="value"><?php echo esc_html($product->get_code()); ?></span>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($product->get_sku()) : ?>
                                <p class="supplier-product-sku">
                                    <span class="label">SKU:</span>
                                    <span class="value"><?php echo esc_html($product->get_sku()); ?></span>
                                </p>
                            <?php endif; ?>
                            
                            <div class="supplier-product-description">
                                <?php echo esc_html($product->get_description()); ?>
                            </div>
                            
                            <div class="supplier-product-price">
                                <span class="price-amount"><?php echo number_format($product->get_price(), 2); ?></span>
                            </div>
                            
                            <div class="supplier-product-stock">
                                <?php if ($product->get_stock() > 0) : ?>
                                    <span class="in-stock"><?php echo sprintf('재고 있음 (%d)', $product->get_stock()); ?></span>
                                <?php else : ?>
                                    <span class="out-of-stock">품절</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($product->get_stock() > 0) : ?>
                                <div class="supplier-product-actions">
                                    <form class="add-to-cart-form" method="post" action="<?php echo esc_url($this->api_endpoint . '/sync-to-woocommerce'); ?>">
                                        <?php wp_nonce_field('add_to_cart_' . $product->get_id(), 'add_to_cart_nonce'); ?>
                                        <input type="hidden" name="supplier_product_id" value="<?php echo esc_attr($product->get_id()); ?>">
                                        
                                        <div class="quantity">
                                            <label for="quantity_<?php echo esc_attr($product->get_id()); ?>">수량</label>
                                            <input type="number" id="quantity_<?php echo esc_attr($product->get_id()); ?>" name="quantity" value="1" min="1" max="<?php echo esc_attr($product->get_stock()); ?>">
                                        </div>
                                        
                                        <button type="submit" name="add_to_cart" class="add-to-cart-button">
                                            장바구니에 추가
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="no-products">상품이 없습니다.</p>
    <?php endif; ?>
</div> 