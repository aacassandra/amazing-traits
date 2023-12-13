import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import Vue from '@vitejs/plugin-vue'
// new stuff
import { resolve, dirname } from 'node:path'
import vueI18n from '@intlify/vite-plugin-vue-i18n'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/vue/assets/css/app.css', 'resources/vue/app.ts'],
      refresh: true,
    }),
    Vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    vueI18n({
      // you need to set i18n resource including paths
      runtimeOnly: false,
      include: resolve(
        dirname(fileURLToPath(import.meta.url)),
        './resources/vue/locales/**',
      ),
    }),
  ],
  resolve: {
    alias: {
      '~': fileURLToPath(new URL('resources/vue', import.meta.url)),
    },
  },
})
