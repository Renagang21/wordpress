# 워드프레스 커스텀 숏코드 예제 모음

## 목차
1. [기본 숏코드](#기본-숏코드)
2. [속성이 있는 숏코드](#속성이-있는-숏코드)
3. [콘텐츠가 있는 숏코드](#콘텐츠가-있는-숏코드)
4. [중첩 숏코드](#중첩-숏코드)
5. [동적 숏코드](#동적-숏코드)
6. [스타일링과 스크립트](#스타일링과-스크립트)

## 기본 숏코드
### 간단한 숏코드
```php
// [hello] 숏코드
function hello_shortcode() {
    return '안녕하세요!';
}
add_shortcode('hello', 'hello_shortcode');

// 사용 예: [hello]
```

### 현재 날짜 표시
```php
// [current_date] 숏코드
function current_date_shortcode() {
    return date('Y-m-d');
}
add_shortcode('current_date', 'current_date_shortcode');

// 사용 예: [current_date]
```

## 속성이 있는 숏코드
### 기본 속성 처리
```php
// [greeting name="홍길동"] 숏코드
function greeting_shortcode($atts) {
    $atts = shortcode_atts(array(
        'name' => '방문자',
    ), $atts);
    
    return sprintf('안녕하세요, %s님!', esc_html($atts['name']));
}
add_shortcode('greeting', 'greeting_shortcode');

// 사용 예: [greeting name="홍길동"]
```

### 다중 속성
```php
// [box width="300" color="blue" align="center"] 숏코드
function box_shortcode($atts) {
    $atts = shortcode_atts(array(
        'width' => '100%',
        'color' => 'black',
        'align' => 'left',
    ), $atts);
    
    return sprintf(
        '<div style="width: %s; color: %s; text-align: %s;">
            박스 내용
        </div>',
        esc_attr($atts['width']),
        esc_attr($atts['color']),
        esc_attr($atts['align'])
    );
}
add_shortcode('box', 'box_shortcode');

// 사용 예: [box width="300px" color="blue" align="center"]
```

## 콘텐츠가 있는 숏코드
### 기본 래핑 숏코드
```php
// [wrap]내용[/wrap] 숏코드
function wrap_shortcode($atts, $content = null) {
    return '<div class="wrapper">' . do_shortcode($content) . '</div>';
}
add_shortcode('wrap', 'wrap_shortcode');

// 사용 예: [wrap]이 내용이 래핑됩니다.[/wrap]
```

### 스타일 적용 숏코드
```php
// [highlight]내용[/highlight] 숏코드
function highlight_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'color' => 'yellow',
    ), $atts);
    
    return sprintf(
        '<span style="background-color: %s;">%s</span>',
        esc_attr($atts['color']),
        do_shortcode($content)
    );
}
add_shortcode('highlight', 'highlight_shortcode');

// 사용 예: [highlight color="yellow"]강조할 텍스트[/highlight]
```

## 중첩 숏코드
### 탭 시스템
```php
// [tabs][tab title="제목1"]내용1[/tab][tab title="제목2"]내용2[/tab][/tabs]
function tabs_shortcode($atts, $content = null) {
    // 전역 변수로 탭 카운터 초기화
    global $tab_count;
    $tab_count = 0;
    
    // 내부 [tab] 숏코드 처리
    $content = do_shortcode($content);
    
    return sprintf(
        '<div class="tabs">%s</div>',
        $content
    );
}
add_shortcode('tabs', 'tabs_shortcode');

function tab_shortcode($atts, $content = null) {
    global $tab_count;
    $tab_count++;
    
    $atts = shortcode_atts(array(
        'title' => '탭 ' . $tab_count,
    ), $atts);
    
    return sprintf(
        '<div class="tab" data-tab="%d">
            <h3>%s</h3>
            <div class="tab-content">%s</div>
        </div>',
        $tab_count,
        esc_html($atts['title']),
        do_shortcode($content)
    );
}
add_shortcode('tab', 'tab_shortcode');
```

## 동적 숏코드
### 최근 포스트 표시
```php
// [recent_posts count="5" category="news"] 숏코드
function recent_posts_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 5,
        'category' => '',
    ), $atts);
    
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => intval($atts['count']),
        'category_name' => $atts['category'],
    );
    
    $query = new WP_Query($args);
    $output = '<ul class="recent-posts">';
    
    while ($query->have_posts()) {
        $query->the_post();
        $output .= sprintf(
            '<li><a href="%s">%s</a></li>',
            get_permalink(),
            get_the_title()
        );
    }
    
    wp_reset_postdata();
    $output .= '</ul>';
    
    return $output;
}
add_shortcode('recent_posts', 'recent_posts_shortcode');

// 사용 예: [recent_posts count="3" category="news"]
```

### 사용자 정보 표시
```php
// [user_info field="display_name"] 숏코드
function user_info_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '로그인이 필요합니다.';
    }
    
    $atts = shortcode_atts(array(
        'field' => 'display_name',
    ), $atts);
    
    $user = wp_get_current_user();
    return esc_html($user->get($atts['field']));
}
add_shortcode('user_info', 'user_info_shortcode');

// 사용 예: [user_info field="user_email"]
```

## 스타일링과 스크립트
### 스타일 포함
```php
// 숏코드 관련 스타일 등록
function register_shortcode_styles() {
    wp_register_style(
        'shortcode-styles',
        plugins_url('css/shortcodes.css', __FILE__)
    );
}
add_action('wp_enqueue_scripts', 'register_shortcode_styles');

// 숏코드에서 스타일 로드
function styled_box_shortcode($atts, $content = null) {
    wp_enqueue_style('shortcode-styles');
    
    return sprintf(
        '<div class="styled-box">%s</div>',
        do_shortcode($content)
    );
}
add_shortcode('styled_box', 'styled_box_shortcode');
```

### 자바스크립트 통합
```php
// 숏코드 관련 스크립트 등록
function register_shortcode_scripts() {
    wp_register_script(
        'shortcode-scripts',
        plugins_url('js/shortcodes.js', __FILE__),
        array('jquery'),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'register_shortcode_scripts');

// 대화형 숏코드 예제
function interactive_box_shortcode($atts, $content = null) {
    wp_enqueue_script('shortcode-scripts');
    
    return sprintf(
        '<div class="interactive-box" data-action="toggle">%s</div>',
        do_shortcode($content)
    );
}
add_shortcode('interactive_box', 'interactive_box_shortcode');
```

## 참고 사항
- 숏코드는 항상 sanitize/escape 처리
- 성능을 위해 필요한 경우에만 스타일/스크립트 로드
- 중첩 숏코드 사용시 `do_shortcode()` 호출 필요
- 전역 변수 사용은 최소화

## 관련 링크
- [WordPress Shortcode API](https://codex.wordpress.org/Shortcode_API)
- [숏코드 모범 사례](https://example.com)
- [스타일링 가이드](https://example.com)
