
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Layout from "@/Shared/SidebarLayout/Layout";
import { createApp, h } from 'vue'
import { App, plugin } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import I18n from './vendor/I18n';

require('./bootstrap');

// Create EventBus
/*const eventBus = new Vue();
Vue.prototype.$eventBus = eventBus;*/


/*Vue.mixin({
  methods: {
    route: window.route
  }
})*/

InertiaProgress.init()

const el = document.getElementById('app')

const app = createApp({
  render: () => h(App, {
      initialPage: JSON.parse(el.dataset.page),
      resolveComponent: name => import(`@/Pages/${name}`)
        //.then(module => module.default),
        .then(({ default: page }) => {
          if (page.layout === undefined) {
            page.layout = Layout
          }
          return page
        }),
  })
}).use(plugin)

app.mixin({methods: {
  title: title => `Seatplus - ${title}`,
}})

// Add route helper to vue
app.config.globalProperties.$route = (...args) => route(...args);

//window.I18n = I18n;
app.config.globalProperties.$I18n = new I18n;

app.mount(el)

/*new Vue({
  render: h => h(App, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
    },
  }),
}).$mount(app)*/
