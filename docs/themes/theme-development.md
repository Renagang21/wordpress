# 워드프레스 테마 개발 가이드

## 목차
1. [테마 기본 구조](#테마-기본-구조)
2. [템플릿 계층](#템플릿-계층)
3. [테마 기능](#테마-기능)
4. [사용자 정의](#사용자-정의)
5. [성능 최적화](#성능-최적화)
6. [보안 고려사항](#보안-고려사항)
7. [테스트](#테스트)

## 테마 기본 구조
### 필수 파일
```
my-theme/
├── style.css
├── index.php
├── functions.php
├── header.php
├── footer.php
├── sidebar.php
├── page.php
├── single.php
├── archive.php
├── 404.php
├── search.php
├── comments.php
├── screenshot.png
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
└── template-parts/
    ├── content.php
    ├── content-page.php
    └── content-none.php
```

### style.css
```css
/*
Theme Name: My Custom Theme
Theme URI: https://example.com/my-theme
Author: Your Name
Author URI: https://example.com
Description: A custom WordPress theme
Version: 1.0.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: my-theme
Tags: custom, responsive
*/
```

## 템플릿 계층
### 기본 템플릿
```php
// index.php
<?php get_header(); ?>

<main id="primary" class="site-main">
    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            get_template_part('template-parts/content', get_post_type());
        endwhile;
        
        the_posts_navigation();
    else :
        get_template_part('template-parts/content', 'none');
    endif;
    ?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
```

### 특수 템플릿
```php
// single.php
<?php get_header(); ?>

<main id="primary" class="site-main">
    <?php
    while (have_posts()) :
        the_post();
        get_template_part('template-parts/content', 'single');
        
        // 댓글
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
    endwhile;
    ?>
</main>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
```

## 테마 기능
### functions.php
```php
<?php
if (!defined('ABSPATH')) {
    exit;
}

// 테마 설정
function my_theme_setup() {
    // 자동 피드 링크
    add_theme_support('automatic-feed-links');
    
    // 타이틀 태그
    add_theme_support('title-tag');
    
    // 포스트 썸네일
    add_theme_support('post-thumbnails');
    
    // 메뉴 위치
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my-theme'),
        'footer' => __('Footer Menu', 'my-theme'),
    ));
    
    // HTML5 마크업
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
}
add_action('after_setup_theme', 'my_theme_setup');

// 위젯 영역
function my_theme_widgets_init() {
    register_sidebar(array(
        'name' => __('Sidebar', 'my-theme'),
        'id' => 'sidebar-1',
        'description' => __('Add widgets here.', 'my-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'my_theme_widgets_init');

// 스타일과 스크립트
function my_theme_scripts() {
    wp_enqueue_style(
        'my-theme-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
    
    wp_enqueue_script(
        'my-theme-navigation',
        get_template_directory_uri() . '/assets/js/navigation.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'my_theme_scripts');
```

## 사용자 정의
### 커스텀 로고
```php
// functions.php
function my_theme_customize_register($wp_customize) {
    $wp_customize->add_section('my_theme_logo_section', array(
        'title' => __('Logo', 'my-theme'),
        'priority' => 30,
    ));
    
    $wp_customize->add_setting('my_theme_logo');
    
    $wp_customize->add_control(new WP_Customize_Image_Control(
        $wp_customize,
        'my_theme_logo',
        array(
            'label' => __('Upload Logo', 'my-theme'),
            'section' => 'my_theme_logo_section',
            'settings' => 'my_theme_logo',
        )
    ));
}
add_action('customize_register', 'my_theme_customize_register');
```

### 커스텀 색상
```php
// functions.php
function my_theme_custom_colors() {
    $wp_customize->add_section('my_theme_colors', array(
        'title' => __('Colors', 'my-theme'),
        'priority' => 40,
    ));
    
    // 기본 색상
    $wp_customize->add_setting('my_theme_primary_color', array(
        'default' => '#0073aa',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control(
        $wp_customize,
        'my_theme_primary_color',
        array(
            'label' => __('Primary Color', 'my-theme'),
            'section' => 'my_theme_colors',
            'settings' => 'my_theme_primary_color',
        )
    ));
}
add_action('customize_register', 'my_theme_custom_colors');
```

## 성능 최적화
### 자산 최적화
```php
// functions.php
function my_theme_optimize_assets() {
    // 스타일 최적화
    wp_enqueue_style(
        'my-theme-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
    
    // 스크립트 최적화
    wp_enqueue_script(
        'my-theme-script',
        get_template_directory_uri() . '/assets/js/script.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );
    
    // 조건부 로딩
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'my_theme_optimize_assets');
```

### 캐싱
```php
// functions.php
function my_theme_cache_headers() {
    if (!is_admin()) {
        header('Cache-Control: public, max-age=31536000');
    }
}
add_action('send_headers', 'my_theme_cache_headers');
```

## 보안 고려사항
### 데이터 검증
```php
// functions.php
function my_theme_sanitize_input($input) {
    return wp_kses_post($input);
}

function my_theme_validate_data($data) {
    if (!isset($data['nonce']) || !wp_verify_nonce($data['nonce'], 'my_theme_action')) {
        return false;
    }
    return true;
}
```

### 파일 권한
```php
// functions.php
function my_theme_check_permissions() {
    $upload_dir = wp_upload_dir();
    if (!is_writable($upload_dir['basedir'])) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p>' . 
                 __('Upload directory is not writable.', 'my-theme') . 
                 '</p></div>';
        });
    }
}
add_action('admin_init', 'my_theme_check_permissions');
```

## 테스트
### PHPUnit 테스트
```php
class My_Theme_Test extends WP_UnitTestCase {
    public function setUp() {
        parent::setUp();
        switch_theme('my-theme');
    }
    
    public function test_theme_setup() {
        $this->assertTrue(current_theme_supports('post-thumbnails'));
        $this->assertTrue(has_nav_menu('primary'));
    }
    
    public function test_template_hierarchy() {
        $this->go_to('/');
        $this->assertTemplateLoaded('index.php');
    }
}
```

### 브라우저 테스트
```javascript
// 테스트 예제
describe('My Theme', () => {
    it('should load correctly', () => {
        cy.visit('/');
        cy.get('body').should('have.class', 'home');
    });
    
    it('should display navigation', () => {
        cy.get('.main-navigation').should('be.visible');
    });
});
```

## 참고 사항
- 반응형 디자인 구현
- 접근성 표준 준수
- 성능 최적화
- 보안 고려

## 관련 링크
- [Theme Handbook](https://developer.wordpress.org/themes/)
- [Theme Development](https://developer.wordpress.org/themes/getting-started/)
- [Theme Standards](https://developer.wordpress.org/themes/theme-basics/) 