# 워드프레스 데이터베이스 쿼리 스니펫 모음

## 목차
1. [기본 쿼리 작성법](#기본-쿼리-작성법)
2. [포스트 관련 쿼리](#포스트-관련-쿼리)
3. [사용자 관련 쿼리](#사용자-관련-쿼리)
4. [메타데이터 쿼리](#메타데이터-쿼리)
5. [커스텀 테이블 쿼리](#커스텀-테이블-쿼리)
6. [성능 최적화](#성능-최적화)

## 기본 쿼리 작성법
### wpdb 객체 사용
```php
global $wpdb;

// 기본 쿼리 예제
$results = $wpdb->get_results(
    "SELECT * FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'"
);

// prepared statement 사용
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",
        'post',
        'publish'
    )
);
```

### 주요 메서드
```php
// 단일 행 조회
$row = $wpdb->get_row("SELECT * FROM {$wpdb->posts} WHERE ID = 1");

// 단일 값 조회
$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");

// 컬럼 조회
$titles = $wpdb->get_col("SELECT post_title FROM {$wpdb->posts}");

// 삽입
$wpdb->insert(
    $wpdb->posts,
    array(
        'post_title' => '제목',
        'post_content' => '내용',
        'post_status' => 'publish'
    ),
    array('%s', '%s', '%s')
);

// 업데이트
$wpdb->update(
    $wpdb->posts,
    array('post_title' => '새 제목'),
    array('ID' => 1),
    array('%s'),
    array('%d')
);

// 삭제
$wpdb->delete(
    $wpdb->posts,
    array('ID' => 1),
    array('%d')
);
```

## 포스트 관련 쿼리
### 포스트 목록 조회
```php
// 최근 포스트 조회
$recent_posts = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT ID, post_title, post_date 
        FROM {$wpdb->posts} 
        WHERE post_type = %s 
        AND post_status = %s 
        ORDER BY post_date DESC 
        LIMIT %d",
        'post',
        'publish',
        10
    )
);

// 카테고리별 포스트 조회
$category_posts = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT p.* 
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        WHERE tt.taxonomy = %s 
        AND tt.term_id = %d
        AND p.post_status = %s",
        'category',
        $category_id,
        'publish'
    )
);
```

### 메타데이터 포함 조회
```php
// 커스텀 필드로 포스트 검색
$posts_with_meta = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT p.*, pm.meta_value
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE pm.meta_key = %s
        AND p.post_status = %s",
        'custom_field_key',
        'publish'
    )
);
```

## 사용자 관련 쿼리
### 사용자 정보 조회
```php
// 역할별 사용자 조회
$users = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT u.*, um.meta_value as role
        FROM {$wpdb->users} u
        INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
        WHERE um.meta_key = %s
        AND um.meta_value LIKE %s",
        $wpdb->prefix . 'capabilities',
        '%administrator%'
    )
);

// 최근 가입 사용자
$new_users = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT ID, user_login, user_email, user_registered
        FROM {$wpdb->users}
        ORDER BY user_registered DESC
        LIMIT %d",
        10
    )
);
```

## 메타데이터 쿼리
### 포스트 메타
```php
// 메타 값으로 포스트 검색
$posts = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT p.*, pm.meta_value
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = %s
        AND pm.meta_value > %d",
        'view_count',
        1000
    )
);

// 여러 메타 조건 검색
$posts = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT p.*
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id
        INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id
        WHERE pm1.meta_key = %s
        AND pm1.meta_value = %s
        AND pm2.meta_key = %s
        AND pm2.meta_value > %d",
        'featured',
        'yes',
        'price',
        100
    )
);
```

## 커스텀 테이블 쿼리
### 테이블 생성
```php
// 테이블 생성 함수
function create_custom_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}custom_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        content text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
```

### CRUD 작업
```php
// 삽입
$wpdb->insert(
    $wpdb->prefix . 'custom_table',
    array(
        'title' => '제목',
        'content' => '내용'
    ),
    array('%s', '%s')
);

// 조회
$results = $wpdb->get_results(
    "SELECT * FROM {$wpdb->prefix}custom_table ORDER BY created_at DESC"
);

// 업데이트
$wpdb->update(
    $wpdb->prefix . 'custom_table',
    array('title' => '새 제목'),
    array('id' => 1),
    array('%s'),
    array('%d')
);
```

## 성능 최적화
### 인덱스 활용
```sql
-- 인덱스 추가
ALTER TABLE {$wpdb->prefix}custom_table ADD INDEX title_index (title);

-- 복합 인덱스
ALTER TABLE {$wpdb->prefix}custom_table 
ADD INDEX compound_index (title, created_at);
```

### 쿼리 최적화 팁
```php
// 필요한 컬럼만 선택
$wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts}");

// LIMIT 사용
$wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} LIMIT %d, %d",
        $offset,
        $per_page
    )
);

// JOIN 최적화
$wpdb->get_results(
    "SELECT p.ID, p.post_title, pm.meta_value
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
    AND pm.meta_key = 'specific_key'"
);
```

## 참고 사항
- 항상 `$wpdb->prepare()`를 사용하여 SQL 인젝션 방지
- 큰 데이터셋의 경우 페이지네이션 구현
- 적절한 인덱스 사용으로 성능 최적화
- 트랜잭션 사용시 주의사항 준수

## 관련 링크
- [WordPress 데이터베이스 클래스](https://developer.wordpress.org/reference/classes/wpdb/)
- [데이터베이스 조작](https://developer.wordpress.org/plugins/database/)
- [쿼리 최적화 가이드](https://example.com)
