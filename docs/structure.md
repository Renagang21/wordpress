# Plugin Project Structure

## Directory Structure
```
plugin/
├── block/
│   ├── copy_to_clipboard/
│   │   ├── block.json       # 블록 설정
│   │   ├── index.js        # 에디터 스크립트
│   │   ├── frontend.js     # 프론트엔드 스크립트
│   │   ├── style.css       # 스타일
│   │   └── index.php       # PHP 렌더링
│   └── qr_code/            # (개발 예정)
└── docs/
    ├── structure.md        # 이 문서
    ├── block-copy.md       # Copy 블록 문서
    ├── block-qr.md         # QR 코드 블록 문서
    └── changes.md          # 변경사항 기록
```

## File Descriptions

### Core Files
- `rena-plugin.php`: 플러그인 메인 파일
- `block.json`: 구텐베르그 블록 설정 파일

### Block Components
- `index.js`: 블록 에디터 구현
- `frontend.js`: 프론트엔드 동작 구현
- `style.css`: 블록 스타일링
- `index.php`: 서버사이드 렌더링