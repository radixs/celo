// webpack.config.js
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    entry: {
        entry: __dirname + '/js/app.js'
    },
    output: {
        filename: 'celo-gui.bundle.js'
    },
    mode: "development",
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'celo-gui.bundle.css',
        })
    ],
    module: {
        rules: [
            {
                test: /\.css$/,
                use: [MiniCssExtractPlugin.loader, 'css-loader'],
            },
        ]
    }
};