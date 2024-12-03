import { useBlockProps, RichText } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

export default function Save({ attributes }) {
    const blockProps = useBlockProps.save({
        className: 'wp-block-rena-copy-to-clipboard',
        style: {
            backgroundColor: attributes.backgroundColor,
            color: attributes.textColor,
        }
    });

    return (
        <div {...blockProps}>
            <RichText.Content
                tagName="h4"
                className="copy-title"
                value={attributes.title}
            />
            <RichText.Content
                tagName="pre"
                className="copy-content"
                value={attributes.content}
            />
            <button 
                className="copy-button" 
                data-clipboard-text={attributes.content}
                type="button"
            >
                {__('Copy', 'rena-blocks')}
            </button>
        </div>
    );
}