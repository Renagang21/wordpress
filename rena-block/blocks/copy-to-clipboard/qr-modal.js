import { __ } from '@wordpress/i18n';
import { Modal, Button, RangeControl } from '@wordpress/components';
import QRCode from 'qrcode';
import { useState, useEffect } from '@wordpress/element';

export default function QRModal({ isOpen, onClose, content, size, onSizeChange }) {
    const [qrDataUrl, setQrDataUrl] = useState('');
    const [printSettings, setPrintSettings] = useState({
        codesPerRow: 2,
        codeSize: 50,
        showText: true,
    });

    useEffect(() => {
        if (content) {
            generateQR();
        }
    }, [content, size]);

    const generateQR = async () => {
        try {
            const url = await QRCode.toDataURL(content, {
                width: size,
                margin: 1,
                color: {
                    dark: '#000000',
                    light: '#ffffff',
                },
            });
            setQrDataUrl(url);
        } catch (err) {
            console.error('QR 생성 에러:', err);
        }
    };

    const handleDownload = () => {
        const link = document.createElement('a');
        link.download = 'qrcode.png';
        link.href = qrDataUrl;
        link.click();
    };

    const handleCopyQR = async () => {
        try {
            const response = await fetch(qrDataUrl);
            const blob = await response.blob();
            await navigator.clipboard.write([
                new ClipboardItem({
                    [blob.type]: blob,
                }),
            ]);
        } catch (err) {
            console.error('QR 코드 복사 실패:', err);
        }
    };

    const handlePrint = () => {
        const printWindow = window.open('', '_blank');
        const html = generatePrintHTML();
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.print();
    };

    const generatePrintHTML = () => {
        const { codesPerRow, codeSize, showText } = printSettings;
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
                    ${showText ? '.qr-text { margin-top: 10px; }' : ''}
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
    };

    return (
        isOpen && (
            <Modal
                title={__('QR Code', 'rena-blocks')}
                onRequestClose={onClose}
                className="qr-code-modal"
            >
                <div className="qr-code-container">
                    {qrDataUrl && <img src={qrDataUrl} alt="QR Code" />}
                    <RangeControl
                        label={__('QR Code Size', 'rena-blocks')}
                        value={size}
                        onChange={onSizeChange}
                        min={100}
                        max={400}
                    />
                    <div className="qr-actions">
                        <Button variant="primary" onClick={handleDownload}>
                            {__('Download', 'rena-blocks')}
                        </Button>
                        <Button onClick={handleCopyQR}>
                            {__('Copy to Clipboard', 'rena-blocks')}
                        </Button>
                        <Button onClick={handlePrint}>
                            {__('Print', 'rena-blocks')}
                        </Button>
                    </div>
                    <div className="print-settings">
                        <RangeControl
                            label={__('Codes per row', 'rena-blocks')}
                            value={printSettings.codesPerRow}
                            onChange={(value) => setPrintSettings({...printSettings, codesPerRow: value})}
                            min={1}
                            max={4}
                        />
                        <RangeControl
                            label={__('Code size (mm)', 'rena-blocks')}
                            value={printSettings.codeSize}
                            onChange={(value) => setPrintSettings({...printSettings, codeSize: value})}
                            min={20}
                            max={100}
                        />
                    </div>
                </div>
            </Modal>
        )
    );
}