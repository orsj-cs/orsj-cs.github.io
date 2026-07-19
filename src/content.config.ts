import { defineCollection, z } from "astro:content";
import { glob } from "astro/loaders";

// お知らせ (ニュース) コレクション。
// src/content/news/*.md を追加するだけで一覧・個別ページ・トップ最新に反映される。
const news = defineCollection({
  loader: glob({ pattern: "**/*.md", base: "./src/content/news" }),
  schema: z.object({
    title: z.string(),
    date: z.coerce.date(),
    category: z.string().optional(),
  }),
});

// 研究会・講演会などの行事。トップの「次回の研究会」「今年度の予定」を自動生成する。
const events = defineCollection({
  loader: glob({ pattern: "**/*.md", base: "./src/content/events" }),
  schema: z.object({
    title: z.string(),
    date: z.coerce.date(), // 開催日
    venue: z.string().optional(), // 会場
    deadline: z.coerce.date().optional(), // 申込締切
    applyUrl: z.string().optional(), // 申込リンク
  }),
});

export const collections = { news, events };
