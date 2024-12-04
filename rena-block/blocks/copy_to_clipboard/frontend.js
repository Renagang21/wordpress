document.addEventListener('DOMContentLoaded', function() {
    // 모든 버튼 처리
    const blocks = document.querySelectorAll('.wp-block-rena-copy-to-clipboard');
    
    blocks.forEach(block => {
        // Copy 버튼 처리
        const copyButton = block.querySelector('.copy-button');
        if (copyButton) {
            copyButton.addEventListener('click', async function() {
                const text = this.getAttribute('data-clipboard-text');
                
                try {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(text);
                    } else {
                        // 폴백: 레거시 방식 사용
                        const textArea = document.createElement('textarea');
                        textArea.value = text;
                        textArea.style.position = 'fixed';
                        textArea.style.left = '-999999px';
                        textArea.style.top = '-999999px';
                        document.body.appendChild(textArea);
                        textArea.focus();
                        textArea.select();
                        
                        try {
                            document.execCommand('copy');
                        } finally {
                            textArea.remove();
                        }
                    }
                    
                    // 복사 성공 피드백
                    const originalText = this.textContent;
                    this.textContent = 'Copied!';
                    this.classList.add('copied');
                    
                    setTimeout(() => {
                        this.textContent = originalText;
                        this.classList.remove('copied');
                    }, 2000);
                    
                } catch (err) {
                    console.error('복사 실패:', err);
                }
            });
        }

        // QR 코드 버튼 처리
        const qrButton = block.querySelector('.qr-button');
        if (qrButton) {
            qrButton.addEventListener('click', async function() {
                const content = this.getAttribute('data-content');
                const size = parseInt(this.getAttribute('data-size'), 10);
                const printLayout = JSON.parse(this.getAttribute('data-print-layout'));
                
                // QR 모달 생성
                const modal = createQRModal(content, size, printLayout);
                document.body.appendChild(modal);
                
                // QR 코드 생성
                try {
                    const qrcode = await import('qrcode');
                    const qrDataUrl = await qrcode.toDataURL(content, {
                        width: size,
                        margin: 1,
                        color: {
                            dark: '#000000',
                            light: '#ffffff',
                        }
                    });
                    
                    const qrImage = modal.querySelector('.qr-image');
                    if (qrImage) {
                        qrImage.src = qrDataUrl;
                        qrImage.setAttribute('data-qr-url', qrDataUrl);
                    }
                } catch (err) {
                    console.error('QR 코드 생성 실패:', err);
                }
            });
        }
    });
});

// QR 모달 생성 함수
function createQRModal(content, size, printLayout) {
    const modal = document.createElement('div');
    modal.className = 'qr-modal';
    
    modal.innerHTML = `
        <div class="qr-modal-content">
            <span class="close-button">&times;</span>
            <div class="qr-container">
                <img class="qr-image" alt="QR Code" />
                <div class="qr-actions">
                    <button class="download-button">Download</button>
                    <button class="copy-qr-button">Copy QR</button>
                    <button class="print-button">Print</button>
                </div>
            </div>
        </div>
    `;
    
    // 닫기 버튼
    const closeButton = modal.querySelector('.close-button');
    closeButton.onclick = function() {
        modal.remove();
    };
    
    // 배경 클릭시 닫기
    modal.onclick = function(event) {
        if (event.target === modal) {
            modal.remove();
        }
    };
    
    // 다운로드 버튼
    const downloadButton = modal.querySelector('.download-button');
    downloadButton.onclick = function() {
        const qrImage = modal.querySelector('.qr-image');
        const link = document.createElement('a');
        link.download = 'qrcode.png';
        link.href = qrImage.getAttribute('data-qr-url');
        link.click();
    };
    
    // QR 복사 버튼
    const copyQRButton = modal.querySelector('.copy-qr-button');
    copyQRButton.onclick = async function() {
        const qrImage = modal.querySelector('.qr-image');
        try {
            const response = await fetch(qrImage.getAttribute('data-qr-url'));
            const blob = await response.blob();
            await navigator.clipboard.write([
                new ClipboardItem({
                    [blob.type]: blob
                })
            ]);
            copyQRButton.textContent = 'Copied!';
            setTimeout(() => {
                copyQRButton.textContent = 'Copy QR';
            }, 2000);
        } catch (err) {
            console.error('QR 복사 실패:', err);
        }
    };
    
    // 프린트 버튼
    const printButton = modal.querySelector('.print-button');
    printButton.onclick = function() {
        const qrImage = modal.querySelector('.qr-image');
        const printWindow = window.open('', '_blank');
        const html = generatePrintHTML(qrImage.getAttribute('data-qr-url'), content, printLayout);
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.print();
    };
    
    return modal;
}

// 프린트 HTML 생성 함수
function generatePrintHTML(qrDataUrl, content, printLayout) {
    const { codesPerRow, codeSize, showText } = printLayout;
    return `
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                .qr-grid {
                    display: grid;
                    grid-template-columns: repeat(${codesPerRow}, 1fr);
                    gap: 20px;
                    padding: 20px;
                }
                .qr-item {
                    text-align: center;
                }
                .qr-code {
                    width: ${codeSize}mm;
                    height: ${codeSize}mm;
                }
                .qr-text {
                    margin-top: 10px;
                    font-size: 12px;
                }
                @media print {
                    @page {
                        size: A4;
                        margin: 10mm;
                    }
                }
            </style>
        </head>
        <body>
            <div class="qr-grid">
                ${Array(6).fill().map(() => `
                    <div class="qr-item">
                        <img src="${qrDataUrl}" class="qr-code" />
                        ${showText ? `<div class="qr-text">${content}</div>` : ''}
                    </div>
                `).join('')}
            </div>
        </body>
        </html>
    `;
}