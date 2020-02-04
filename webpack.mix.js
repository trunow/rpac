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

mix.webpackConfig({
    output: {
        chunkFilename: 'vendor/rpac/[name].js',
    },
});
mix.js('resources/js/rpac.js', 'public/vendor/rpac');
mix.sass('resources/sass/rpac.scss', 'public/vendor/rpac');
