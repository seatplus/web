import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/index';
import SingleColumnLayout from "@/Shared/SidebarLayout/SingleColumnLayout.vue";
import I18n from '@/i18n/I18n';

const I18nPlugin = {
    install(app) {
        // Options-API global: translate against the current page's reactive `translations`
        // prop. (`<script setup>` pages use the useTranslations() composable instead.)
        app.config.globalProperties.$trans = function (key, replace = {}) {
            return I18n.trans(this.$page?.props?.translations || {}, key, replace);
        };
        app.config.globalProperties.$trans_choice = function (key, count = 1, replace = {}) {
            return I18n.trans_choice(this.$page?.props?.translations || {}, key, count, replace);
        };
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
