# Rena Multistore

## 📦 개요

**Rena Multistore**는 WooCommerce 기반의 워드프레스 사이트에서 멀티 스토어 구조를 지원하기 위한 독립형 플러그인입니다.  
약국 또는 지점 기반의 상점 구조를 구성하고, 관리자가 스토어 단위로 상품, 테마, 언어 등을 분리 운영할 수 있도록 돕습니다.

이 플러그인은 **다른 플러그인들과 독립적으로 설치 가능하며**,  
개발 중 동일한 기능이 필요한 경우, 공용 함수 또는 유틸리티는 플러그인 내부에 포함된 구조로 관리됩니다.


---

## 📁 디렉터리 구조


```
rena-multistore/
├── rena-multistore.php # 메인 플러그인 파일
├── admin/
│ ├── class-rena-multistore-admin.php # 관리자 설정 로직
│ ├── css/rena-multistore-admin.css # 관리자 전용 스타일
│ ├── js/rena-multistore-admin.js # 관리자용 JS 스크립트
│ └── partials/rena-multistore-admin-display.php # 관리자 템플릿
├── includes/
│ ├── class-rena-multistore-loader.php # 로딩 및 훅 바인딩
│ ├── class-rena-multistore-activator.php # 활성화 처리 (DB, 설정)
│ └── class-rena-multistore-deactivator.php # 비활성화 처리 (캐시 제거)




---

## 🧩 주요 기능 및 설명

| 기능 | 설명 |
|------|------|
| 관리자 메뉴 생성 | `Rena Multistore` 설정 페이지 추가 |
| 스크립트/스타일 로딩 | 관리자 페이지 전용 리소스 등록 |
| DB 테이블 자동 생성 | 플러그인 활성화 시 `rena_multistore_stores` 테이블 생성 |
| 기본 설정 등록 | `default_store`, `enabled` 등의 옵션 자동 등록 |
| 임시 데이터 정리 | 비활성화 시 캐시 자동 삭제 (`rena_multistore_cache`) |

---

## 🛠 설치 및 개발

1. 플러그인을 `wp-content/plugins/rena-multistore/` 디렉터리에 업로드합니다.
2. WordPress 관리자 > 플러그인 > Rena Multistore 활성화
3. 관리자 메뉴에서 'Rena Multistore' 항목 확인

---

## 📅 변경 이력

- **v0.1.0**
  - 플러그인 기본 구조 생성
  - 관리자 메뉴 및 화면 구성
  - DB 테이블 자동 생성 기능 구현
  - 기본 설정값 자동 등록
  - 캐시 자동 정리 기능 포함

---

**작성일**: 2025-04-30  
**작성자**: Rena
