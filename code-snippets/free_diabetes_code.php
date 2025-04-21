<?php
/**
 * 무료혈당기 코드 처리 시스템 - 세션 기반 버전
 * - URL 파라미터 사용하지 않음
 * - 순수 세션 기반 인증
 */
toolset_snippet_security_check() or die('Direct access is not allowed');

// 프론트엔드에 AJAX URL 제공
add_action('wp_head', 'add_ajax_url_to_frontend');
function add_ajax_url_to_frontend() {
    echo '<script type="text/javascript">
            var ajaxurl = "' . admin_url('admin-ajax.php') . '";
          </script>';
}

// AJAX 요청 처리
add_action('wp_ajax_process_code_submission', 'process_code_submission');
add_action('wp_ajax_nopriv_process_code_submission', 'process_code_submission');

function process_code_submission() {
    // wp_doing_ajax()로 AJAX 호출 확인
    if (is_admin() && !wp_doing_ajax()) {
        return;
    }

    if (!isset($_POST['tem_code_value'])) {
        echo json_encode([
            'status' => 'error',
            'message' => '데이터가 전송되지 않았습니다.',
            'redirectUrl' => home_url('/product-realcategory/free-meter/')
        ]);
        wp_die();
    }

    $tem_code_value = trim(sanitize_text_field($_POST['tem_code_value']));

    // 코드 길이 검증
    if (empty($tem_code_value) || strlen($tem_code_value) !== 16) {
        echo json_encode([
            'status' => 'error',
            'message' => '유효하지 않은 코드 형식입니다. 16자리 코드를 입력해주세요.',
            'redirectUrl' => home_url('/product-realcategory/free-meter/')
        ]);
        wp_die();
    }

    // 코드 검색
    $args = [
        'post_type'      => 'diabetes-meter-code',
        'post_status'    => 'publish',
        'title'          => $tem_code_value,
        'posts_per_page' => 1,
        'exact'          => true,
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $post_content = get_post_field('post_content', $post_id);

        if ($post_content !== "1") {
            // 코드 사용 처리
            wp_update_post([
                'ID'           => $post_id,
                'post_content' => '1'
            ]);

            // 세션 설정
            if (function_exists('WC') && WC()->session) {
                WC()->session->set('free_meter_access', true);
            }

            echo json_encode([
                'status' => 'success',
                'message' => '정상적인 코드입니다. 무료로 혈당기를 받으실 수 있습니다.',
                'redirectUrl' => home_url('/product-realcategory/free-meter/')
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => '이미 사용한 코드입니다.',
                'redirectUrl' => home_url('/product-realcategory/free-meter/')
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => '유효하지 않은 코드입니다.',
            'redirectUrl' => home_url('/product-realcategory/free-meter/')
        ]);
    }

    wp_reset_postdata();
    wp_die();
}

// 관리자 페이지에서 상품 처리 시 문제 해결
add_filter('woocommerce_product_get_price', 'modify_product_price', 10, 2);
add_filter('woocommerce_product_get_regular_price', 'modify_product_price', 10, 2);

function modify_product_price($price, $product) {
    // 관리자 페이지에서는 원래 가격 반환 (AJAX 요청 제외)
    if (is_admin() && !wp_doing_ajax()) {
        return $price;
    }

    // WooCommerce 세션이 유효한지 확인
    if (!function_exists('WC') || !WC()->session) {
        return $price;
    }

    // 무료혈당기 카테고리 제품만 처리
    if (has_term('free-meter', 'product-realcategory', $product->get_id())) {
        // 세션에서만 확인
        if (WC()->session->get('free_meter_access')) {
            return 0; // 무료 적용
        }
    }
    
    return $price;
}

// WooCommerce 상품 목록에서 무료 혈당기 제품 제외 (세션 기반)
add_action('pre_get_posts', 'exclude_free_meter_products');

function exclude_free_meter_products($query) {
    // 관리자 페이지인 경우 모든 상품 표시 (필터링 하지 않음)
    if (is_admin()) {
        return;
    }
    
    // 메인 쿼리가 아닌 경우 무시
    if (!$query->is_main_query()) {
        return;
    }

    // WooCommerce 세션 확인
    $has_access = false;
    if (function_exists('WC') && WC()->session) {
        $has_access = WC()->session->get('free_meter_access');
    }

    // 접근 권한이 없는 경우에만 free-meter 카테고리 제외
    if (!$has_access) {
        // 상점 페이지에 적용 (shop)
        if (is_shop() || is_product_category() || is_product_tag() || is_search()) {
            $tax_query = (array) $query->get('tax_query');
            
            // 기존 tax_query에 추가
            $tax_query[] = [
                'taxonomy' => 'product-realcategory',
                'field'    => 'slug',
                'terms'    => 'free-meter',
                'operator' => 'NOT IN'
            ];
            
            $query->set('tax_query', $tax_query);
        }
    }
}

// 단일 무료 혈당기 상품 접근 제어 - 직접 URL로 접근 방지
add_action('template_redirect', 'check_free_meter_product_access');

function check_free_meter_product_access() {
    // 단일 상품 페이지가 아닌 경우 무시
    if (!is_product()) {
        return;
    }
    
    // 접근 권한 확인
    $has_access = false;
    if (function_exists('WC') && WC()->session) {
        $has_access = WC()->session->get('free_meter_access');
    }
    
    // 현재 상품이 무료혈당기 카테고리에 속하는지 확인
    global $product;
    if ($product && has_term('free-meter', 'product-realcategory', $product->get_id())) {
        // 접근 권한이 없으면 shop 페이지로 리다이렉트
        if (!$has_access) {
            wp_redirect(wc_get_page_permalink('shop'));
            exit;
        }
    }
}

// 무료혈당기 카테고리 자체에 대한 접근 제어
add_action('template_redirect', 'check_free_meter_category_access');

function check_free_meter_category_access() {
    // 현재 product-realcategory 택소노미의 free-meter 텀인지 확인
    if (is_tax('product-realcategory', 'free-meter')) {
        // 접근 권한 확인
        $has_access = false;
        if (function_exists('WC') && WC()->session) {
            $has_access = WC()->session->get('free_meter_access');
        }
        
        // 접근 권한이 없으면 shop 페이지로 리다이렉트
        if (!$has_access) {
            wp_redirect(wc_get_page_permalink('shop'));
            exit;
        }
    }
}

// 무료 혈당기 안내 메시지
add_action('woocommerce_before_single_product', 'add_free_meter_notice');

function add_free_meter_notice() {
    if (is_admin()) {
        return;
    }

    if (!function_exists('WC') || !WC()->session) {
        return;
    }

    $product = wc_get_product();
    if (!$product) {
        return;
    }

    if (has_term('free-meter', 'product-realcategory', $product->get_id())) {
        // 세션에서만 확인
        if (WC()->session->get('free_meter_access')) {
            wc_print_notice('무료 혈당기를 받으실 수 있습니다.', 'success');
        } else {
            wc_print_notice('유효한 코드가 없습니다. 유료로 구매하실 수 있습니다.', 'notice');
        }
    }
}

// 주문 완료 후 세션 초기화
add_action('woocommerce_thankyou', 'clear_free_meter_session');

function clear_free_meter_session() {
    if (function_exists('WC') && WC()->session) {
        WC()->session->__unset('free_meter_access');
    }
}

// 카트에 상품 추가 시 처리
add_filter('woocommerce_add_to_cart_validation', 'validate_free_meter_product', 10, 3);

function validate_free_meter_product($valid, $product_id, $quantity) {
    // 무료혈당기 카테고리에 속하는지 확인
    if (has_term('free-meter', 'product-realcategory', $product_id)) {
        // 접근 권한 확인
        $has_access = false;
        if (function_exists('WC') && WC()->session) {
            $has_access = WC()->session->get('free_meter_access');
        }
        
        // 접근 권한이 없으면 카트에 추가 불가
        if (!$has_access) {
            wc_add_notice('무료 혈당기는 유효한 코드가 있어야 구매할 수 있습니다.', 'error');
            return false;
        }
    }
    
    return $valid;
}

// 입력 필드 자동 이동 기능 추가
add_action('wp_footer', 'add_code_input_field_handling');
function add_code_input_field_handling() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // 숫자 입력시 자동으로 다음 필드로 이동
        $('#tem1, #tem2, #tem3').on('input', function() {
            if ($(this).val().length === 4) {
                $(this).next('input[type="text"]').focus();
            }
        });
        
        // 백스페이스 입력시 이전 필드로 이동
        $('#tem2, #tem3, #tem4').on('keydown', function(e) {
            if (e.keyCode === 8 && $(this).val().length === 0) {
                $(this).prev('input[type="text"]').focus();
            }
        });

        // 코드 제출 후 페이지 리로드 시 메시지 표시
        if (window.location.href.indexOf('free-meter') > -1) {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('free_meter_status')) {
                if (urlParams.get('free_meter_status') === 'success') {
                    $('.woocommerce-notices-wrapper').prepend('<div class="woocommerce-message">무료 혈당기 코드가 인증되었습니다.</div>');
                }
            }
        }
    });
    </script>
    <?php
}