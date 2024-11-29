import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import {
    useBlockProps,
    RichText,
    InspectorControls,
} from '@wordpress/block-editor';
import {
    PanelBody,
    ColorPicker,
    RangeControl,
    SelectControl,
    TextControl,
    ToggleControl,
    Button,
} from '@wordpress/components';

/**
 * Edit component for the Copy to Clipboard block
 */
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
        allowShortcode,
    } = attributes;

    // 블록 스타일
    const blockStyle = {
        backgroundColor: backgroundColor || '#ffffff',
        color: textColor || '#000000',
        borderStyle: borderStyle || 'solid',
        borderWidth: `${borderWidth || 1}px`,
        borderColor: borderColor || '#dddddd',
        borderRadius: `${borderRadius || 4}px`,
        padding: '20px',
    };

    // 미리보기용 복사 기능
    const handlePreviewCopy = async () => {
        try {
            await navigator.clipboard.writeText(content);
            // 성공 메시지를 표시할 수 있습니다
        } catch (err) {
            console.error('Copy failed:', err);
        }
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Block Settings', 'rena-block')} initialOpen={true}>
                    {/* 색상 설정 */}
                    <div className="components-base-control">
                        <label className="components-base-control__label">
                            {__('Background Color', 'rena-block')}
                        </label>
                        <ColorPicker
                            color={backgroundColor}
                            onChange={(color) => setAttributes({ backgroundColor: color })}
                            enableAlpha
                        />
                    </div>

                    <div className="components-base-control">
                        <label className="components-base-control__label">
                            {__('Text Color', 'rena-block')}
                        </label>
                        <ColorPicker
                            color={textColor}
                            onChange={(color) => setAttributes({ textColor: color })}
                            enableAlpha
                        />
                    </div>

                    {/* 테두리 설정 */}
                    <SelectControl
                        label={__('Border Style', 'rena-block')}
                        value={borderStyle}
                        options={[
                            { label: 'Solid', value: 'solid' },
                            { label: 'Dashed', value: 'dashed' },
                            { label: 'Dotted', value: 'dotted' },
                            { label: 'None', value: 'none' },
                        ]}
                        onChange={(value) => setAttributes({ borderStyle: value })}
                    />

                    <RangeControl
                        label={__('Border Width', 'rena-block')}
                        value={borderWidth}
                        onChange={(value) => setAttributes({ borderWidth: value })}
                        min={0}
                        max={10}
                        step={1}
                    />

                    <RangeControl
                        label={__('Border Radius', 'rena-block')}
                        value={borderRadius}
                        onChange={(value) => setAttributes({ borderRadius: value })}
                        min={0}
                        max={20}
                        step={1}
                    />

                    <div className="components-base-control">
                        <label className="components-base-control__label">
                            {__('Border Color', 'rena-block')}
                        </label>
                        <ColorPicker
                            color={borderColor}
                            onChange={(color) => setAttributes({ borderColor: color })}
                            enableAlpha
                        />
                    </div>

                    {/* 버튼 설정 */}
                    <TextControl
                        label={__('Copy Button Text', 'rena-block')}
                        value={buttonSettings?.copyButton?.text || ''}
                        onChange={(value) => setAttributes({
                            buttonSettings: {
                                ...buttonSettings,
                                copyButton: {
                                    ...buttonSettings?.copyButton,
                                    text: value
                                }
                            }
                        })}
                    />

                    <TextControl
                        label={__('QR Button Text', 'rena-block')}
                        value={buttonSettings?.qrButton?.text || ''}
                        onChange={(value) => setAttributes({
                            buttonSettings: {
                                ...buttonSettings,
                                qrButton: {
                                    ...buttonSettings?.qrButton,
                                    text: value
                                }
                            }
                        })}
                    />

                    {/* 숏코드 허용 설정 */}
                    <ToggleControl
                        label={__('Allow Shortcodes', 'rena-block')}
                        checked={allowShortcode}
                        onChange={(value) => setAttributes({ allowShortcode: value })}
                        help={__('Enable shortcode processing in content', 'rena-block')}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...useBlockProps({ style: blockStyle })}>
                {/* 제목 입력 */}
                <RichText
                    tagName="h4"
                    value={title}
                    onChange={(value) => setAttributes({ title: value })}
                    placeholder={__('Enter title...', 'rena-block')}
                    className="copy-clipboard-title"
                />

                <div className="copy-clipboard-content-wrapper">
                    {/* 컨텐츠 입력 */}
                    <RichText
                        tagName="pre"
                        value={content}
                        onChange={(value) => setAttributes({ content: value })}
                        placeholder={__('Enter content to copy...', 'rena-block')}
                        className="copy-clipboard-content"
                    />

                    {/* 버튼 미리보기 */}
                    <div className="copy-clipboard-buttons">
                        <Button
                            variant="secondary"
                            onClick={handlePreviewCopy}
                            className="copy-button"
                            icon="clipboard"
                        >
                            {buttonSettings?.copyButton?.text || __('Copy', 'rena-block')}
                        </Button>

                        <Button
                            variant="secondary"
                            className="qr-button"
                            icon="qrcode"
                            disabled
                        >
                            {buttonSettings?.qrButton?.text || __('QR Code', 'rena-block')}
                        </Button>
                    </div>
                </div>
            </div>
        </>
    );
}