import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'node_modules/rappasoft/laravel-livewire-tables/resources/js/core.min.js',
                'node_modules/rappasoft/laravel-livewire-tables/resources/js/thirdparty.min.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            $: 'jquery'
        }
    }
});

