<?php

/**
 * Google OAuth 처리를 위한 핸들러 클래스
 */

require_once dirname(__FILE__) . '/../../../../common-core/auth/backend/src/providers/google.php';

class GoogleAuthHandler
{
    private $provider;

    public function __construct()
    {
        $this->provider = new GoogleAuthProvider();
    }

    /**
     * Google OAuth 콜백 처리
     */
    public function handleCallback($code)
    {
        try {
            return $this->provider->handleCallback($code);
        } catch (Exception $e) {
            error_log('Google OAuth 오류: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Google OAuth URL 가져오기
     */
    public function getAuthUrl()
    {
        return $this->provider->getAuthUrl();
    }

    /**
     * 토큰 검증
     */
    public function verifyToken($token)
    {
        return $this->provider->verifyToken($token);
    }
}
