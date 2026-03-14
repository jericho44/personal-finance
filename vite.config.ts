import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        vue(),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: 'auto',
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,woff,woff2}'],
                cleanupOutdatedCaches: true,
            },
            manifest: {
                name: 'Personal Finance Tracker',
                short_name: 'FinanceApp',
                description: 'Track your personal finance with AI insights',
                theme_color: '#ffffff',
                background_color: '#ffffff',
                display: 'standalone',
                icons: [
                    {
                        src: '/icons/pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/icons/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                    {
                        src: '/icons/maskable-icon.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable',
                    },
                ],
            },
        }),
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
