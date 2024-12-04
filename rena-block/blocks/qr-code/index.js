import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { 
    PanelBody, 
    TextControl, 
    ToggleControl, 
    RangeControl,
    ColorPicker,
    Button
} from '@wordpress/components';
import { QrCode } from 'lucide-react';

registerBlockType('rena-plugin/qr-code', {
    edit: function Edit({ attributes, setAttributes }) {
        const { 
            content, 
            buttonText, 
            showIcon, 
            bgColor, 
            fgColor, 
            size,
            buttonStyle
        } = attributes;
        
        const blockProps = useBlockProps();

        return (
            <div {...blockProps}>
                <InspectorControls>
                    <PanelBody title={__('QR Code Settings', 'rena-plugin')}>
                        <TextControl
                            label={__('Content', 'rena-plugin')}
                            value={content}
                            onChange={(value) => setAttributes({ content: value })}
                            help={__('Enter text or URL for QR code', 'rena-plugin')}
                        />
                        <TextControl
                            label={__('Button Text', 'rena-plugin')}
                            value={buttonText}
                            onChange={(value) => setAttributes({ buttonText: value })}
                        />
                        <ToggleControl
                            label={__('Show Icon', 'rena-plugin')}
                            checked={showIcon}
                            onChange={(value) => setAttributes({ showIcon: value })}
                        />
                        <RangeControl
                            label={__('QR Code Size', 'rena-plugin')}
                            value={size}
                            onChange={(value) => setAttributes({ size: value })}
                            min={128}
                            max={512}
                            step={32}
                        />
                    </PanelBody>

                    <PanelBody title={__('Colors', 'rena-plugin')}>
                        <div className="qr-color-settings">
                            <label>{__('Background Color', 'rena-plugin')}</label>
                            <ColorPicker
                                color={bgColor}
                                onChangeComplete={(value) => setAttributes({ bgColor: value.hex })}
                                disableAlpha
                            />
                            <label>{__('QR Code Color', 'rena-plugin')}</label>
                            <ColorPicker
                                color={fgColor}
                                onChangeComplete={(value) => setAttributes({ fgColor: value.hex })}
                                disableAlpha
                            />
                        </div>
                    </PanelBody>

                    <PanelBody title={__('Button Style', 'rena-plugin')}>
                        <div className="button-style-settings">
                            <label>{__('Button Color', 'rena-plugin')}</label>
                            <ColorPicker
                                color={buttonStyle.backgroundColor}
                                onChangeComplete={(value) => 
                                    setAttributes({ 
                                        buttonStyle: {
                                            ...buttonStyle,
                                            backgroundColor: value.hex
                                        }
                                    })
                                }
                                disableAlpha
                            />
                            <label>{__('Button Text Color', 'rena-plugin')}</label>
                            <ColorPicker
                                color={buttonStyle.textColor}
                                onChangeComplete={(value) => 
                                    setAttributes({ 
                                        buttonStyle: {
                                            ...buttonStyle,
                                            textColor: value.hex
                                        }
                                    })
                                }
                                disableAlpha
                            />
                        </div>
                    </PanelBody>
                </InspectorControls>

                <div className="wp-block-rena-qr-code">
                    <div className="qr-content">
                        <TextControl
                            placeholder={__('Enter text or URL for QR code', 'rena-plugin')}
                            value={content}
                            onChange={(value) => setAttributes({ content: value })}
                        />
                    </div>
                    <Button 
                        className="qr-generate-button"
                        style={{
                            backgroundColor: buttonStyle.backgroundColor,
                            color: buttonStyle.textColor,
                            padding: buttonStyle.padding
                        }}
                        disabled
                    >
                        {showIcon && <QrCode className="qr-icon" size={16} />}
                        <span>{buttonText}</span>
                    </Button>
                    {content && (
                        <div className="qr-preview">
                            <img 
                                src={`/wp-json/rena-plugin/v1/qr-code?content=${encodeURIComponent(content)}&size=${size}&bgcolor=${encodeURIComponent(bgColor)}&color=${encodeURIComponent(fgColor)}`}
                                alt="QR Code Preview"
                                width={size}
                                height={size}
                            />
                        </div>
                    )}
                </div>
            </div>
        );
    },

    save: function Save({ attributes }) {
        const { 
            content, 
            buttonText, 
            showIcon,
            buttonStyle
        } = attributes;

        const blockProps = useBlockProps.save();

        return (
            <div {...blockProps}>
                <div className="wp-block-rena-qr-code">
                    <div className="qr-content">{content}</div>
                    <button 
                        className="qr-generate-button"
                        data-content={content}
                        style={{
                            backgroundColor: buttonStyle.backgroundColor,
                            color: buttonStyle.textColor,
                            padding: buttonStyle.padding
                        }}
                    >
                        {showIcon && <QrCode className="qr-icon" size={16} />}
                        <span>{buttonText}</span>
                    </button>
                </div>
            </div>
        );
    }
});