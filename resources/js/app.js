import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/index';
import SingleColumnLayout from "@/Shared/SidebarLayout/SingleColumnLayout.vue";
import I18n from '@/vendor/I18n';
import { I18nKey } from '@/composables/useTranslations';

const i18n = new I18n();

const I18nPlugin = {
    install(app) {
        // Options-API global for un-migrated pages (`this.$I18n`)…
        app.config.globalProperties.$I18n = i18n;
        // …and provide the same instance for the `useTranslations()` composable (`<script setup>`).
        app.provide(I18nKey, i18n);
    }
}

createInertiaApp({
    resolve: (name) => {
        const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));

        page.then((module) => {
            if (module.default.layout === undefined) {
                module.default.layout = SingleColumnLayout;
            }
        });

        return page;

    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(I18nPlugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563'
    }
});
