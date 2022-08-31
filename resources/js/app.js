import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import SingleColumnLayout from "@/Shared/SidebarLayout/SingleColumnLayout.vue";


createInertiaApp({
    //title: (title) => `${title} - Seatplus`,
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
            .mixin({ methods: {title: title => `${title} - Seatplus`,}})
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });