import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import Save from './save';
import './style.scss';
import './editor.scss';

registerBlockType('rena/copy-to-clipboard', {
    apiVersion: 2,
    title: __('Copy to Clipboard', 'rena-blocks'),
    description: __('A block that allows users to copy content to clipboard with shortcode support.', 'rena-blocks'),
    category: 'widgets',
    icon: 'clipboard',
    supports: {
        html: false,
        align: true,
    },
    parent: ['core/column'],
    attributes: {
        title: {
            type: 'string',
            source: 'html',
            selector: '.copy-title',
            default: '',
        },
        content: {
            type: 'string',
            source: 'html',
            selector: 'pre',
            default: '',
        },
        backgroundColor: {
            type: 'string',
            default: '#f8f8f8',
        },
        textColor: {
            type: 'string',
            default: '#333333',
        },
    },
    edit: Edit,
    save: Save,
});