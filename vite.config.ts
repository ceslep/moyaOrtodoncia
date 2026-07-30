import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import tailwindcss from '@tailwindcss/vite'
import path from 'path'

export default defineConfig({
  base:'/moyaOrtodoncia/',
  plugins: [svelte(), tailwindcss()],
  resolve: {
    alias: {
      '$lib': path.resolve('./src/lib'),
      '$lib/*': path.resolve('./src/lib/*'),
    },
  },
})
