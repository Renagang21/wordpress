import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Save component for the Copy to Clipboard block
 * Handles the block's HTML output on the frontend
 * 
 * @param {Object} props Block properties
 * @returns {JSX.Element} Block save content
 */
export default function save({ attributes }) {
    const {
        title,
        content,
        parsedContent,
        backgroundColor,
        textColor,
        borderStyle,
        borderWidth,
        borderColor,
        borderRadius,
        buttonSettings,
        allowShortcode
    } = attributes;

    // 블록 스타일 정의
    const blockStyle = {
        backgroundColor: backgroundColor || '#ffffff',
        color: textColor || '#000000',
        borderStyle: borderStyle || 'solid',
        borderWidth: `${borderWidth || 1}px`,
        borderColor: borderColor || '#dddddd',
        borderRadius: `${borderRadius || 4}px`,
        padding: '20px',
        position: 'relative'
    };

    // 표시할 컨텐츠 결정 (파싱된 컨텐츠 우선)
    const displayContent = allowShortcode ? (parsedContent || content) : content;

    // 버튼 텍스트 및 아이콘 설정
    const copyButtonText = buttonSettings?.copyButton?.text || __('Copy', 'rena-block');
    const qrButtonText = buttonSettings?.qrButton?.text || __('QR Code', 'rena-block');

    // 버튼 아이콘 표시 여부
    const showCopyIcon = buttonSettings?.copyButton?.showIcon ?? true;
    const showQrIcon = buttonSettings?.qrButton?.showIcon ?? true;

    // Save 구조
    return (
        <div {...useBlockProps.save({ style: blockStyle })}>
            {title && (
                <h4 className="copy-clipboard-title">
                    {title}
                </h4>
            )}
            
            <div className="copy-clipboard-content-wrapper">
                {/* 컨텐츠 영역 */}
                <pre 
                    className="copy-clipboard-content"
                    dangerouslySetInnerHTML={{ __html: displayContent }}
                />
                
                {/* 버튼 영역 */}
                <div className="copy-clipboard-buttons">
                    {/* 복사 버튼 */}
                    <button
                        type="button"
                        className="copy-button"
                        data-clipboard-text={displayContent}
                        aria-label={__('Copy to clipboard', 'rena-block')}
                    >
                        {showCopyIcon && (
                            <span className="dashicons dashicons-clipboard"></span>
                        )}
                        <span className="button-text">{copyButtonText}</span>
                    </button>
                    
                    {/* QR 코드 버튼 */}
                    <button
                        type="button"
                        className="qr-button"
                        data-content={displayContent}
                        aria-label={__('Generate QR Code', 'rena-block')}
                    >
                        {showQrIcon && (
                            <span className="dashicons dashicons-qrcode"></span>
                        )}
                        <span className="button-text">{qrButtonText}</span>
                    </button>
                </div>
            </div>

            {/* 접근성을 위한 설명 */}
            <div className="screen-reader-text">
                {__('Use the copy button to copy the content, or generate a QR code', 'rena-block')}
            </div>

            {/* 숏코드 처리 여부 표시 */}
            {allowShortcode && (
                <div className="shortcode-processed" aria-hidden="true" />
            )}
        </div>
    );
}