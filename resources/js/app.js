import './bootstrap';
import '../css/app.css';

import { createApp, defineComponent, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import SingleColumnLayout from "@/Shared/SidebarLayout/SingleColumnLayout.vue";
import I18n from '@/vendor/I18n';

const I18nPlugin = {
    install(app) {
        app.config.globalProperties.$I18n = new I18n();
    }
}

// Pages with `layout: null` embed their own full-screen layout (e.g. MultiColumnLayout).
// Inertia v3 crashes (parentNode null) when swapping from a layout component to literal null.
// Using a transparent pass-through component prevents the crash while still rendering the
// page's own embedded layout correctly.
const TransparentLayout = defineComponent({
    name: 'TransparentLayout',
    setup(_, { slots }) {
        return () => slots.default?.()
    }
})

createInertiaApp({
    resolve: (name) => {
        const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));

        page.then((module) => {
            if (module.default.layout === undefined) {
                module.default.layout = SingleColumnLayout;
            } else if (module.default.layout === null) {
                module.default.layout = TransparentLayout;
            }
        });

        return page;

    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(I18nPlugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563'
    }
});
