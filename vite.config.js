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
                    // Keep the Wayfinder output (@/actions, @/routes) generated and in sync.
                    // Runs on dev/build startup so a fresh checkout always has it, and re-runs
                    // when backend routes/controllers change. Avoids the drift where @/actions
                    // goes missing while components still import from it.
                    startup: true,
                    name: 'wayfinder generate',
                    run: ['php', 'artisan', 'wayfinder:generate'],
                    pattern: [
                        'routes/**',
                        'vendor/seatplus/**/routes/**',
                        'vendor/seatplus/**/src/Http/Controllers/**',
                    ],
                },
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
            },
        },
    }
});
