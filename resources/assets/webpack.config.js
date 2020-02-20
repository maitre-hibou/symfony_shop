(function () {
    'use strict';

    const   merge = require('webpack-merge'),
            path = require('path'),
            webpack = require('webpack'),
            { CleanWebpackPlugin } = require('clean-webpack-plugin'),
            MiniCssExtractPlugin  = require('mini-css-extract-plugin'),

            config = require('./build/config')
    ;

    let webpackConfig = {
        context: path.resolve(__dirname),
        entry: {
            theme: path.resolve(config.paths.assets, 'scripts/theme.js')
        },
        mode: config.isProduction ? 'production' : 'development',
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: [
                        { loader: 'buble-loader' }
                    ]
                },
                {
                    test: /\.s[a|c]ss$/,
                    exclude: /node_modules/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        { loader: 'css-loader', options: {sourceMap: config.isProduction} },
                        { loader: 'sass-loader', options: {sourceMaps: config.isProduction} }
                    ]
                },
                {
                    test: /images\/(.+).(jpe?g|png|gif|svg|ico)/,
                    include: config.paths.assets,
                    use: [
                        { loader: 'file-loader', options: { name: `img/${config.cacheBusting}.[ext]` } }
                    ]
                },
                {
                    test: /webfonts\/(.+).(eot|svg|woff|woff2|ttf)/,
                    use: {
                        loader: 'file-loader',
                        options: {
                            name: 'fonts/[name].[ext]',
                            publicPath: '/dist'
                        }
                    }
                }
            ]
        },
        output: {
            filename: `js/${config.cacheBusting}.js`,
            path: config.paths.dist,
            publicPath: config.distPublicPath
        },
        plugins: [
            new CleanWebpackPlugin({cleanOnceBeforeBuildPatterns: ['**/*', '!.gitkeep']}),
            new MiniCssExtractPlugin({
                filename: `css/${config.cacheBusting}.css`
            })
        ]
    };

    if (config.isProduction) {
        const   UglifyJSPlugin          = require('uglifyjs-webpack-plugin'),
                OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin'),
                WebpackAssetsManifest   = require('webpack-assets-manifest')
        ;

        let manifest = new WebpackAssetsManifest({
            output: 'assets.json',
            space: 2,
            writeToDisk: false,
            assets: {},
            publicPath: true
        });

        manifest.hooks.customize.tap('assets', function (entry) {
            const targetPath = path.basename(path.dirname(entry.value));

            let entryKey = `dist/${targetPath}/${entry.key}`;

            if (/^img$/.test(targetPath)) {
                entryKey = `dist/${entry.key}`;
            }

            return {
                key: entryKey,
                value: entry.value
            }
        });

        webpackConfig.plugins.push(
            new UglifyJSPlugin({
                cache: true,
                parallel: true,
                sourceMap: true
            }),
            new OptimizeCSSAssetsPlugin({}),
            manifest
        );
    }

    module.exports = merge(webpackConfig, {});
}());
