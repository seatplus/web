
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue'
/*
* Install bootstrap-vue components
*/
/*import BootstrapVue from 'bootstrap-vue' //Importing
Vue.use(BootstrapVue); // Telling Vue to use BootstrapVue in whole application*/

import { App, plugin } from '@inertiajs/inertia-vue'
Vue.use(plugin); // Telling Vue to use InertiaApp in whole application

import I18n from './vendor/I18n'
window.I18n = I18n
Vue.prototype.$I18n = new I18n;

// Create EventBus
const eventBus = new Vue();
Vue.prototype.$eventBus = eventBus

// Add route helper to vue
Vue.prototype.$route = (...args) => route(...args).url()
/*Vue.mixin({
  methods: {
    route: window.route
  }
})*/

import { InertiaProgress } from '@inertiajs/progress'

InertiaProgress.init({
    // The delay after which the progress bar will
    // appear during navigation, in milliseconds.
    delay: 250,

    // The color of the progress bar.
    color: '#29d',

    // Whether to include the default NProgress styles.
    includeCSS: true,

    // Whether the NProgress spinner will be shown.
    showSpinner: false,
})

const app = document.getElementById('app')

new Vue({
  render: h => h(App, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
    },
  }),
}).$mount(app)
