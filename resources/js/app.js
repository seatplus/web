import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import SingleColumnLayout from "@/Shared/SidebarLayout/SingleColumnLayout.vue";

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
            .mount(el);
    },
    progress: {
        color: '#4B5563'
    }
});
