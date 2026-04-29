import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
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
            wayfinder(),
        ],
        resolve: {
            alias: {
                '@': '/resources/js',
            },
        },
    }
});
