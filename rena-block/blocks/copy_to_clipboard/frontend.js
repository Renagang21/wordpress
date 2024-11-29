document.addEventListener('DOMContentLoaded', function() {
    // 모든 복사 버튼에 이벤트 리스너 추가
    document.querySelectorAll('.copy-button').forEach(button => {
        button.addEventListener('click', handleCopy);
    });

    // 모든 QR 코드 버튼에 이벤트 리스너 추가
    document.querySelectorAll('.qr-button').forEach(button => {
        button.addEventListener('click', handleQRCode);
    });

    // 복사 기능 처리
    async function handleCopy(e) {
        const button = e.currentTarget;
        const text = button.dataset.clipboardText;
        const originalText = button.textContent;

        try {
            await navigator.clipboard.writeText(text);
            
            // 시각적 피드백
            button.classList.add('copied');
            button.textContent = '복사됨!';
            
            // 3초 후 원래 상태로 복구
            setTimeout(() => {
                button.classList.remove('copied');
                button.textContent = originalText;
            }, 3000);
        } catch (err) {
            console.error('Copy failed:', err);
            // 폴백: 구형 브라우저를 위한 복사 방법
            fallbackCopy(text);
        }
    }

    // QR 코드 생성 및 모달 표시
    function handleQRCode(e) {
        const content = e.currentTarget.dataset.content;
        const modal = createQRModal(content);
        document.body.appendChild(modal);
    }

    // 구형 브라우저를 위한 복사 폴백
    function fallbackCopy(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        
        try {
            document.execCommand('copy');
            textarea.remove();
        } catch (err) {
            console.error('Fallback copy failed:', err);
            textarea.remove();
        }
    }

    // QR 코드 모달 생성
    function createQRModal(content) {
        const modal = document.createElement('div');
        modal.className = 'qr-modal';
        modal.innerHTML = `
            <div class="qr-modal-backdrop"></div>
            <div class="qr-modal-content">
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(content)}" 
                         alt="QR Code">
                </div>
                <button type="button" class="close-modal">닫기</button>
            </div>
        `;

        // 모달 닫기 이벤트
        const closeBtn = modal.querySelector('.close-modal');
        const backdrop = modal.querySelector('.qr-modal-backdrop');
        
        closeBtn.addEventListener('click', () => modal.remove());
        backdrop.addEventListener('click', () => modal.remove());

        return modal;
    }
});