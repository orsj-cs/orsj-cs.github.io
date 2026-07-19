/**
 * 使い捨て移行スクリプト (実行後は不要)。
 *
 * 旧 Jekyll サイトの静的コンテンツを Astro 構成へ移す:
 *   1. 4つのセクション一覧 (members/achievement/bukai/sokai の index.html) を
 *      src/pages/<section>/index.astro として生成 (BaseLayout + set:html)。
 *      ※ 旧HTMLは <li> 未閉じ等が多く JSX 解釈に載らないため set:html で描画。
 *   2. それ以外の凍結HTML (membersYYYY / achieveYYYY / bukai旧トピック /
 *      sokai各年サブページ / event個別) を、共通ヘッダ/フッタ+新CSSで包んだ
 *      自己完結HTMLとして public/ の同一パスへ出力 (URL維持)。
 *   3. PDF・画像等のバイナリを public/ へ無加工コピー (URL維持)。
 *   4. ssor2017/ は静的資産 (index.html / fujiwara.pdf / logo.gif / css/style.css)
 *      のみ public/ssor2017/ へ。PHP・lib・admin・csv・tmp は除外。
 *
 * 実行: node scripts/migrate-legacy.mjs   (リポジトリルートから)
 */
import { promises as fs } from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");
const PUBLIC = path.join(ROOT, "public");
const PAGES = path.join(ROOT, "src", "pages");

/** フロントマターを剥がして本文を返す */
function stripFrontMatter(src) {
  const m = src.match(/^---\r?\n[\s\S]*?\r?\n---\r?\n?/);
  return m ? src.slice(m[0].length) : src;
}
/** フロントマターの title を取得 */
function frontMatterTitle(src) {
  const m = src.match(/^---\r?\n([\s\S]*?)\r?\n---/);
  if (!m) return null;
  const t = m[1].match(/^\s*title:\s*(.+?)\s*$/m);
  return t ? t[1].trim() : null;
}
function hasFrontMatter(src) {
  return /^---\r?\n/.test(src);
}

// ---- 凍結HTML用 自己完結テンプレート -------------------------------------
function standaloneHtml(title, bodyHtml) {
  return `<!doctype html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>${title}</title>
<link rel="stylesheet" href="/assets/archive.css">
</head>
<body>
<header class="site-header"><div class="site-header__inner">
<a class="site-header__logo" href="/"><img src="/images/logo.gif" alt="日本OR学会 中国・四国支部"></a>
<p class="site-header__title"><a href="/">日本オペレーションズ・リサーチ学会 中国・四国支部</a></p>
</div></header>
<nav class="site-nav"><div class="site-nav__inner">
<a href="/">ホーム</a><a href="/members/">支部役員</a><a href="/achievement/">活動報告</a><a href="/bukai/">研究部会</a><a href="/sokai/">総会資料</a><a href="/history">過去のイベント</a><a href="/office">事務局</a><a href="/link">リンク集</a>
</div></nav>
<main>
<h1 class="page-title">${title}</h1>
${bodyHtml}
</main>
<footer class="site-footer"><div class="site-footer__inner">
<address>日本オペレーションズ・リサーチ学会 中国・四国支部事務局<br>(o&#114;&#115;&#106;&#46;c&#104;u&#115;h&#105;&#107;&#111;ku&#64;&#103;mail.&#99;om)</address>
</div></footer>
</body>
</html>
`;
}

