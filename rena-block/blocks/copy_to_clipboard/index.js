import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { 
    PanelBody, 
    ToggleControl, 
    TextControl,
    ColorPicker,
    RangeControl,
    SelectControl,
    Button,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { dispatch } from '@wordpress/data';

// 스타일 import
import './editor.scss';

// 블록 에디터
export default function Edit({ attributes, setAttributes, isSelected }) {
    const {
        title,
        content,
        backgroundColor,
        textColor,
        borderStyle,
        borderWidth,
        borderColor,
        borderRadius,
        buttonSettings,
        isColumnOnly,
        allowShortcode,
    } = attributes;

    // Copy 기능 상태 관리
    const [isCopied, setIsCopied] = useState(false);

    // 복사 기능
    const handleCopy = async () => {
        try {
            await navigator.clipboard.writeText(content);
            setIsCopied(true);
            setTimeout(() => setIsCopied(false), 2000);
        } catch (err) {
            console.error('Failed to copy text: ', err);
        }
    };

    // QR 코드 생성 모달 상태
    const [isQRModalOpen, setIsQRModalOpen] = useState(false);

    // 블록 스타일
    const blockStyle = {
        backgroundColor,
        color: textColor,
        borderStyle,
        borderWidth: `${borderWidth}px`,
        borderColor,
        borderRadius: `${borderRadius}px`,
        padding: '20px',
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Block Settings', 'rena-block')}>
                    <ColorPicker
                        label={__('Background Color', 'rena-block')}
                        color={backgroundColor}
                        onChange={(color) => setAttributes({ backgroundColor: color })}
                    />
                    <ColorPicker
                        label={__('Text Color', 'rena-block')}
                        color={textColor}
                        onChange={(color) => setAttributes({ textColor: color })}
                    />
                    <SelectControl
                        label={__('Border Style', 'rena-block')}
                        value={borderStyle}
                        options={[
                            { label: 'Solid', value: 'solid' },
                            { label: 'Dashed', value: 'dashed' },
                            { label: 'Dotted', value: 'dotted' },
                        ]}
                        onChange={(value) => setAttributes({ borderStyle: value })}
                    />
                    <RangeControl
                        label={__('Border Width', 'rena-block')}
                        value={borderWidth}
                        onChange={(value) => setAttributes({ borderWidth: value })}
                        min={0}
                        max={10}
                    />
                    <RangeControl
                        label={__('Border Radius', 'rena-block')}
                        value={borderRadius}
                        onChange={(value) => setAttributes({ borderRadius: value })}
                        min={0}
                        max={20}
                    />
                    <TextControl
                        label={__('Copy Button Text', 'rena-block')}
                        value={buttonSettings.copyButton.text}
                        onChange={(value) => setAttributes({
                            buttonSettings: {
                                ...buttonSettings,
                                copyButton: {
                                    ...buttonSettings.copyButton,
                                    text: value
                                }
                            }
                        })}
                    />
                    <TextControl
                        label={__('QR Button Text', 'rena-block')}
                        value={buttonSettings.qrButton.text}
                        onChange={(value) => setAttributes({
                            buttonSettings: {
                                ...buttonSettings,
                                qrButton: {
                                    ...buttonSettings.qrButton,
                                    text: value
                                }
                            }
                        })}
                    />
                    <ToggleControl
                        label={__('Allow Shortcode', 'rena-block')}
                        checked={allowShortcode}
                        onChange={(value) => setAttributes({ allowShortcode: value })}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...useBlockProps({ style: blockStyle })}>
                <RichText
                    tagName="h4"
                    value={title}
                    onChange={(value) => setAttributes({ title: value })}
                    placeholder={__('Enter title...', 'rena-block')}
                    className="copy-clipboard-title"
                />
                
                <div className="copy-clipboard-content-wrapper">
                    <RichText
                        tagName="pre"
                        value={content}
                        onChange={(value) => setAttributes({ content: value })}
                        placeholder={__('Enter content to copy...', 'rena-block')}
                        className="copy-clipboard-content"
                    />
                    
                    <div className="copy-clipboard-buttons">
                        <Button
                            variant="secondary"
                            onClick={handleCopy}
                            className={`copy-button ${isCopied ? 'copied' : ''}`}
                            icon={isCopied ? 'yes' : 'clipboard'}
                        >
                            {isCopied ? __('Copied!', 'rena-block') : buttonSettings.copyButton.text}
                        </Button>
                        
                        <Button
                            variant="secondary"
                            onClick={() => setIsQRModalOpen(true)}
                            className="qr-button"
                            icon="qrcode"
                        >
                            {buttonSettings.qrButton.text}
                        </Button>
                    </div>
                </div>
            </div>

            {isQRModalOpen && (
                <div className="qr-modal">
                    {/* QR 코드 모달 내용은 별도 컴포넌트로 구현 예정 */}
                    <Button
                        variant="secondary"
                        onClick={() => setIsQRModalOpen(false)}
                    >
                        {__('Close', 'rena-block')}
                    </Button>
                </div>
            )}
        </>
    );
}