# 워드프레스 플러그인 개발 가이드

## 목차
1. [플러그인 기본 구조](#플러그인-기본-구조)
2. [플러그인 활성화/비활성화](#플러그인-활성화비활성화)
3. [관리자 페이지](#관리자-페이지)
4. [데이터베이스 작업](#데이터베이스-작업)
5. [보안 고려사항](#보안-고려사항)
6. [성능 최적화](#성능-최적화)
7. [테스트](#테스트)

## 플러그인 기본 구조
### 기본 파일 구조
```
my-plugin/
├── my-plugin.php
├── uninstall.php
├── includes/
│   ├── class-plugin.php
│   ├── class-admin.php
│   └── class-public.php
├── admin/
│   ├── css/
│   ├── js/
│   └── partials/
├── public/
│   ├── css/
│   ├── js/
│   └── partials/
└── languages/
```

### 메인 플러그인 파일
```php
<?php
/**
 * Plugin Name: My Custom Plugin
 * Plugin URI: https://example.com/my-plugin
 * Description: A custom WordPress plugin
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * Text Domain: my-plugin
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// 플러그인 상수 정의
define('MY_PLUGIN_VERSION', '1.0.0');
define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));

// 오토로더
require_once MY_PLUGIN_PATH . 'includes/class-plugin.php';

// 플러그인 초기화
function my_plugin_init() {
    $plugin = new My_Plugin();
    $plugin->run();
}
add_action('plugins_loaded', 'my_plugin_init');
```

## 플러그인 활성화/비활성화
### 활성화 훅
```php
register_activation_hook(__FILE__, 'my_plugin_activate');

function my_plugin_activate() {
    // 데이터베이스 테이블 생성
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}my_plugin_data (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        value text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // 기본 옵션 설정
    add_option('my_plugin_version', MY_PLUGIN_VERSION);
}
```

### 비활성화 훅
```php
register_deactivation_hook(__FILE__, 'my_plugin_deactivate');

function my_plugin_deactivate() {
    // 임시 데이터 정리
    wp_clear_scheduled_hook('my_plugin_cron');
}
```

## 관리자 페이지
### 메뉴 추가
```php
add_action('admin_menu', 'my_plugin_admin_menu');

function my_plugin_admin_menu() {
    add_menu_page(
        __('My Plugin', 'my-plugin'),
        __('My Plugin', 'my-plugin'),
        'manage_options',
        'my-plugin',
        'my_plugin_admin_page',
        'dashicons-admin-generic',
        20
    );
    
    add_submenu_page(
        'my-plugin',
        __('Settings', 'my-plugin'),
        __('Settings', 'my-plugin'),
        'manage_options',
        'my-plugin-settings',
        'my_plugin_settings_page'
    );
}
```

### 설정 페이지
```php
function my_plugin_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // 설정 저장 처리
    if (isset($_POST['my_plugin_settings'])) {
        check_admin_referer('my_plugin_settings');
        
        $settings = array(
            'option1' => sanitize_text_field($_POST['option1']),
            'option2' => sanitize_text_field($_POST['option2']),
        );
        
        update_option('my_plugin_settings', $settings);
        add_settings_error('my_plugin_messages', 'my_plugin_message', __('Settings Saved', 'my-plugin'), 'updated');
    }
    
    // 설정 페이지 출력
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <?php settings_errors('my_plugin_messages'); ?>
        <form method="post">
            <?php wp_nonce_field('my_plugin_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Option 1', 'my-plugin'); ?></th>
                    <td>
                        <input type="text" name="option1" value="<?php echo esc_attr(get_option('my_plugin_settings')['option1']); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Option 2', 'my-plugin'); ?></th>
                    <td>
                        <input type="text" name="option2" value="<?php echo esc_attr(get_option('my_plugin_settings')['option2']); ?>" class="regular-text">
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings', 'my-plugin')); ?>
        </form>
    </div>
    <?php
}
```

## 데이터베이스 작업
### CRUD 작업
```php
class My_Plugin_Data {
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'my_plugin_data';
    }
    
    public function create($data) {
        global $wpdb;
        return $wpdb->insert(
            $this->table_name,
            array(
                'name' => $data['name'],
                'value' => $data['value'],
            ),
            array('%s', '%s')
        );
    }
    
    public function read($id) {
        global $wpdb;
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id = %d",
                $id
            )
        );
    }
    
    public function update($id, $data) {
        global $wpdb;
        return $wpdb->update(
            $this->table_name,
            array(
                'name' => $data['name'],
                'value' => $data['value'],
            ),
            array('id' => $id),
            array('%s', '%s'),
            array('%d')
        );
    }
    
    public function delete($id) {
        global $wpdb;
        return $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );
    }
}
```

## 보안 고려사항
### 데이터 검증
```php
function my_plugin_process_form() {
    if (!isset($_POST['my_plugin_nonce']) || !wp_verify_nonce($_POST['my_plugin_nonce'], 'my_plugin_action')) {
        wp_die(__('Security check failed', 'my-plugin'));
    }
    
    $data = array(
        'name' => sanitize_text_field($_POST['name']),
        'email' => sanitize_email($_POST['email']),
        'message' => wp_kses_post($_POST['message']),
    );
    
    // 데이터 처리
}
```

### 권한 확인
```php
function my_plugin_admin_action() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'my-plugin'));
    }
    
    // 관리자 작업 수행
}
```

## 성능 최적화
### 캐싱
```php
function my_plugin_get_data() {
    $cache_key = 'my_plugin_data';
    $data = get_transient($cache_key);
    
    if (false === $data) {
        $data = expensive_database_query();
        set_transient($cache_key, $data, HOUR_IN_SECONDS);
    }
    
    return $data;
}
```

### 자산 최적화
```php
function my_plugin_enqueue_assets() {
    wp_enqueue_style(
        'my-plugin-style',
        MY_PLUGIN_URL . 'public/css/style.css',
        array(),
        MY_PLUGIN_VERSION
    );
    
    wp_enqueue_script(
        'my-plugin-script',
        MY_PLUGIN_URL . 'public/js/script.js',
        array('jquery'),
        MY_PLUGIN_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'my_plugin_enqueue_assets');
```

## 테스트
### PHPUnit 테스트
```php
class My_Plugin_Test extends WP_UnitTestCase {
    public function setUp() {
        parent::setUp();
        $this->plugin = new My_Plugin();
    }
    
    public function test_plugin_initialization() {
        $this->assertInstanceOf('My_Plugin', $this->plugin);
    }
    
    public function test_data_creation() {
        $data = array(
            'name' => 'Test',
            'value' => 'Test Value'
        );
        
        $result = $this->plugin->create_data($data);
        $this->assertTrue($result);
    }
}
```

### JavaScript 테스트
```javascript
// Jest 테스트 예제
describe('MyPlugin', () => {
    it('should initialize correctly', () => {
        const plugin = new MyPlugin();
        expect(plugin).toBeDefined();
    });
    
    it('should handle form submission', () => {
        const form = document.createElement('form');
        const plugin = new MyPlugin(form);
        
        const submitEvent = new Event('submit');
        form.dispatchEvent(submitEvent);
        
        expect(plugin.submitted).toBe(true);
    });
});
```

## 참고 사항
- 플러그인은 항상 최신 워드프레스 버전과 호환
- 보안 취약점 검사
- 성능 모니터링
- 사용자 피드백 수집

## 관련 링크
- [Plugin Handbook](https://developer.wordpress.org/plugins/)
- [Plugin API Reference](https://developer.wordpress.org/plugins/plugin-basics/)
- [Best Practices](https://developer.wordpress.org/plugins/plugin-basics/best-practices/) 