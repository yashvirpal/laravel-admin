import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default ({ mode }) => {
    const env = loadEnv(mode, process.cwd());

    return defineConfig({
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                    'resources/css/admin.css',
                    'resources/js/admin.js',
                ],
                refresh: true,
            }),
        ],
        server: {
            host: '0.0.0.0',
            port: 5173,
            strictPort: true,
            hmr: {
                host: env.VITE_DEV_SERVER_URL
                    ? env.VITE_DEV_SERVER_URL.replace('http://', '').replace(':5173', '')
                    : 'localhost',
            },
        },
    });
};