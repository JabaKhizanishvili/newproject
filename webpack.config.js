const path = require('path');
// const BundleAnalyzerPlugin = require("webpack-bundle-analyzer").BundleAnalyzerPlugin;

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
        },
    },
    // plugins: [
    //     new BundleAnalyzerPlugin()
    //   ]
};

