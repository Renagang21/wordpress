const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { useBlockProps, InspectorControls } = wp.blockEditor;
const {
    PanelBody,
    TextControl,
    ToggleControl,
    ColorPicker
} = wp.components;

// Dashicons의 'clipboard' 아이콘 사용
const ClipboardIcon = 'clipboard'; // Dashicons 기본 제공 아이콘

registerBlockType('rena-plugin/copy-to-clipboard', {
    icon: ClipboardIcon, // Dashicons 아이콘 설정

    edit: ({ attributes, setAttributes }) => {
        const {
            shortcode,
            copyButtonText,
            qrButtonText,
            showIcons,
            buttonStyle,
            containerStyle
        } = attributes;

        const blockProps = useBlockProps();

        return (
            <div {...blockProps}>
                <InspectorControls>
                    <PanelBody title={__('Button Settings', 'rena-plugin')}>
                        <TextControl
                            label={__('Copy Button Text', 'rena-plugin')}
                            value={copyButtonText || ''}
                            onChange={(value) =>
                                setAttributes({ copyButtonText: value })
                            }
                        />
                        <TextControl
                            label={__('QR Code Button Text', 'rena-plugin')}
                            value={qrButtonText || ''}
                            onChange={(value) =>
                                setAttributes({ qrButtonText: value })
                            }
                        />
                        <ToggleControl
                            label={__('Show Icons', 'rena-plugin')}
                            checked={showIcons}
                            onChange={(value) =>
                                setAttributes({ showIcons: value })
                            }
                        />
                    </PanelBody>

                    <PanelBody title={__('Style Settings', 'rena-plugin')}>
                        <label>{__('Button Background', 'rena-plugin')}</label>
                        <ColorPicker
                            color={buttonStyle?.backgroundColor || '#007cba'}
                            onChangeComplete={(value) =>
                                setAttributes({
                                    buttonStyle: {
                                        ...buttonStyle,
                                        backgroundColor: value.hex
                                    }
                                })
                            }
                        />
                        <label>{__('Button Text Color', 'rena-plugin')}</label>
                        <ColorPicker
                            color={buttonStyle?.textColor || '#ffffff'}
                            onChangeComplete={(value) =>
                                setAttributes({
                                    buttonStyle: {
                                        ...buttonStyle,
                                        textColor: value.hex
                                    }
                                })
                            }
                        />
                    </PanelBody>
                </InspectorControls>

                <div
                    className="wp-block-rena-copy-to-clipboard"
                    style={containerStyle}
                >
                    <TextControl
                        label={__('Shortcode', 'rena-plugin')}
                        value={shortcode || ''}
                        onChange={(value) =>
                            setAttributes({ shortcode: value })
                        }
                        help={__(
                            'Enter shortcode in format: [Title]<pre>Content</pre>',
                            'rena-plugin'
                        )}
                    />
                    <div className="button-container">
                        <button
                            className="copy-button"
                            style={buttonStyle}
                            disabled
                        >
                            {showIcons && (
                                <span className="dashicon dashicons-clipboard" />
                            )}
                            <span>{copyButtonText || 'Copy'}</span>
                        </button>

                        <button
                            className="qr-button"
                            style={buttonStyle}
                            disabled
                        >
                            {showIcons && (
                                <span className="dashicon dashicons-admin-site-alt3" />
                            )}
                            <span>{qrButtonText || 'QR Code'}</span>
                        </button>
                    </div>
                </div>
            </div>
        );
    },

    save: ({ attributes }) => {
        const {
            title,
            parsedContent,
            copyButtonText,
            qrButtonText,
            showIcons,
            buttonStyle,
            containerStyle
        } = attributes;

        const blockProps = useBlockProps.save();

        return (
            <div {...blockProps}>
                <div
                    className="wp-block-rena-copy-to-clipboard"
                    style={containerStyle}
                >
                    <h4>{title}</h4>
                    <div
                        className="copyable-content"
                        data-content={parsedContent}
                    >
                        {parsedContent}
                    </div>
                    <div className="button-container">
                        <button
                            className="copy-button"
                            data-clipboard-text={parsedContent}
                            style={buttonStyle}
                        >
                            {showIcons && (
                                <span className="dashicon dashicons-clipboard" />
                            )}
                            <span>{copyButtonText || 'Copy'}</span>
                        </button>

                        <button
                            className="qr-button"
                            data-content={parsedContent}
                            style={buttonStyle}
                        >
                            {showIcons && (
                                <span className="dashicon dashicons-admin-site-alt3" />
                            )}
                            <span>{qrButtonText || 'QR Code'}</span>
                        </button>
                    </div>
                </div>
            </div>
        );
    }
});
