import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // Masque les avertissements de dépréciation émis par les dépendances (Bootstrap)
                // pour garder un build propre. Les avertissements de notre propre code restent visibles.
                quietDeps: true,
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
            },
        },
    },
    build: {
        rollupOptions: {
            checks: {
                pluginTimings: false,
            },
        },
    },
});
