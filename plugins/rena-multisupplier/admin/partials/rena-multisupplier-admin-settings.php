<?php
/**
 * WooCommerce REST API 설정 페이지
 */

// If accessed directly, exit
if (!defined('ABSPATH')) {
    exit;
}

// 설정 저장 처리
if (isset($_POST['save_settings']) && isset($_POST['rena_settings_nonce']) && wp_verify_nonce($_POST['rena_settings_nonce'], 'rena_save_settings')) {
    // 설정 값 가져오기
    $api_endpoint = isset($_POST['api_endpoint']) ? sanitize_text_field($_POST['api_endpoint']) : '';
    $api_key = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';
    $api_secret = isset($_POST['api_secret']) ? sanitize_text_field($_POST['api_secret']) : '';
    
    // 설정 저장
    update_option('rena_multisupplier_wc_api_endpoint', $api_endpoint);
    update_option('rena_multisupplier_wc_api_key', $api_key);
    update_option('rena_multisupplier_wc_api_secret', $api_secret);
    
    // 성공 메시지 표시
    echo '<div class="notice notice-success is-dismissible"><p>설정이 저장되었습니다.</p></div>';
}

// 현재 설정 가져오기
$api_endpoint = get_option('rena_multisupplier_wc_api_endpoint', 'https://example.com/wp-json/wc/v3');
$api_key = get_option('rena_multisupplier_wc_api_key', '');
$api_secret = get_option('rena_multisupplier_wc_api_secret', '');
?>

<div class="wrap rena-multisupplier-admin">
    <h1>WooCommerce REST API 설정</h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('rena_save_settings', 'rena_settings_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="api_endpoint">WooCommerce API 엔드포인트</label>
                </th>
                <td>
                    <input type="url" name="api_endpoint" id="api_endpoint" class="regular-text" 
                           value="<?php echo esc_attr($api_endpoint); ?>" required />
                    <p class="description">예: https://example.com/wp-json/wc/v3</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="api_key">API 키</label>
                </th>
                <td>
                    <input type="text" name="api_key" id="api_key" class="regular-text" 
                           value="<?php echo esc_attr($api_key); ?>" required />
                    <p class="description">WooCommerce REST API 키</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="api_secret">API 시크릿</label>
                </th>
                <td>
                    <input type="password" name="api_secret" id="api_secret" class="regular-text" 
                           value="<?php echo esc_attr($api_secret); ?>" required />
                    <p class="description">WooCommerce REST API 시크릿</p>
                </td>
            </tr>
        </table>
        
        <p class="description">
            WooCommerce REST API 키를 생성하는 방법: <br>
            1. WooCommerce 관리자 설정 > 고급 > REST API로 이동 <br>
            2. "키 추가" 버튼을 클릭하여 새 API 키 생성 <br>
            3. 권한을 "읽기/쓰기"로 설정 <br>
            4. 생성된 키와 시크릿을 위 필드에 입력
        </p>
        
        <p class="submit">
            <input type="submit" name="save_settings" class="button button-primary" value="설정 저장" />
        </p>
    </form>
    
    <div class="rena-api-test">
        <h2>API 연결 테스트</h2>
        
        <button type="button" id="test_api_connection" class="button">연결 테스트</button>
        <span id="test_result"></span>
        
        <script>
            jQuery(document).ready(function($) {
                $('#test_api_connection').on('click', function() {
                    var testResult = $('#test_result');
                    testResult.html('<span style="color:blue">테스트 중...</span>');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'rena_test_wc_api',
                            nonce: '<?php echo wp_create_nonce('rena_test_api'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                testResult.html('<span style="color:green">연결 성공! ' + response.data.message + '</span>');
                            } else {
                                testResult.html('<span style="color:red">연결 실패: ' + response.data.message + '</span>');
                            }
                        },
                        error: function() {
                            testResult.html('<span style="color:red">AJAX 요청 실패</span>');
                        }
                    });
                });
            });
        </script>
    </div>
</div> 