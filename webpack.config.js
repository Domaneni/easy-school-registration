const path = require( 'path' );
const webpack = require( 'webpack' );
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const IgnoreEmitPlugin = require('ignore-emit-webpack-plugin');

module.exports = {
    entry: {
        './inc/assets/admin/js/esr-production' : './assets/admin/js/esr-admin',
        './inc/assets/admin/js/esr-course-admin' : ['./assets/admin/js/course/esr-course-admin', './assets/admin/js/other/esr-datatable.js'],
        './inc/assets/admin/js/esr-wave-admin' : ['./assets/admin/js/wave/esr-wave-admin', './assets/admin/js/other/esr-datatable.js'],
        './inc/assets/admin/js/esr-teacher-admin' : ['./assets/admin/js/teacher/esr-teacher-admin', './assets/admin/js/other/esr-datatable.js'],
        './inc/assets/admin/js/esr-data-changing' : './assets/admin/js/esr-data-changing',
        './inc/assets/admin/js/esr-students' : './assets/admin/js/students/esr-students-admin',
        './inc/assets/admin/js/esr-student-admin' : './assets/admin/js/esr-student-admin',
        './inc/assets/admin/js/esr-mce-plugin' : './assets/admin/js/esr-mce-plugin',
        './inc/assets/web/css/esr-style' : './assets/web/scss/esr-style.scss',
        './inc/assets/admin/css/esr-admin-settings': './assets/admin/scss/esr-admin-settings.scss',
        './inc/assets/admin/css/esr-student-admin': './assets/admin/scss/esr-student-admin.scss',
        './inc/assets/admin/css/esr-menu-separator': './assets/admin/scss/esr-menu-separator.scss',
        './inc/assets/admin/css/esr-tinymce': './assets/admin/scss/esr-tinymce.scss',
    },
    output: {
        path: path.resolve( __dirname ),
        filename: '[name].js',
    },
    watch: 'production' !== process.env.NODE_ENV,
    module: {
        rules: [
            {
                test: /esr-style\.s?css$/,
                use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
            },
            {
                test: /esr-tinymce\.s?css$/,
                use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
            },
            {
                test: /esr-menu-separator\.s?css$/,
                use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
            },
            {
                test: /esr-student-admin\.s?css$/,
                use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
            },
            {
                test: /esr-admin-settings\.s?css$/,
                use: [MiniCssExtractPlugin.loader, {loader: 'css-loader', options: { url: false }}, 'sass-loader'],
            }
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name].min.css',
            //chunkFilename: 'chunk-[id][name].css',
            chunkFilename: './assets/css/[name].css',
        }),
        new IgnoreEmitPlugin(/styles\.js$/) // ignore extra emitted JS files from the CSS extraction process (shouldn't need to do this in webpack 5!)
    ],
};