import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import run from 'vite-plugin-run';
import { existsSync } from 'fs';

// Three execution contexts for this config (it ships via vendor:publish):
// 1. Package dev  — running from packages/web/ (testbench available)
// 2. Monorepo     — running from seatplus/core root (packages/web/ directory exists)
// 3. End-user     — running from a plain Laravel app root (default)
const isPackageDev = existsSync('./vendor/bin/testbench');
const isMonorepo   = !isPackageDev && existsSync('./packages/web');

const packageDevConfig = {
    refresh: ['resources/js/**'],
    serverWatch: undefined,
    runTasks: [
        {
            startup: true,
            name: 'wayfinder',
            run: ['php', 'vendor/bin/testbench', 'wayfinder:generate', '--path=resources/js'],
            pattern: ['routes/**/*.php', 'src/Http/Controllers/**/*.php'],
        },
    ],
};

const monorepoConfig = {
    refresh: ['resources/js/**', 'packages/web/resources/js/**'],
    serverWatch: { ignored: ['**/node_modules/**', '**/vendor/**', '**/public/**'] },
    runTasks: [
        {
            startup: false,
            name: 'copy vendor',
            run: ['php', 'artisan', 'vendor:publish', '--tag=web', '--force'],
            pattern: ['packages/web/resources/js/**'],
        },
        {
            startup: true,
            name: 'wayfinder',
            run: ['php', 'artisan', 'wayfinder:generate'],
            pattern: ['packages/web/routes/**/*.php', 'packages/web/src/Http/Controllers/**/*.php'],
        },
    ],
};

const endUserConfig = {
    refresh: ['resources/js/**'],
    serverWatch: undefined,
    runTasks: [
        {
            startup: true,
            name: 'wayfinder',
            run: ['php', 'artisan', 'wayfinder:generate'],
            pattern: [],
        },
    ],
};

const contextConfig = (
    isPackageDev ? packageDevConfig :
        isMonorepo   ? monorepoConfig   :
            endUserConfig
);

export default defineConfig(({mode}) => {
    return {
        server: {
            cors: mode === "development",
            ...(contextConfig.serverWatch ? { watch: contextConfig.serverWatch } : {}),
        },
        plugins: [
            laravel({
                input: 'resources/js/app.js',
                refresh: contextConfig.refresh,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            run(contextConfig.runTasks),
        ],
        resolve: {
            alias: {
                '@': '/resources/js',
            },
        },
    }
});
