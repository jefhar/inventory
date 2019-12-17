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
  react('resources/js/entrypoints/workorders/edit.js',
    'public/js/workorders/edit.js').
  react('resources/js/entrypoints/types/create.js',
    'public/js/types/create.js').
  sourceMaps().
  sass('resources/sass/app.scss', 'public/css')
mix.browserSync('localhost:8080')
