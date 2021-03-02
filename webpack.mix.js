const mix = require('laravel-mix');
const path = require('path');
const tailwindcss = require('tailwindcss')

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
    .sass('src/resources/sass/app.scss', 'src/public/css')
    .webpackConfig({
      output : {
          chunkFilename: 'js/[name].js?id=[chunkhash]'
      },
      resolve: {
        alias: {
          vue$: 'vue/dist/vue.runtime.esm.js',
          '@' : path.resolve('src/resources/js'),
        },
      },
    })
    .babelConfig({
      plugins: ['@babel/plugin-syntax-dynamic-import'],
    })
    .options({
        processCssUrls: false,
        postCss: [ tailwindcss('./tailwind.config.js') ],
    })

if ( mix.inProduction()) {
  mix.version();
}

//mix.browserSync('seatplus.test');
