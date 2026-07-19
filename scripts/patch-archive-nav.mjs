/**
 * 凍結アーカイブ (public 配下の .html) のグローバルナビを最新版へ一括置換する。
 *
 * 移行時テンプレート (migrate-legacy.mjs) は移行元削除により再実行不可のため、
 * ナビ項目を増やした際はこのスクリプトで public 側の <nav class="site-nav"> を
 * 差し替えて整合を取る。SiteNav.astro の links と同じ内容を維持すること。
 *
 * 実行: node scripts/patch-archive-nav.mjs
 */
import { promises as fs } from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");
const PUBLIC = path.join(ROOT, "public");

// SiteNav.astro と同一の項目
const NAV_LINKS = [
  ["/", "ホーム"],
  ["/about", "支部について"],
  ["/greeting", "挨拶"],
  ["/news/", "お知らせ"],
  ["/members/", "支部役員"],
  ["/achievement/", "活動報告"],
  ["/bukai/", "研究部会"],
  ["/sokai/", "総会資料"],
  ["/history", "過去のイベント"],
  ["/office", "事務局"],
  ["/link", "リンク集"],
];

const NAV_HTML =
  `<nav class="site-nav"><div class="site-nav__inner">` +
  NAV_LINKS.map(([h, l]) => `<a href="${h}">${l}</a>`).join("") +
  `</div></nav>`;

const NAV_RE = /<nav class="site-nav">[\s\S]*?<\/nav>/;

async function walk(dir, out = []) {
  for (const ent of await fs.readdir(dir, { withFileTypes: true })) {
    const abs = path.join(dir, ent.name);
    if (ent.isDirectory()) await walk(abs, out);
    else if (abs.endsWith(".html")) out.push(abs);
  }
  return out;
}

let patched = 0,
  skipped = 0;
for (const file of await walk(PUBLIC)) {
  const html = await fs.readFile(file, "utf8");
  if (!NAV_RE.test(html)) {
    skipped++;
    continue;
  }
  await fs.writeFile(file, html.replace(NAV_RE, NAV_HTML));
  patched++;
}
console.log(`patch-archive-nav: patched=${patched}, skipped(no nav)=${skipped}`);
