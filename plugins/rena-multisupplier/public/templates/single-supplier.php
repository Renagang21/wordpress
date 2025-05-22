<?php
/**
 * The template for displaying all single supplier posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 */

// If accessed directly, exit
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="supplier-single-container">
    <main id="primary" class="site-main">
        <?php
        while (have_posts()) :
            the_post();
            $supplier_id = get_the_ID();
            $supplier = new Rena_Multisupplier_Supplier($supplier_id);
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>

                <div class="entry-content">
                    <div class="supplier-details">
                        <div class="supplier-contact-info">
                            <?php if ($supplier->get_contact()) : ?>
                                <p class="supplier-contact">
                                    <strong>담당자:</strong>
                                    <?php echo esc_html($supplier->get_contact()); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($supplier->get_email()) : ?>
                                <p class="supplier-email">
                                    <strong>이메일:</strong>
                                    <a href="mailto:<?php echo esc_attr($supplier->get_email()); ?>">
                                        <?php echo esc_html($supplier->get_email()); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($supplier->get_phone()) : ?>
                                <p class="supplier-phone">
                                    <strong>전화번호:</strong>
                                    <?php echo esc_html($supplier->get_phone()); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($supplier->get_address()) : ?>
                                <p class="supplier-address">
                                    <strong>주소:</strong>
                                    <?php echo esc_html($supplier->get_address()); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="supplier-description">
                        <?php the_content(); ?>
                    </div>
                    
                    <div class="supplier-products">
                        <h2>제품</h2>
                        <?php
                        // Display supplier products
                        echo do_shortcode('[rena_supplier_products supplier_id="' . $supplier_id . '"]');
                        ?>
                    </div>
                </div>
            </article>
        <?php
        endwhile; // End of the loop.
        ?>
    </main><!-- #main -->
</div><!-- .supplier-single-container -->

<?php
get_sidebar();
get_footer();
?> 