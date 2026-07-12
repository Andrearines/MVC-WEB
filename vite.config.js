import { defineConfig }  from 'vite';
import { resolve, relative, dirname, extname, basename } from 'path';
import { fileURLToPath } from 'url';
import fs   from 'fs';
import path from 'path';
import * as sass       from 'sass';
import postcss         from 'postcss';
import autoprefixer    from 'autoprefixer';
import cssnano         from 'cssnano';

const __filename = fileURLToPath(import.meta.url);
const __dirname  = dirname(__filename);
const SRC_DIR    = resolve(__dirname, 'src');

// ─────────────────────────────────────────────────────────────────────────────
// Util: recorre un directorio recursivamente y retorna archivos que coincidan
// ─────────────────────────────────────────────────────────────────────────────
function collectFiles(dir, regex) {
  const results = [];
  if (!fs.existsSync(dir)) return results;
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const fp = path.join(dir, entry.name);
    if (entry.isDirectory())         results.push(...collectFiles(fp, regex));
    else if (regex.test(entry.name)) results.push(fp);
  }
  return results;
}

// ─────────────────────────────────────────────────────────────────────────────
// Plugin SCSS  →  public/build/css/**  (misma estructura que src/)
// Equivalente a la tarea css() de Gulp:
//   src('src/**/**/*.scss') → dest('./public/build/css')
// ─────────────────────────────────────────────────────────────────────────────
function scssStructurePlugin() {
  const cssOutDir = resolve(__dirname, 'public/build/css');

  async function compileFile(file) {
    try {
      const result = sass.compile(file, {
        style:     'compressed',
        loadPaths: [SRC_DIR],
        quietDeps: true,
      });

      const processed = await postcss([autoprefixer(), cssnano()])
        .process(result.css, { from: undefined });

      const rel = relative(SRC_DIR, file).replace(/\.scss$/, '.css');
      const out = resolve(cssOutDir, rel);
      fs.mkdirSync(dirname(out), { recursive: true });
      fs.writeFileSync(out, processed.css);
      console.log(`  [scss] ✓  css/${rel}`);
    } catch (e) {
      console.error(`  [scss] ✗  ${relative(__dirname, file)}\n          ${e.message}`);
    }
  }

  async function compileAll() {
    // Solo archivos que NO son partiales (no empiezan con _)
    const files = collectFiles(SRC_DIR, /^[^_].*\.scss$/);
    console.log(`\n[scss] Compilando ${files.length} archivos SCSS...`);
    await Promise.all(files.map(compileFile));
  }

  return {
    name: 'vite-plugin-scss-structure',

    // closeBundle se ejecuta DESPUÉS de que emptyOutDir limpió el directorio
    // y Rollup terminó de escribir sus archivos → aquí es seguro escribir CSS
    async closeBundle() {
      await compileAll();
    },

    // Modo dev: watch + compilación inmediata al levantar el servidor
    configureServer(server) {
      server.watcher.add(resolve(SRC_DIR, '**', '*.scss'));

      server.watcher.on('change', async (filePath) => {
        if (!filePath.endsWith('.scss')) return;
        // Un partial puede afectar cualquier entrada → recompilar todo
        await compileAll();
        server.ws.send({ type: 'full-reload' });
      });

      // Compilación inicial al arrancar
      compileAll();
    },
  };
}

// ─────────────────────────────────────────────────────────────────────────────
// Plugin Imágenes  →  optimizadas en imgs/  +  versiones WebP en img/webp/
// Equivalente a imagenes() + versionWebp() de Gulp
// ─────────────────────────────────────────────────────────────────────────────
function imagesPlugin() {
  const imgsOutDir = resolve(__dirname, 'public/build/assets/imgs');
  const webpOutDir = resolve(__dirname, 'public/build/assets/img/webp');
  const imgRegex   = /\.(jpe?g|png|gif|svg)$/i;

  async function processImages() {
    let sharp;
    try {
      sharp = (await import('sharp')).default;
    } catch {
      console.warn(
        '\n[images] ⚠  sharp no instalado → imágenes no procesadas.\n' +
        '          Instala con: npm i -D sharp\n'
      );
      return;
    }

    const files = collectFiles(SRC_DIR, imgRegex);
    if (files.length === 0) {
      console.log('[images] No se encontraron imágenes en src/');
      return;
    }

    fs.mkdirSync(imgsOutDir, { recursive: true });
    fs.mkdirSync(webpOutDir, { recursive: true });
    console.log(`\n[images] Procesando ${files.length} imágenes...`);

    for (const file of files) {
      const name = basename(file);
      const ext  = extname(name).toLowerCase();
      try {
        if (ext === '.svg') {
          // SVG: copiar directamente (no se puede pasar por sharp)
          fs.copyFileSync(file, resolve(imgsOutDir, name));
        } else {
          // PNG / JPG / GIF → optimizar
          await sharp(file).toFile(resolve(imgsOutDir, name));
          // → también generar versión WebP
          await sharp(file)
            .webp({ quality: 85 })
            .toFile(resolve(webpOutDir, basename(name, ext) + '.webp'));
        }
        console.log(`  [images] ✓  ${name}`);
      } catch (e) {
        console.error(`  [images] ✗  ${name}: ${e.message}`);
      }
    }
  }

  return {
    name: 'vite-plugin-images',
    async closeBundle() { await processImages(); },
    configureServer()   { processImages(); },
  };
}

// ─────────────────────────────────────────────────────────────────────────────
// Entradas JS: cada archivo .js en src/ → bundle individual preservando ruta
// Equivalente a javascript() de Gulp:
//   src('src/**/**/*.js') → dest('./public/build/js')
// ─────────────────────────────────────────────────────────────────────────────
function getJsEntries() {
  return collectFiles(SRC_DIR, /\.js$/).reduce((acc, file) => {
    // Clave = ruta relativa a src/ sin extensión (ej: "Ui/Auth/pages/page/js/page")
    const rel = relative(SRC_DIR, file)
      .replace(/\.js$/, '')
      .replace(/\\/g, '/'); // compatibilidad Windows
    acc[rel] = file;
    return acc;
  }, {});
}

// ─────────────────────────────────────────────────────────────────────────────
// Configuración Vite
// ─────────────────────────────────────────────────────────────────────────────
export default defineConfig({
  // PHP es el servidor público; Vite no debe servir la carpeta public/
  publicDir: false,

  build: {
    outDir:      'public/build',
    emptyOutDir: true,
    manifest:    true,

    rollupOptions: {
      // ── Entradas: todos los .js de src/ ───────────────────────────────────
      input: getJsEntries(),

      output: {
        // JS: preserva estructura → public/build/js/Ui/Auth/pages/.../page.js
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/chunks/[name]-[hash].js',

        // CSS importada en JS (ej: import './app.scss' en main.js)
        // va a public/build/css/ sin hash → ruta predecible
        assetFileNames: (assetInfo) => {
          const name = assetInfo.name || '';
          if (/\.css$/.test(name)) return 'css/[name][extname]';
          return 'assets/[name][extname]';
        },
      },
    },
  },

  plugins: [
    scssStructurePlugin(),  // SCSS con estructura
    imagesPlugin(),         // PNG optimizado + WebP
  ],

  css: {
    preprocessorOptions: {
      scss: { quietDeps: true },
    },
  },

  server: {
    origin: 'http://localhost:5173',
    cors:   true,
    port:   5173,
  },
});
