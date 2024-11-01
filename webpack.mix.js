const path        = require('path')
const mix         = require('laravel-mix')
const webpack     = require('webpack')
const { version } = require('./package.json')
// const OfflinePlugin  = require('offline-plugin')
// const UglifyJSPlugin = require('uglifyjs-webpack-plugin')

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

mix.js('resources/js/app.js', 'public/js')
mix.sass('resources/sass/app.scss', 'public/css')
mix.webpackConfig({
  resolve: {
    alias: {
      '@'     : path.resolve(__dirname, 'resources/js/coreui/'),
      'static': path.resolve(__dirname, 'resources/static/'),
    },
  },
  plugins: [
    // new UglifyJSPlugin({
    //   test         : /\.js($|\?)/i,
    //   cache        : false,
    //   parallel     : false,
    //   sourceMap    : true,
    //   uglifyOptions: { compress: true },
    // }),
    new webpack.DefinePlugin({ __VERSION: JSON.stringify(version) }),
    // new OfflinePlugin({
    //   publicPath      : '/',
    //   appShell        : '/',
    //   responseStrategy: 'network-first',
    //   externals       : [
    //     '/',
    //     '/manifest.json',
    //     '/favicon.png',
    //   ],
    //   ServiceWorker: {
    //     entry : path.resolve(__dirname, 'resources/js/sw.js'),
    //     output: 'sw.js',
    //     minify: mix.inProduction(),
    //   },
    // }),
  ],
})

mix.extend('vueOptions', (webpackConfig, vueOptions, ...args) => {
  const vueLoader   = webpackConfig.module.rules.find((loader) => loader.loader === 'vue-loader')
  vueLoader.options = require('webpack-merge').smart(vueLoader.options, vueOptions)
})

mix.vueOptions({
  transformToRequire: {
    'img'             : 'src',
    'image'           : 'xlink:href',
    'b-img'           : 'src',
    'b-img-lazy'      : ['src', 'blank-src'],
    'b-card'          : 'img-src',
    'b-card-img'      : 'img-src',
    'b-carousel-slide': 'img-src',
    'b-embed'         : 'src',
  },
})

mix.extract([
  'axios',
  'bootstrap',
  'bootstrap-vue',
  'chart.js',
  'css-vars-ponyfill',
  'file-saver',
  'iview',
  'jquery',
  'lodash',
  'mini-toastr',
  'moment',
  'perfect-scrollbar',
  'popper.js',
  'quill',
  'select2',
  'sweetalert2',
  'text-mask-addons',
  'v-calendar',
  'vee-validate',
  'vue',
  'vue-authenticate',
  'vue-axios',
  'vue-chartjs',
  'vue-codemirror',
  'vue-cookies',
  'vue-events',
  'vue-good-table',
  'vue-grid-layout',
  'vue-js-modal',
  'vue-js-toggle-button',
  'vue-json-excel',
  'vue-jstree',
  'vue-loading-spinner',
  'vue-lodash',
  'vue-monthly-picker',
  'vue-mq',
  'vue-multiselect',
  'vue-notification',
  'vue-notifications',
  'vue-numeric',
  'vue-perfect-scrollbar',
  'vue-quill-editor',
  'vue-resize',
  'vue-router',
  'vue-select',
  'vue-simple-calendar',
  'vue-sweetalert2',
  'vue-tables-2',
  'vue-text-mask',
  'vue-uuid',
  'vue2-autocomplete-js',
  'vue2-datatable-component',
  'vue2-datepicker',
  'vue2-filters',
  'vue2-google-maps',
  // 'vue2-slot-calendar',
  'vuejs-datepicker',
  'vuelidate',
  'vuex',
  'vuex-easy-access',
  'written-number',
])

// mix.options({
//   hmrOptions: {
//     host: process.env.MIX_HMR_HOST,
//     port: process.env.MIX_HMR_PORT,
//   },
//   uglify: {
//     cache        : false,
//     parallel     : false,
//     sourceMap    : true,
//     uglifyOptions: { compress: true },
//   },
// })

// if (mix.inProduction())
//   mix.version()
// else
//   mix.sourceMaps()

// if (process.platform === 'darwin')
//   mix.disableNotifications()
