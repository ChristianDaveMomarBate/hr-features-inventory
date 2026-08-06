import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/bootstrap-app.css',
                'resources/css/index.style.css',
                'resources/css/kiosk.css',
                'resources/css/request.css',
                'resources/css/login.css',
                'resources/js/inventory/script.js',
                'resources/js/inventory/kiosk.js',
                'resources/js/inventory/login.js',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
