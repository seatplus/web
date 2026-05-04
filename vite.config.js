import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import run from 'vite-plugin-run';
import { existsSync } from 'fs';

// In local dev (monorepo), packages/ exists. In end-user installs, it does not.
// End users only run `npm run build` once — no dev server / file watchers needed.
const isMonorepo = existsSync('./packages/web');

const plugins = [
    laravel({
        input: 'resources/js/app.js',
        refresh: ['resources/js/**'],
    }),
    vue({
        template: {
            transformAssetUrls: {
                base: null,
                includeAbsolute: false,
            },
        },
    }),
];

if (isMonorepo) {
    plugins.push(run([
        {
            startup: false,
            name: 'copy vendor',
            run: ['php', 'artisan', 'vendor:publish', '--tag=web', '--force'],
            pattern: ['packages/**/resources/js/**'],
        },
        {
            startup: true,
            name: 'wayfinder',
            run: ['php', 'artisan', 'wayfinder:generate'],
            pattern: ['packages/**/src/Http/**/*.php', 'packages/**/routes/**/*.php'],
        },
    ]));
}

export default defineConfig(({mode}) => {
    return {
        server: {
            cors: mode === 'development',
            watch: {
                ignored: [
                    '**/node_modules/**',
                    '**/vendor/**',
                    '**/public/**',
                    '!**/vendor/seatplus/**',
                ],
            },
        },
        plugins,
        resolve: {
            alias: {
                '@': '/resources/js',
            },
        },
    };
});