// ---- セクション一覧の Astro ページ生成 ------------------------------------
function sectionAstro(title, bodyHtml) {
  // set:html に渡すためバッククォートと ${ をエスケープ
  const escaped = bodyHtml.replace(/\\/g, "\\\\").replace(/`/g, "\\`").replace(/\$\{/g, "\\${");
  return `---
import BaseLayout from "../../layouts/BaseLayout.astro";
const body = \`${escaped}\`;
---

<BaseLayout title=${JSON.stringify(title)}>
  <Fragment set:html={body} />
</BaseLayout>
`;
}

const SECTION_TITLES = {
  members: "支部役員｜中国・四国支部",
  achievement: "活動報告｜中国・四国支部",
  bukai: "研究部会｜中国・四国支部",
  sokai: "総会資料｜中国・四国支部",
};

// バイナリ拡張子
const BINARY = new Set([".pdf", ".jpg", ".jpeg", ".png", ".gif", ".ico", ".svg", ".cur", ".swf"]);
// public へ運ばない (サイトソース/旧Jekyll/ゴミ)
const SKIP_TOP = new Set([
  ".git", "node_modules", "dist", ".astro", "src", "public", "scripts",
  ".github", "_layouts", "_includes", "ssor2017", ".vscode",
]);
const SKIP_FILE = new Set([".DS_Store"]);

async function walk(dir, cb) {
  for (const ent of await fs.readdir(dir, { withFileTypes: true })) {
    if (SKIP_FILE.has(ent.name)) continue;
    const abs = path.join(dir, ent.name);
    const rel = path.relative(ROOT, abs);
    if (ent.isDirectory()) {
      if (dir === ROOT && SKIP_TOP.has(ent.name)) continue;
      await walk(abs, cb);
    } else {
      await cb(abs, rel);
    }
  }
}

async function ensureDir(p) {
  await fs.mkdir(path.dirname(p), { recursive: true });
}

let counts = { sectionIndex: 0, frozenHtml: 0, binary: 0, ssor: 0, skipped: 0 };

async function run() {
  await walk(ROOT, async (abs, rel) => {
    const ext = path.extname(rel).toLowerCase();
    const base = path.basename(rel);
    const topDir = rel.split(path.sep)[0];

    // 旧トップページ(index/history/link/office)は Astro 化済み → 無視
    if (
      ["index.html", "history.html", "link.html", "office_info.html"].includes(rel)
    ) {
      counts.skipped++;
      return;
    }
    // 旧 CSS/JS はアーカイブ互換用に別途コピー(後述) → ここでは扱う
    if (topDir === "css" && ext === ".css") {
      const dest = path.join(PUBLIC, rel);
      await ensureDir(dest);
      await fs.copyFile(abs, dest);
      counts.binary++;
      return;
    }
    if (topDir === "js") {
      counts.skipped++; // update_date.js は破棄
      return;
    }

    if (ext === ".html") {
      const src = await fs.readFile(abs, "utf8");
      // セクション一覧 (<section>/index.html のみ。年度サブフォルダの index.html は凍結扱い)
      if (rel === path.join(topDir, "index.html") && SECTION_TITLES[topDir]) {
        const body = stripFrontMatter(src).trim();
        const out = path.join(PAGES, topDir, "index.astro");
        await ensureDir(out);
        await fs.writeFile(out, sectionAstro(SECTION_TITLES[topDir], body));
        counts.sectionIndex++;
        return;
      }
      // 凍結HTML
      const title = frontMatterTitle(src) || base;
      const body = stripFrontMatter(src).trim();
      const dest = path.join(PUBLIC, rel);
      await ensureDir(dest);
      await fs.writeFile(dest, standaloneHtml(title, body));
      counts.frozenHtml++;
      return;
    }

    if (BINARY.has(ext)) {
      const dest = path.join(PUBLIC, rel);
      await ensureDir(dest);
      await fs.copyFile(abs, dest);
      counts.binary++;
      return;
    }
    counts.skipped++;
  });

  // ssor2017 の静的資産のみ
  const ssorFiles = [
    "index.html",
    "fujiwara.pdf",
    "logo.gif",
    "css/style.css",
  ];
  for (const f of ssorFiles) {
    const abs = path.join(ROOT, "ssor2017", f);
    try {
      const dest = path.join(PUBLIC, "ssor2017", f);
      await ensureDir(dest);
      await fs.copyFile(abs, dest);
      counts.ssor++;
    } catch (e) {
      console.warn("ssor2017 skip:", f, e.message);
    }
  }

  console.log("migration done:", counts);
}

run().catch((e) => {
  console.error(e);
  process.exit(1);
});
