# 워드프레스 외부 API 통합 스니펫 모음

## 목차
1. [REST API 기본 통신](#REST-API-기본-통신)
2. [OAuth 인증](#OAuth-인증)
3. [주요 API 통합 예제](#주요-API-통합-예제)
4. [에러 처리](#에러-처리)
5. [캐싱 전략](#캐싱-전략)
6. [보안 고려사항](#보안-고려사항)

## REST API 기본 통신
### HTTP 요청 기본
```php
// GET 요청
function make_get_request($url, $args = []) {
    $response = wp_remote_get($url, $args);
    
    if (is_wp_error($response)) {
        return $response;
    }
    
    return json_decode(wp_remote_retrieve_body($response));
}

// POST 요청
function make_post_request($url, $data, $args = []) {
    $default_args = array(
        'body' => json_encode($data),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
    );
    
    $args = wp_parse_args($args, $default_args);
    $response = wp_remote_post($url, $args);
    
    if (is_wp_error($response)) {
        return $response;
    }
    
    return json_decode(wp_remote_retrieve_body($response));
}
```

### 헤더 및 인증 설정
```php
// API 키 인증
$args = array(
    'headers' => array(
        'Authorization' => 'Bearer ' . $api_key,
        'Content-Type' => 'application/json',
    ),
);

// Basic 인증
$args = array(
    'headers' => array(
        'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
    ),
);
```

## OAuth 인증
### OAuth2 구현
```php
class OAuth2_Client {
    private $client_id;
    private $client_secret;
    private $token_url;
    
    public function __construct($client_id, $client_secret, $token_url) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->token_url = $token_url;
    }
    
    public function get_access_token() {
        $args = array(
            'body' => array(
                'grant_type' => 'client_credentials',
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
            ),
        );
        
        $response = wp_remote_post($this->token_url, $args);
        return json_decode(wp_remote_retrieve_body($response));
    }
}
```

### 토큰 관리
```php
class Token_Manager {
    private $option_name = 'api_access_token';
    
    public function get_token() {
        $token = get_option($this->option_name);
        
        if (!$token || $this->is_token_expired($token)) {
            $token = $this->refresh_token();
            $this->save_token($token);
        }
        
        return $token;
    }
    
    private function is_token_expired($token) {
        return $token->expires_at < time();
    }
    
    private function save_token($token) {
        update_option($this->option_name, $token);
    }
}
```

## 주요 API 통합 예제
### Google Maps API
```php
function get_geocoding_data($address) {
    $api_key = get_option('google_maps_api_key');
    $url = add_query_arg(
        array(
            'address' => urlencode($address),
            'key' => $api_key,
        ),
        'https://maps.googleapis.com/maps/api/geocode/json'
    );
    
    return make_get_request($url);
}
```

### OpenWeather API
```php
function get_weather_data($city) {
    $api_key = get_option('openweather_api_key');
    $url = add_query_arg(
        array(
            'q' => $city,
            'appid' => $api_key,
            'units' => 'metric',
        ),
        'https://api.openweathermap.org/data/2.5/weather'
    );
    
    return make_get_request($url);
}
```

## 에러 처리
### API 응답 검증
```php
function validate_api_response($response) {
    if (is_wp_error($response)) {
        error_log('API Error: ' . $response->get_error_message());
        return false;
    }
    
    $http_code = wp_remote_retrieve_response_code($response);
    if ($http_code !== 200) {
        error_log('API HTTP Error: ' . $http_code);
        return false;
    }
    
    return true;
}
```

### 재시도 메커니즘
```php
function make_request_with_retry($url, $args = [], $max_retries = 3) {
    $attempt = 0;
    
    while ($attempt < $max_retries) {
        $response = wp_remote_get($url, $args);
        
        if (!is_wp_error($response)) {
            return $response;
        }
        
        $attempt++;
        sleep(pow(2, $attempt)); // 지수 백오프
    }
    
    return new WP_Error('api_error', '최대 재시도 횟수 초과');
}
```

## 캐싱 전략
### 트랜지언트 API 사용
```php
function get_cached_api_data($endpoint, $cache_time = 3600) {
    $cache_key = 'api_cache_' . md5($endpoint);
    $cached_data = get_transient($cache_key);
    
    if (false === $cached_data) {
        $response = make_get_request($endpoint);
        
        if (!is_wp_error($response)) {
            set_transient($cache_key, $response, $cache_time);
            return $response;
        }
        
        return $response;
    }
    
    return $cached_data;
}
```

### 조건부 요청
```php
function make_conditional_request($url, $etag = '') {
    $args = array(
        'headers' => array(),
    );
    
    if ($etag) {
        $args['headers']['If-None-Match'] = $etag;
    }
    
    $response = wp_remote_get($url, $args);
    
    if (304 === wp_remote_retrieve_response_code($response)) {
        return get_cached_data($url);
    }
    
    return $response;
}
```

## 보안 고려사항
### API 키 관리
```php
// API 키 암호화 저장
function save_api_key($key) {
    if (!function_exists('wp_encrypt_string')) {
        return false;
    }
    
    $encrypted = wp_encrypt_string($key);
    return update_option('encrypted_api_key', $encrypted);
}

// API 키 복호화
function get_api_key() {
    if (!function_exists('wp_decrypt_string')) {
        return false;
    }
    
    $encrypted = get_option('encrypted_api_key');
    return wp_decrypt_string($encrypted);
}
```

### 요청 검증
```php
function verify_api_request($params, $secret) {
    $signature = $params['signature'];
    unset($params['signature']);
    
    ksort($params);
    $string_to_sign = http_build_query($params);
    $calculated_signature = hash_hmac('sha256', $string_to_sign, $secret);
    
    return hash_equals($signature, $calculated_signature);
}
```

## 참고 사항
- API 요청 시 항상 에러 처리 구현
- 적절한 캐싱 전략 사용
- API 키와 시크릿 보안 유지
- 요청 제한 고려

## 관련 링크
- [WordPress HTTP API](https://developer.wordpress.org/plugins/http-api/)
- [OAuth2 프로토콜](https://oauth.net/2/)
- [API 보안 가이드](https://example.com)
