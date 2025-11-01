import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.js'],
      refresh: true,
    }),
  ],
  // PostCSS will be picked up automatically from postcss.config.cjs
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
    },
  },
})


