const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
    ...defaultConfig,
    entry: {
        'copy-to-clipboard': './src/copy-to-clipboard/index.js',
        'qr-code': './src/qr-code/index.js'
    },
    output: {
        path: path.join(__dirname, 'build'),
        filename: '[name].js'
    }
};
