import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'
import { fileURLToPath } from 'url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/js/app.js',
        'resources/js/tinymce.js'
      ],
      refresh: true,
    }),
  ],
  // PostCSS will be picked up automatically from postcss.config.cjs
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
    },
  },
  // Copy TinyMCE files to public during build
  build: {
    rollupOptions: {
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.includes('tinymce')) {
            return 'tinymce/[name][extname]'
          }
          return 'assets/[name]-[hash][extname]'
        }
      }
    }
  }
})


