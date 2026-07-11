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
            return I18n.trans(I18n.merge(this.$page?.props?.translations, this.$page?.props?.pageTranslations), key, replace);
        };
        app.config.globalProperties.$trans_choice = function (key, count = 1, replace = {}) {
            return I18n.trans_choice(I18n.merge(this.$page?.props?.translations, this.$page?.props?.pageTranslations), key, count, replace);
        };
    }
}

createInertiaApp({
    resolve: async (name) => {
        const module = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));

        // Default persistent layout only for pages that don't declare one at all. Use
        // `=== undefined` (not ??=) so a page that opts out with an explicit `layout: null`
        // — e.g. mails, which render their own MultiColumnLayout — isn't double-wrapped
        // with SingleColumnLayout (which stacks a second sidebar).
        if (module.default.layout === undefined) {
            module.default.layout = SingleColumnLayout;
        }

        return module;
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
