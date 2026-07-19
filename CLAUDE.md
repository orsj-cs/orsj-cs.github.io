# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Static website for the 日本OR学会 中国・四国支部 (Operations Research Society of Japan, Chugoku-Shikoku Branch), built with **Astro** and deployed to **GitHub Pages** via GitHub Actions. Content is Japanese. This is the org-root Pages repo (`orsj-cs/orsj-cs.github.io`), so the site is served at `https://orsj-cs.github.io/` and `base` is `/`.

## Commands

- `npm install` — install dependencies (Node 20+; local dev works on the repo's Node 26).
- `npm run dev` — local dev server at `http://localhost:4321/` (live preview, no GitHub Pages needed).
- `npm run build` — build static site into `dist/`.
- `npm run preview` — serve the built `dist/` locally.

Deployment is automatic: pushing to `master` triggers `.github/workflows/deploy.yml` (build → upload-pages-artifact → deploy-pages). Repo **Settings → Pages → Source must be "GitHub Actions"**.

**Dev workflow**: work happens on branches via PRs into `master`. `.github/workflows/ci.yml` runs `npm run build` on every PR (job name `build`, used as a required branch-protection check). **Production stays on GitHub Pages** (to keep the `orsj-cs.github.io` URL); **PR previews come from Cloudflare Pages** (connected to the repo, preview-only — subpath schemes like gh-pages `/pr-preview/` break because the site and its 61 frozen `public/` archive pages use absolute paths, whereas Cloudflare serves each preview at a domain root). Node is pinned via `.nvmrc` (20) across CI, deploy, and Cloudflare. See README "6.5 開発フロー" for the full setup.

## Architecture

Two content tiers, deliberately separated:

1. **Living pages** — hand-authored Astro under `src/pages/`, wrapped in `src/layouts/BaseLayout.astro` (header + `SiteNav` + `SiteFooter` + `src/styles/global.css`):
   - `index.astro` (home + イベント案内), `history.astro`, `link.astro`, `office.astro`.
   - Section index lists: `members/index.astro`, `achievement/index.astro`, `bukai/index.astro`, `sokai/index.astro`. These render the legacy link lists via `<Fragment set:html={body} />` because the original HTML uses loose/unclosed `<li>` that Astro's JSX parser rejects. Links inside are **relative** and resolve because each page keeps its original URL depth (e.g. `/sokai/` + `sokai2024/m1.pdf`).

2. **Frozen archive** — served verbatim from `public/` at unchanged URLs (this is why old links never break):
   - All PDFs, images, and old year folders (`public/sokai/sokaiYYYY/…`).
   - Old per-year HTML pages (`public/members/membersYYYY.html`, `public/achievement/achieveYYYY.html`, `public/bukai/*.html`, `public/event/YYYYMMDD.html`, sokai sub-year pages). These are self-contained HTML (own header/nav/footer) linking `/assets/archive.css` — a stable copy of the modern styles (Astro hashes `global.css`, so archive pages can't reference it).
   - `public/ssor2017/` — the 2017 seminar archive (`index.html` + `fujiwara.pdf` + assets). Its PHP registration form was **dropped** (can't run on a static host); the form is intentionally non-functional. It links `/css/base.css` + `/images/logo.gif`, both kept in `public/`.

`scripts/migrate-legacy.mjs` is the one-shot migration script that produced the above from the old Jekyll tree. It's retained for reference; not part of the build.

## Common tasks

- **Add a research meeting / event**: create `src/content/events/YYYY-MM-DD-slug.md` with frontmatter `title` / `date` / optional `venue` / `deadline` / `applyUrl`. The homepage (`src/pages/index.astro`) auto-splits events into 次回 (featured `EventCard`), 今後の予定, and 過去の研究会 by comparing `date` against build-time `new Date()`. No per-event page is generated (events are data only). Schema is the `events` collection in `src/content.config.ts`; the card is `src/components/EventCard.astro`.
- **Add an お知らせ (news post)**: create `src/content/news/YYYY-MM-DD-slug.md` with frontmatter `title` / `date` / optional `category`, then write Markdown body. It auto-appears on `/news/`, gets its own `/news/<slug>/` page, and the newest 3 show on the homepage. Schema is in `src/content.config.ts` (Astro content collection, `glob` loader).
- **Edit About / Greeting**: `src/pages/about.astro`, `src/pages/greeting.astro` (current text is a draft — look for `TODO` comments).
- **Add a new officer/report/meeting year**: drop the new PDF into the right `public/<section>/…` folder, then add one `<li>` link at the top of the matching `src/pages/<section>/index.astro` `body` string.
- **Add/update an event**: edit `src/pages/index.astro` (イベント案内) and `src/pages/history.astro`. Archived event pages live in `public/event/`.
- **Nav changes**: edit the `links` array in `src/components/SiteNav.astro`, then run `node scripts/patch-archive-nav.mjs` to sync the same nav into the frozen `public/**/*.html` archive pages (keep `NAV_LINKS` there identical to `SiteNav.astro`). `migrate-legacy.mjs` can no longer regenerate the archive (its source tree was deleted), so `patch-archive-nav.mjs` is the maintenance path.
- **Global styling**: `src/styles/global.css`. If you change it and want frozen archive pages to match, also update `public/assets/archive.css`.

## Gotchas

- Archive pages under `public/` are **not** processed by Astro — they must be complete standalone HTML. Don't add Astro/Liquid syntax there.
- `global.css` is bundled with a hashed filename; never link to it directly from `public/` HTML — use `/assets/archive.css`.
- Verify internal links after big changes: build, then a quick file-existence sweep over `dist/` catches 404s (obfuscated `mailto:` entities like `m&#97;i…` show up as false positives — ignore them).
