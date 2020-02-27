(function () {
    'use strict';

    const   merge = require('webpack-merge'),
            path = require('path'),
            fs = require('fs'),

            projectRoot = path.resolve(__dirname, '../../../'),
            isProduction = process.env.NODE_ENV === 'production'
    ;

    require('dotenv').config({path: `${projectRoot}/.env`});

    if (fs.existsSync(`${projectRoot}/.env.local`)) {
        require('dotenv').config({path: `${projectRoot}/.env.local`});
    }

    let config = {
        cacheBusting: isProduction ? '[name]_[hash]' : '[name]',
        isProduction: isProduction,
        paths: {
            assets: path.resolve(projectRoot, 'resources/assets'),
            dist: path.resolve(projectRoot, 'public/dist'),
            root: projectRoot
        }
    };

    module.exports = merge(config, {
        distPublicPath: `${path.basename(config.paths.dist)}/`,
        appUrl: process.env.APP_URL
    });
}());
