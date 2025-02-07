const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for ,defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */


mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/mobile.js', 'public/js')
    .js('resources/js/mobile/main.js', 'public/js/mobileVue.js')
    .js('resources/js/contract.js','public/js/contract.js')
    .sass('resources/sass/contract.scss', 'public/css')
    .sass('resources/sass/app.scss', 'public/css')
    .options({
        processCssUrls: true,
    })
    .version()
    .sass('resources/sass/mobile.scss', 'public/css')
    .copyDirectory('resources/images', 'public/images')
    .extract(['vue', 'sweetalert2', 'toastr', 'jquery', 'bootstrap'])
    .version();
