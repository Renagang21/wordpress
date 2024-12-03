document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.copy-button');
    
    buttons.forEach(button => {
        button.addEventListener('click', async function() {
            const text = this.getAttribute('data-clipboard-text');
            
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    // 모던 브라우저용 Clipboard API
                    await navigator.clipboard.writeText(text);
                } else {
                    // 레거시 방식 폴백
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    textarea.style.position = 'fixed';
                    textarea.style.left = '-999999px';
                    textarea.style.top = '-999999px';
                    document.body.appendChild(textarea);
                    textarea.focus();
                    textarea.select();
                    
                    try {
                        document.execCommand('copy');
                    } finally {
                        textarea.remove();
                    }
                }
                
                // 시각적 피드백
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                this.classList.add('copied');
                
                // 접근성 알림
                const notification = document.createElement('div');
                notification.setAttribute('role', 'status');
                notification.setAttribute('aria-live', 'polite');
                notification.className = 'screen-reader-text';
                notification.textContent = 'Content copied to clipboard';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.classList.remove('copied');
                    notification.remove();
                }, 2000);
                
            } catch (err) {
                console.error('Failed to copy text: ', err);
                this.textContent = 'Failed to copy';
                setTimeout(() => {
                    this.textContent = 'Copy';
                }, 2000);
            }
        });
    });
});