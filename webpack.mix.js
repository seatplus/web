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

mix.setPublicPath('src/public');

mix.js('src/resources/js/app.js', 'src/public/js')
   .sass('src/resources/sass/app.scss', 'src/public/css');


if (! mix.inProduction()) {
  mix.copyDirectory('src/public', '../../../public')
}

mix.browserSync('seatplus.test');
