#!/usr/bin/env bash
#
# ローカルでサイトを確認する。
#   ./scripts/serve-local.sh            # 開発サーバ (ホットリロード)
#   ./scripts/serve-local.sh preview    # 本番ビルドをプレビュー
#
# Docker があれば Docker を使い (Node インストール不要)、
# 無ければ手元の Node/npm にフォールバックする。
# どちらも http://localhost:4321/ で開ける。停止は Ctrl-C。

set -euo pipefail
cd "$(dirname "$0")/.."

MODE="${1:-dev}"
if [[ "$MODE" != "dev" && "$MODE" != "preview" ]]; then
  echo "usage: $0 [dev|preview]" >&2
  exit 1
fi

# Docker (compose v2 か 旧 docker-compose) を検出
if command -v docker >/dev/null 2>&1 && docker compose version >/dev/null 2>&1; then
  echo ">> Docker (compose) で起動します: $MODE"
  exec docker compose up --build "$MODE"
elif command -v docker-compose >/dev/null 2>&1; then
  echo ">> docker-compose で起動します: $MODE"
  exec docker-compose up --build "$MODE"
fi

# フォールバック: ローカル Node
echo ">> Docker が見つからないため Node で起動します: $MODE"
if [[ ! -d node_modules ]]; then
  echo ">> npm install を実行..."
  npm install
fi
if [[ "$MODE" == "preview" ]]; then
  npm run build
  exec npm run preview
else
  exec npm run dev
fi
