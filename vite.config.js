import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import run from 'vite-plugin-run';

export default defineConfig(({mode}) => {
    return {
        server: {
            cors: mode === "development",
            watch: {
                ignored: [
                    '**/node_modules/**',
                    '**/vendor/**',
                    '**/public/**',
                    '!**/vendor/seatplus/**',
                ],
            }
        },
        plugins: [
            laravel({
                input: 'resources/js/app.js',
                refresh: [
                    'resources/js/**',
                    'vendor/seatplus/**/resources/js/**',
                ],
            }),
            tailwindcss(),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            run([
                {
                    // Keep the Wayfinder output (@/actions, @/routes) generated and in sync
                    // while developing: runs on `vite`/dev startup and re-runs when backend
                    // routes/controllers change. Avoids the drift where @/actions goes missing
                    // while components still import from it.
                    //
                    // `build: false` is load-bearing, not tidiness: production builds run in a
                    // node-only container (base-app's node service is node:current-alpine, no
                    // php binary), so `vite build` must never shell out to php. @/actions is
                    // produced in the php container instead, by core's post-update-cmd during
                    // `composer update` — see seatplus/web#1678.
                    startup: true,
                    build: false,
                    name: 'wayfinder generate',
                    run: ['php', 'artisan', 'wayfinder:generate'],
                    pattern: [
                        'routes/**',
                        'vendor/seatplus/**/routes/**',
                        'vendor/seatplus/**/src/Http/Controllers/**',
                    ],
                },
                {
                    // Dev-only as well: only fires on hot update, and never during a build.
                    startup: false,
                    build: false,
                    name: 'copy vendor',
                    run: ['php', 'artisan', 'vendor:publish', '--tag=web', '--force'],
                    pattern: ['vendor/seatplus/**/resources/js/**']
                }
            ])
        ],
        resolve: {
            alias: {
                '@': '/resources/js',
            },
        },
    }
});
