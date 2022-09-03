import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import SingleColumnLayout from "@/Shared/SidebarLayout/SingleColumnLayout.vue";
import I18n from '@/vendor/I18n';

const I18nPlugin = {
    install(app) {
        app.config.globalProperties.$I18n = new I18n();
    }
}

createInertiaApp({
    resolve: (name) => {
        const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));

        page.then((module) => {
            module.default.layout = module.default.layout || SingleColumnLayout;
        });

        return page;

    },
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(I18nPlugin)
            .mixin({ methods: {title: title => `${title} - Seatplus`,}})
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });