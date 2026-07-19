# 日本OR学会 中国・四国支部 ウェブサイト

日本オペレーションズ・リサーチ学会（ORSJ）中国・四国支部の公式サイトのソースです。
[Astro](https://astro.build/) で構築した静的サイトで、`master` ブランチへの push で
GitHub Actions が自動ビルド・公開します。

- 公開URL: <https://orsj-cs.github.io/>
- 連絡先: orsj.chushikoku@gmail.com

このREADMEは**サイト管理者向け**の手引きです。ページ構成・更新方法・公開手順をまとめています。

---

## 1. ローカルで確認する

編集内容はローカルのブラウザで確認できます（`http://localhost:4321/`、停止は `Ctrl-C`）。

### Docker を使う場合（Node のインストール不要・おすすめ）

```bash
docker compose up dev        # 開発サーバ（編集が即反映）
# または本番と同じビルドを確認したいとき
docker compose up preview
```

### Node（npm）を直接使う場合（Node 20 以上）

```bash
npm install                  # 最初の一度だけ
npm run dev                  # 開発サーバ
npm run build                # dist/ に本番ビルド
npm run preview              # ビルド結果を確認
```

どちらか迷ったら次のスクリプトが自動判別します（Dockerがあれば Docker、なければ npm）。

```bash
./scripts/serve-local.sh          # 開発サーバ
./scripts/serve-local.sh preview  # ビルド結果を確認
```

---

## 2. ディレクトリ構成

```
src/                         ← 編集する“生きている”ページはここ
  pages/                     ← 各ページ（.astro）。ファイル＝URL
    index.astro              …… トップページ
    about.astro              …… 支部について
    greeting.astro           …… 支部長挨拶
    news/                    …… お知らせ（一覧・個別）
    members/index.astro      …… 支部役員 一覧
    achievement/index.astro  …… 活動報告 一覧
    bukai/index.astro        …… 研究部会 一覧
    sokai/index.astro        …… 総会資料 一覧
    history.astro            …… 過去のイベント
    office.astro             …… 事務局
    link.astro               …… リンク集
  content/news/*.md          ← お知らせの記事（Markdown）※投稿はここ
  layouts/BaseLayout.astro   ← 全ページ共通の枠（ヘッダ・フッタ）
  components/SiteNav.astro    ← グローバルナビ
  styles/global.css          ← サイト全体の見た目（色など）

public/                      ← そのまま配信される固定ファイル（原則さわらない）
  members/ achievement/ bukai/ sokai/ event/   …… 過去年度のPDF・旧HTML（凍結）
  images/logo.gif            …… ロゴ
  assets/archive.css         …… 凍結ページ用のCSS（global.css のコピー）
  ssor2017/                  …… 2017年SSORの記録（当時のまま保存）

scripts/                     ← 管理用スクリプト
  serve-local.sh             …… ローカル起動
  patch-archive-nav.mjs      …… 凍結ページのナビ一括更新
  migrate-legacy.mjs         …… 旧サイト移行（実行済み・通常不要）
```

ポイント：**日常の更新は `src/` だけ**を編集します。`public/` の中身は過去年度の
資料など「凍結された」ファイルで、原則そのままにします。

---

## 3. お知らせを投稿する（いちばんよく使う操作）

`src/content/news/` に Markdown ファイルを **1つ追加するだけ** です。追加すると
お知らせ一覧・各記事ページ・トップページの「最新お知らせ」に自動で反映されます。

1. `src/content/news/` に新しいファイルを作成します。
   ファイル名は `年-月-日-任意の名前.md` を推奨（例 `2026-04-01-symposium.md`）。
   このファイル名がそのまま記事のURL（`/news/2026-04-01-symposium/`）になります。

2. ファイルの中身は、先頭の「メタ情報」＋本文（Markdown）です。

```markdown
---
title: 令和8年度 支部講演会のご案内
date: 2026-04-01
category: イベント
---

本文をここに書きます。段落は空行で区切ります。

- 箇条書きも使えます
- リンクは [表示文字](https://example.com/) の形式

支部内のページへは [総会資料](/sokai/) のように `/` から書きます。
```

メタ情報の項目：

| 項目 | 必須 | 説明 |
|------|:---:|------|
| `title` | ○ | 記事タイトル |
| `date` | ○ | 日付。`YYYY-MM-DD` 形式。一覧はこの日付の新しい順に並びます |
| `category` | 任意 | 「イベント」「総会」「お知らせ」など。バッジとして表示 |

3. ローカルで表示を確認したら、変更を `master` に push すれば公開されます
   （後述「6. 公開する」）。

記事を**削除**したいときは、その `.md` ファイルを消すだけです。

---

## 3.5 研究会・講演会（イベント）を登録する

トップページの「次回の研究会・講演会」「今後の予定」「過去の研究会」は、
`src/content/events/` に置いた Markdown から**自動生成**されます。開催日を過ぎると
自動的に「過去の研究会」へ移ります（次回の選択も自動）。

1. `src/content/events/` に `年-月-日-任意の名前.md` を作成。
2. 先頭のメタ情報に開催情報を書きます。

```markdown
---
title: 令和8年度 第1回支部講演会
date: 2026-11-28          # 開催日（必須）
venue: 広島大学 東広島キャンパス   # 会場（任意）
deadline: 2026-11-13      # 申込締切（任意）
applyUrl: https://forms.gle/xxxxx   # 申込リンク（任意）
---

講演会の詳細をここに書きます（任意）。
```

| 項目 | 必須 | 説明 |
|------|:---:|------|
| `title` | ○ | 行事名 |
| `date` | ○ | 開催日 `YYYY-MM-DD`。これを境に「次回/過去」が自動で切り替わります |
| `venue` | 任意 | 会場 |
| `deadline` | 任意 | 申込締切（表示されます） |
| `applyUrl` | 任意 | 「参加申込・詳細」ボタンのリンク先 |

> **注意**：ページは `master` への push でビルドされます。開催日を過ぎても、次に
> サイトを更新（push）するまでは「次回」に表示が残ることがあります。

## 4. 年度の資料を追加する（役員・活動報告・研究部会・総会）

過去年度と同じく、新年度の **PDF を `public/` に置き、一覧ページにリンクを1行足す**
運用です。

例）新年度の支部役員PDFを追加する場合

1. PDFを `public/members/members2027.pdf` として置く。
2. `src/pages/members/index.astro` を開き、一覧の**先頭**にリンクを1行追加する。
   （このページ内の `body` という文字列の中に、既存の行と同じ書式で追記します）

```html
<li><a href="members2027.pdf">令和９年度支部役員</a></li>
```

活動報告（`achievement`）・研究部会（`bukai`）・総会資料（`sokai`）も同様に、
対応する `src/pages/<セクション>/index.astro` を編集します。総会PDFは
`public/sokai/sokaiYYYY/` に置く慣例です。

> リンクは `members2027.pdf` のような**相対パス**で書きます（ページと同じ階層に
> 置かれるため、そのまま繋がります）。

---

## 5. その他のページを編集する

| 変更したいもの | 編集するファイル |
|----------------|------------------|
| トップの導入文・写真枠・全体構成 | `src/pages/index.astro` |
| トップに載る研究会・講演会 | `src/content/events/*.md`（上記 3.5） |
| トップのヒーロー写真 | `public/images/` に画像を置き `src/pages/index.astro` の写真枠を `<img>` に差替 |
| 支部について | `src/pages/about.astro` |
| 支部長挨拶 | `src/pages/greeting.astro` |
| 過去のイベント一覧 | `src/pages/history.astro` |
| 事務局（支部長・事務局員） | `src/pages/office.astro` |
| リンク集 | `src/pages/link.astro` |
| サイトの色（テーマ） | `src/styles/global.css` の先頭 `:root` 内 |

### ナビ（上部メニュー）を変えるとき

1. `src/components/SiteNav.astro` の `links` 配列を編集。
2. 過去年度の凍結ページにも同じメニューを反映するため、次を実行：

```bash
node scripts/patch-archive-nav.mjs
```

（`scripts/patch-archive-nav.mjs` 内の `NAV_LINKS` も `SiteNav.astro` と同じ内容に
そろえてください。）

### 色（テーマ）を変えるとき

`src/styles/global.css` の `:root` にある `--brand` などの色を変更します。凍結ページ
にも反映するには、変更後に次でコピーします：

```bash
cp src/styles/global.css public/assets/archive.css
```

---

## 6. 公開する（デプロイ）

`master` ブランチに push すると、GitHub Actions が自動でビルドして公開します。
特別な操作は不要です。

```bash
git add -A
git commit -m "お知らせ追加：〇〇"
git push origin master
```

- 進捗・成否は GitHub の **Actions** タブで確認できます。
- 反映まで通常1〜2分程度です。
- 初回のみ、リポジトリの **Settings → Pages → Source** を「GitHub Actions」に
  設定しておく必要があります。

---

## 6.5 開発フロー（PR運用・プレビュー）

複数人で運用するため、**ブランチを切って Pull Request（PR）でレビュー**してから
`master` にマージする流れを推奨します。

```
1. 作業用ブランチを作成      git switch -c 2026-news
2. 編集してコミット・push     git push -u origin 2026-news
3. GitHub で Pull Request を作成
4. 自動チェックとプレビューを確認
     - CI（build）… ビルドが通るか自動確認
     - Cloudflare Pages … PRごとに“プレビューURL”が自動生成され、bot が PR に
       コメントします。そのURLで実際の見た目を確認できます（本番とは別）。
5. レビューOKなら master へマージ → 本番 https://orsj-cs.github.io/ に反映
```

- 本番（GitHub Pages）は `master` の内容だけを配信します。PRの中身は本番URLでは
  見られないため、上記の**Cloudflareプレビューbot**のURLで確認します。
- 手元で確認したいときは従来どおり `docker compose up dev`。

### 初期セットアップ（管理者が最初に一度だけ／ダッシュボード操作）

**A. Cloudflare Pages（プレビュー用）**
1. Cloudflare で「Pages → Create → Connect to Git」→ このリポジトリを選択。
2. ビルド設定：
   - Build command: `npm run build`
   - Build output directory: `dist`
   - Production branch: `master`
   - （必要なら）環境変数 `NODE_VERSION` = `20`（`.nvmrc` があるため通常は不要）
3. 以後、PRごとにプレビューURLが自動生成され PR にコメントされます。
   ※ 公開URL `orsj-cs.github.io` は GitHub Pages のまま。Cloudflareはプレビュー用。

**B. GitHub ブランチ保護（Settings → Branches → Add rule、対象 `master`）**
- ☑ Require a pull request before merging（`master` への直接pushを禁止）
- ☑ Require approvals（1名以上のレビュー承認）
- ☑ Require status checks to pass → `build`（CI）を必須に。任意で Cloudflare の
  チェックも必須化。
- ☑ Require branches to be up to date before merging（推奨）
- （任意）Require linear history

## 7. 注意点

- **`public/` の過去資料は編集・削除しない**でください（過去のリンクが切れます）。
  過去のPDFやHTMLはそのまま保存する方針です。
- `ssor2017/` は2017年当時の記録です。当時の見た目のまま保存しており、旧登録
  フォーム（PHP）は動作しません（静的サイトのため）。これは仕様です。
- お知らせや各ページで支部内リンクを書くときは `/sokai/` のように `/` から始めます。
- 大きな変更のあとは、ローカルで `npm run build`（または `docker compose up preview`）
  が成功することを確認してから push すると安全です。

---

## 技術メモ

- フレームワーク: Astro（静的出力）／ お知らせは Astro Content Collections（`src/content.config.ts`）
- ホスティング: GitHub Pages（組織ルートリポジトリ、`base` は `/`）
- 開発の詳細な設計方針は `CLAUDE.md` を参照。
