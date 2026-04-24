import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import run from 'vite-plugin-run';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';

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
            wayfinder(),
            laravel({
                input: 'resources/js/app.js',
                refresh: [
                    'resources/js/**',
                    'vendor/seatplus/**/resources/js/**',
                ],
            }),
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
                    startup: false,
                    name: 'copy vendor',
                    run: ['php', 'artisan', 'vendor:publish', '--tag=web', '--force'],
                    pattern: ['vendor/seatplus/**/resources/js/**']
                }
            ])
        ],
        resolve: {
            alias: {
                '@': '/resources/js',
                "@actions/": "./resources/js/actions",
                "@routes/": "./resources/js/routes",
            },
        },
    }
});
