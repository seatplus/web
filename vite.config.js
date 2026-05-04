import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import run from 'vite-plugin-run';
import { existsSync } from 'fs';

// Detect monorepo (local dev) vs end-user install
const isMonorepo = existsSync('./packages/web');
const base = isMonorepo ? 'packages' : 'vendor/seatplus';

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
                    `${base}/**/resources/js/**`,
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
                    pattern: [`${base}/**/resources/js/**`],
                },
                {
                    startup: true,
                    name: 'wayfinder',
                    run: ['php', 'artisan', 'wayfinder:generate'],
                    pattern: [`${base}/**/src/Http/**/*.php`, `${base}/**/routes/**/*.php`],
                },
            ]),
        ],
        resolve: {
            alias: {
                '@': '/resources/js',
            },
        },
    }
});
