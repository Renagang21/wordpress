const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
    ...defaultConfig,
    entry: {
        'copy-to-clipboard': './src/blocks/copy-to-clipboard/index.js'
    },
    output: {
        path: path.join(__dirname, 'build'),
        filename: '[name].js'
    }
};