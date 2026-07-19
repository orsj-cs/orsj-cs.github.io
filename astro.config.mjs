// @ts-check
import { defineConfig } from 'astro/config';

// 組織ルートの GitHub Pages リポジトリ (orsj-cs.github.io) のため base は '/'。
export default defineConfig({
  site: 'https://orsj-cs.github.io',
  base: '/',
});
