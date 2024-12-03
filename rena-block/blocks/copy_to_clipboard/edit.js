import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { 
    PanelBody, 
    ColorPicker,
    Button,
} from '@wordpress/components';
import { useState } from '@wordpress/element';

export default function Edit({ attributes, setAttributes }) {
    const [isCopied, setIsCopied] = useState(false);
    const blockProps = useBlockProps({
        className: 'wp-block-rena-copy-to-clipboard',
        style: {
            backgroundColor: attributes.backgroundColor,
            color: attributes.textColor,
        }
    });

    const handleCopy = async () => {
        try {
            await navigator.clipboard.writeText(attributes.content);
            setIsCopied(true);
            setTimeout(() => setIsCopied(false), 2000);
        } catch (err) {
            console.error('Failed to copy text: ', err);
        }
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Block Settings', 'rena-blocks')}>
                    <ColorPicker
                        label={__('Background Color', 'rena-blocks')}
                        color={attributes.backgroundColor}
                        onChangeComplete={(value) => 
                            setAttributes({ backgroundColor: value.hex })
                        }
                    />
                    <ColorPicker
                        label={__('Text Color', 'rena-blocks')}
                        color={attributes.textColor}
                        onChangeComplete={(value) => 
                            setAttributes({ textColor: value.hex })
                        }
                    />
                </PanelBody>
            </InspectorControls>
            <div {...blockProps}>
                <RichText
                    tagName="h4"
                    className="copy-title"
                    value={attributes.title}
                    onChange={(title) => setAttributes({ title })}
                    placeholder={__('Enter title...', 'rena-blocks')}
                />
                <RichText
                    tagName="pre"
                    className="copy-content"
                    value={attributes.content}
                    onChange={(content) => setAttributes({ content })}
                    placeholder={__('Enter content to copy...', 'rena-blocks')}
                />
                <Button
                    className={`copy-button ${isCopied ? 'copied' : ''}`}
                    onClick={handleCopy}
                    variant="primary"
                >
                    {isCopied ? __('Copied!', 'rena-blocks') : __('Copy', 'rena-blocks')}
                </Button>
            </div>
        </>
    );
}