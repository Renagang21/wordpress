import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import Save from './save';
import './style.css';
import './editor.css';

registerBlockType('rena/copy-to-clipboard', {
    apiVersion: 3,
    title: __('Copy to Clipboard', 'rena-blocks'),
    description: __('A block that allows users to copy content to clipboard with QR code support', 'rena-blocks'),
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
        qrCodeSize: {
            type: 'number',
            default: 200,
        },
        printLayout: {
            type: 'object',
            default: {
                codesPerRow: 2,
                codeSize: 50,
                showText: true,
            },
        },
    },
    edit: Edit,
    save: Save,
});