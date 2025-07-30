import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // server: {
    //     host: '0.0.0.0',
    //     port: 5173,
    //     strictPort: true,
    //     hmr: {
    //         host: '192.168.137.79',
    //         port: 5173,
    //     }
    // },

    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/custom-filament-scripts.js',
                'resources/css/filament/ticketing/theme.css'],
            refresh: true,
        }),
    ],

});
