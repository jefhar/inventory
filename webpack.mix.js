const mix = require('laravel-mix')

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

mix.react('resources/js/app.js', 'public/js').
  react('resources/js/entrypoints/workorders/create.js',
    'public/js/workorders/create.js').
  react('resources/js/entrypoints/workorders/index.js',
    'public/js/workorders/index.js').
  sourceMaps().
  sass('resources/sass/app.scss', 'public/css')
mix.browserSync('localhost:8080')
