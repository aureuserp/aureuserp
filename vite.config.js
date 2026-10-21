import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'plugins/webkul/barcode/resources/dist/barcode.css',
                'plugins/webkul/barcode/resources/dist/barcode.js',
                'plugins/webkul/point-of-sale/resources/js/till/main.js',
            ],
            refresh: true,
        }),
        vue(),
    ],
});
