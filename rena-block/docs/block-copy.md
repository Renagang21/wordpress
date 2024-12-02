# Copy to Clipboard Block

## 기능 설명
텍스트를 표시하고 클립보드에 복사할 수 있는 블록입니다.

## 사용 방법
1. 구텐베르그 에디터에서 블록 추가
2. 복사할 텍스트 입력
3. 버튼 텍스트 및 아이콘 표시 여부 설정 (선택사항)

## 블록 속성
```json
{
    "content": {
        "type": "string",
        "source": "html",
        "selector": "div.copyable-content"
    },
    "buttonText": {
        "type": "string",
        "default": "Copy"
    },
    "showIcon": {
        "type": "boolean",
        "default": true
    }
}
```

## 기술 스택
- PHP 7.4+
- WordPress 5.8+
- React (Gutenberg)