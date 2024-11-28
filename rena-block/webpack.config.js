const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
    ...defaultConfig,
    entry: {
        'copy-to-clipboard': './blocks/copy_to_clipboard/index.js',
        'qr-code': './blocks/qr_code/index.js'
    },
    output: {
        path: path.join(__dirname, 'build'),
        filename: '[name].js'
    }
};