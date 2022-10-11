const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
// public/js にapp.js保存(ブラウザはpublic/js/app.jsを使用),versionで<script src="/js/app.js?id=dadc3a844ded5d18d741">等採番

mix.js('resources/js/app.js', 'public/js')
    .version();
