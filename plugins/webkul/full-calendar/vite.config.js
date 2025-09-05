import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            publicDirectory: "publishable",
            buildDirectory: "build",
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: '',
        emptyOutDir: true,
    },
});
