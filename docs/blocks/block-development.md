# 구텐베르그 블록 개발 가이드

## 목차
1. [블록 개발 환경 설정](#블록-개발-환경-설정)
2. [블록 기본 구조](#블록-기본-구조)
3. [핵심 블록 확장](#핵심-블록-확장)
4. [커스텀 블록 개발](#커스텀-블록-개발)
5. [블록 패턴](#블록-패턴)
6. [블록 변형](#블록-변형)
7. [성능 최적화](#성능-최적화)

## 블록 개발 환경 설정
### 필수 도구
- Node.js (v14 이상)
- npm 또는 yarn
- @wordpress/create-block 패키지

### 개발 환경 설정
```bash
# 블록 스캐폴딩 생성
npx @wordpress/create-block my-custom-block

# 개발 서버 실행
cd my-custom-block
npm start
```

## 블록 기본 구조
### 필수 파일
- `block.json`: 블록 메타데이터
- `edit.js`: 편집기 컴포넌트
- `save.js`: 프론트엔드 렌더링
- `style.css`: 프론트엔드 스타일
- `editor.css`: 편집기 스타일

### block.json 예제
```json
{
    "apiVersion": 2,
    "name": "my-plugin/my-custom-block",
    "title": "Custom Block",
    "category": "widgets",
    "icon": "smiley",
    "description": "A custom block example",
    "keywords": ["custom", "block"],
    "version": "1.0.0",
    "textdomain": "my-plugin",
    "attributes": {
        "content": {
            "type": "string",
            "source": "html",
            "selector": "p"
        }
    },
    "supports": {
        "html": false,
        "align": true
    }
}
```

## 핵심 블록 확장
### 필터 추가
```php
// functions.php
add_filter('render_block', function($block_content, $block) {
    if ($block['blockName'] === 'core/paragraph') {
        // 단락 블록 수정
    }
    return $block_content;
}, 10, 2);
```

### 스타일 확장
```php
// functions.php
add_action('enqueue_block_editor_assets', function() {
    wp_enqueue_style(
        'my-custom-styles',
        get_template_directory_uri() . '/assets/css/editor.css'
    );
});
```

## 커스텀 블록 개발
### 기본 블록 구조
```jsx
// edit.js
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
    const blockProps = useBlockProps();
    
    return (
        <div {...blockProps}>
            <p>커스텀 블록 편집기</p>
        </div>
    );
}

// save.js
import { useBlockProps } from '@wordpress/block-editor';

export default function save() {
    const blockProps = useBlockProps.save();
    
    return (
        <div {...blockProps}>
            <p>저장된 블록 내용</p>
        </div>
    );
}
```

### 동적 블록
```php
// render.php
function render_my_dynamic_block($attributes) {
    ob_start();
    ?>
    <div class="my-dynamic-block">
        <?php echo esc_html($attributes['content']); ?>
    </div>
    <?php
    return ob_get_clean();
}

register_block_type('my-plugin/my-dynamic-block', array(
    'render_callback' => 'render_my_dynamic_block',
));
```

## 블록 패턴
### 패턴 등록
```php
// patterns.php
register_block_pattern(
    'my-plugin/hero-section',
    array(
        'title' => __('Hero Section', 'my-plugin'),
        'description' => __('A hero section with image and text', 'my-plugin'),
        'content' => '<!-- wp:cover {"url":"...","dimRatio":50} -->
            <div class="wp-block-cover">
                <!-- wp:paragraph -->
                <p>Hero Content</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:cover -->',
    )
);
```

## 블록 변형
### 변형 등록
```js
// variations.js
const variations = [
    {
        name: 'variation-1',
        title: 'Variation 1',
        description: 'First variation',
        attributes: {
            style: 'variation-1'
        },
        icon: 'star',
    }
];

wp.blocks.registerBlockVariation('core/group', variations);
```

## 성능 최적화
### 코드 분할
```js
// edit.js
import { lazy } from '@wordpress/element';

const DynamicComponent = lazy(() => import('./dynamic-component'));

export default function Edit() {
    return (
        <Suspense fallback={<div>Loading...</div>}>
            <DynamicComponent />
        </Suspense>
    );
}
```

### 캐싱 전략
```php
// functions.php
add_filter('render_block', function($block_content, $block) {
    if ($block['blockName'] === 'my-plugin/my-block') {
        $cache_key = 'block_' . md5(serialize($block));
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        set_transient($cache_key, $block_content, HOUR_IN_SECONDS);
    }
    return $block_content;
}, 10, 2);
```

## 참고 사항
- 블록은 항상 반응형으로 설계
- 접근성 고려
- 성능 최적화
- 보안 고려

## 관련 링크
- [Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [Block API Reference](https://developer.wordpress.org/block-editor/reference-guides/block-api/)
- [Create Block Tool](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-create-block/) 