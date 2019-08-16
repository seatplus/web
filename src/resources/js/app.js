
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
import BootstrapVue from 'bootstrap-vue' //Importing
Vue.use(BootstrapVue); // Telling Vue to use BootstrapVue in whole application

import { InertiaApp } from '@inertiajs/inertia-vue'
Vue.use(InertiaApp); // Telling Vue to use InertiaApp in whole application

// Add route helper to vue
Vue.mixin({ methods: { route: window.route } })

const app = document.getElementById('app')

new Vue({
  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
    },
  }),
}).$mount(app)
