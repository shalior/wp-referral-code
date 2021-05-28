const path = require('path');
const CompressionPlugin = require("compression-webpack-plugin");
module.exports = {
    entry: './admin/js/main.js',
    output: {
        filename: 'main.min.js',
        path: path.resolve(__dirname, './admin/js'),
    },
    plugins: [new CompressionPlugin({
        include: /\/admin\/js/
    })],
};
