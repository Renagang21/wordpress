# WordPress 블록

WordPress Gutenberg 블록 개발을 위한 폴더입니다.

## 블록 구조
각 블록은 다음과 같은 구조로 구성됩니다:
```
block-name/
  ├── src/
  │   ├── block.json
  │   ├── index.js
  │   ├── edit.js
  │   └── save.js
  ├── style.css
  └── index.php
```

## 개발 가이드
1. `wp-scripts`를 사용하여 블록 개발
2. 각 블록은 독립적인 패키지로 관리
3. 공통 스타일은 `common-utils/block-utilities`에서 관리 