const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.copyDirectory('resources/assets', 'public/assets')
    .vue()
    .copyDirectory('node_modules/summernote/dist/font', 'public/assets/css/font')
    .copy('node_modules/summernote/dist/summernote.min.css', 'public/assets/css/summernote.min.css')
    .copy('node_modules/summernote/dist/summernote.min.js', 'public/assets/js/summernote.min.js')
    .js('resources/js/app.js', 'public/js')
    .version();

