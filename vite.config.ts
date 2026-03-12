import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        vue()
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '@src': '/resources/js',
            '@components': '/resources/js/components',
            '@plugins': '/resources/js/plugins',
            '@services': '/resources/js/services',
            '@stores': '/resources/js/stores',
            '@routes': '/resources/js/routes',
            '@views': '/resources/js/views',            
            '@types': '/resources/js/types',            
        },
    },
    server: {
        host: true,
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
});
