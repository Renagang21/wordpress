import { __ } from '@wordpress/i18n';
import { PanelBody, ColorPicker, TextControl } from '@wordpress/components';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { useState, useEffect, useRef } from 'react';

const Edit = ({ attributes, setAttributes }) => {
  const { backgroundColor, buttonText, buttonColor } = attributes;
  const [isCopied, setIsCopied] = useState(false);
  const copyButtonRef = useRef(null);

  const handleCopy = () => {
    const textToCopy = 'Text to be copied';
    navigator.clipboard.writeText(textToCopy);
    setIsCopied(true);
    setTimeout(() => setIsCopied(false), 2000);
  };

  useEffect(() => {
    if (isCopied && copyButtonRef.current) {
      copyButtonRef.current.focus();
    }
  }, [isCopied]);

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Block Settings', 'rena-block')}>
          <ColorPicker
            label={__('Background Color', 'rena-block')}
            color={backgroundColor}
            onChangeComplete={(color) =>
              setAttributes({ backgroundColor: color.hex })
            }
          />
          <TextControl
            label={__('Button Text', 'rena-block')}
            value={buttonText}
            onChange={(text) => setAttributes({ buttonText: text })}
          />
          <ColorPicker
            label={__('Button Color', 'rena-block')}
            color={buttonColor}
            onChangeComplete={(color) =>
              setAttributes({ buttonColor: color.hex })
            }
          />
        </PanelBody>
      </InspectorControls>
      <div
        {...useBlockProps({
          className: 'rena-block-copy-to-clipboard',
          style: { backgroundColor },
        })}
      >
        <button
          ref={copyButtonRef}
          className="copy-button"
          style={{ backgroundColor: buttonColor }}
          onClick={handleCopy}
          aria-label={isCopied ? __('Copied', 'rena-block') : __('Copy to clipboard', 'rena-block')}
        >
          {isCopied ? __('Copied', 'rena-block') : buttonText}
        </button>
      </div>
    </>
  );
};

export default Edit;