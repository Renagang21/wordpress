# WordPress 플러그인

WordPress 플러그인 개발을 위한 폴더입니다.

## 플러그인 구조
각 플러그인은 다음과 같은 구조로 구성됩니다:
```
plugin-name/
  ├── includes/
  │   ├── class-plugin-name.php
  │   └── ...
  ├── admin/
  │   ├── css/
  │   ├── js/
  │   └── ...
  ├── public/
  │   ├── css/
  │   ├── js/
  │   └── ...
  ├── languages/
  ├── plugin-name.php
  └── README.md
```

## 개발 가이드
1. WordPress 코딩 표준 준수
2. `common-utils/plugin-helpers`의 유틸리티 활용
3. 각 플러그인은 독립적인 패키지로 관리 