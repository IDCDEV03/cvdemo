import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/js/app.js',
            ],
            refresh: true, // ตรวจสอบให้แน่ใจว่ามี 'refresh: true'
        }),
    ],
    server: {
        host: '127.0.0.1',        
        hmr: {
            host: 'localhost', 
            clientPort: 5173, 
        },
        watch: {
            usePolling: true 
        }
    },
});