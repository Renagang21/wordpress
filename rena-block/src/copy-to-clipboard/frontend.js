document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.copy-button');
    const qrButtons = document.querySelectorAll('.qr-button');

    buttons.forEach(button => {
        button.addEventListener('click', function () {
            const text = this.getAttribute('data-clipboard-text');
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            // 버튼 텍스트 변경
            const originalText = this.textContent;
            this.textContent = 'Copied!';
            setTimeout(() => {
                this.textContent = originalText;
            }, 2000);
        });
    });

    // QR 코드 버튼 처리
    qrButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const content = this.getAttribute('data-clipboard-text');
            const size = parseInt(this.getAttribute('data-qr-size') || '200', 10);
            
            try {
                // QR 코드 생성
                const QRCode = await import('qrcode');
                const qrDataUrl = await QRCode.toDataURL(content, {
                    width: size,
                    margin: 1,
                });

                // 모달 생성 및 표시
                const modal = createQRModal(qrDataUrl, content);
                document.body.appendChild(modal);
            } catch (err) {
                console.error('QR 코드 생성 실패:', err);
            }
        });
    });
});

function createQRModal(qrDataUrl, content) {
    const modal = document.createElement('div');
    modal.className = 'qr-modal';
    modal.innerHTML = `
        <div class="qr-modal-content">
            <span class="close">&times;</span>
            <img src="${qrDataUrl}" alt="QR Code" />
            <div class="qr-actions">
                <button class="download-btn">Download</button>
                <button class="print-btn">Print</button>
            </div>
        </div>
    `;

    // 닫기 버튼
    const closeBtn = modal.querySelector('.close');
    closeBtn.onclick = () => modal.remove();

    // 배경 클릭시 닫기
    modal.onclick = (e) => {
        if (e.target === modal) modal.remove();
    };

    // 다운로드 버튼
    const downloadBtn = modal.querySelector('.download-btn');
    downloadBtn.onclick = () => {
        const link = document.createElement('a');
        link.download = 'qrcode.png';
        link.href = qrDataUrl;
        link.click();
    };

    // 프린트 버튼
    const printBtn = modal.querySelector('.print-btn');
    printBtn.onclick = () => {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <style>
                    body { margin: 20px; text-align: center; }
                    img { max-width: 100%; height: auto; }
                    .content { margin-top: 10px; }
                </style>
            </head>
        document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.copy-button');
    const qrButtons = document.querySelectorAll('.qr-button');

    buttons.forEach(button => {
        button.addEventListener('click', function () {
            const text = this.getAttribute('data-clipboard-text');
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            // 버튼 텍스트 변경
            const originalText = this.textContent;
            this.textContent = 'Copied!';
            setTimeout(() => {
                this.textContent = originalText;
            }, 2000);
        });
    });

    // QR 코드 버튼 처리
    qrButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const content = this.getAttribute('data-clipboard-text');
            const size = parseInt(this.getAttribute('data-qr-size') || '200', 10);
            
            try {
                // QR 코드 생성
                const QRCode = await import('qrcode');
                const qrDataUrl = await QRCode.toDataURL(content, {
                    width: size,
                    margin: 1,
                });

                // 모달 생성 및 표시
                const modal = createQRModal(qrDataUrl, content);
                document.body.appendChild(modal);
            } catch (err) {
                console.error('QR 코드 생성 실패:', err);
            }
        });
    });
});

function createQRModal(qrDataUrl, content) {
    const modal = document.createElement('div');
    modal.className = 'qr-modal';
    modal.innerHTML = `
        <div class="qr-modal-content">
            <span class="close">&times;</span>
            <img src="${qrDataUrl}" alt="QR Code" />
            <div class="qr-actions">
                <button class="download-btn">Download</button>
                <button class="print-btn">Print</button>
            </div>
        </div>
    `;

    // 닫기 버튼
    const closeBtn = modal.querySelector('.close');
    closeBtn.onclick = () => modal.remove();

    // 배경 클릭시 닫기
    modal.onclick = (e) => {
        if (e.target === modal) modal.remove();
    };

    // 다운로드 버튼
    const downloadBtn = modal.querySelector('.download-btn');
    downloadBtn.onclick = () => {
        const link = document.createElement('a');
        link.download = 'qrcode.png';
        link.href = qrDataUrl;
        link.click();
    };

    // 프린트 버튼
    const printBtn = modal.querySelector('.print-btn');
    printBtn.onclick = () => {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <style>
                    body { margin: 20px; text-align: center; }
                    img { max-width: 100%; height: auto; }
                    .content { margin-top: 10px; }
                </style>
            </head>
            <body>
                <img src="${qrDataUrl}" />
                <div class="content">${content}</div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    };

    return modal;
}    <body>
                <img src="${qrDataUrl}" />
                <div class="content">${content}</div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    };

    return modal;
}