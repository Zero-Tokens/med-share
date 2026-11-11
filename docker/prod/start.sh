#!/usr/bin/env bash
# Início do container de produção.
#   ROLE=web    (padrão) migrations + caches + servidor HTTP na porta $PORT
#   ROLE=worker worker da fila (um serviço separado no Railway, mesma imagem)
set -euo pipefail
cd /app

if [ -z "${APP_KEY:-}" ]; then
    echo "ERRO: defina a variável APP_KEY (gere com: php artisan key:generate --show)." >&2
    exit 1
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

case "${ROLE:-web}" in
    web)
        # Só o serviço web aplica migrations, para não haver dois processos
        # migrando ao mesmo tempo.
        php artisan migrate --force
        exec frankenphp php-server --listen ":${PORT:-8080}" --root public/
        ;;
    worker)
        exec php artisan queue:work --tries=3 --timeout=120 --sleep=3
        ;;
    *)
        echo "ERRO: ROLE desconhecido: ${ROLE}. Use web ou worker." >&2
        exit 1
        ;;
esac
