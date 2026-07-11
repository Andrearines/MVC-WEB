import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    outDir: 'public/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/main.js')
      }
    }
  },
  server: {
    origin: 'http://localhost:5173',
    cors: true,
    port: 5173
  }
});
