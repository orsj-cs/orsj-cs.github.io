# ローカル確認用イメージ (開発サーバ / プレビュー兼用)
# 本番デプロイは GitHub Actions が担うため、これは開発専用。
FROM node:20-alpine

WORKDIR /app

# 依存だけ先に入れてキャッシュを効かせる
COPY package.json package-lock.json ./
RUN npm ci

# ソースはコンテナ実行時に bind mount する想定 (compose 参照)。
# 単体 build 用に COPY も残す。
COPY . .

EXPOSE 4321

# デフォルトは開発サーバ (compose 側で上書き可)
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
