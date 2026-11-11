#!/usr/bin/env bash
# Prepara o projeto antes de iniciar o processo do serviço.
#
# Papéis (variável CONTAINER_ROLE):
#   app   → instala dependências do PHP, cria o .env/APP_KEY, roda migrations
#   vite  → instala dependências do Node e espera o vendor/ do serviço app
#   queue → espera o vendor/ e as migrations do serviço app
set -euo pipefail

cd /var/www/html
role="${CONTAINER_ROLE:-app}"

wait_for() {
    local description="$1" check="$2"
    echo "[$role] aguardando $description..."
    until eval "$check" >/dev/null 2>&1; do sleep 2; done
}

case "$role" in
    app)
        rm -f storage/framework/.container-ready
        [ -f .env ] || { cp .env.example .env; echo "[app] .env criado a partir do .env.example"; }

        composer install --no-interaction --prefer-dist

        if ! grep -qE '^APP_KEY=.+' .env; then
            php artisan key:generate --force
        fi

        php artisan migrate --force
        touch storage/framework/.container-ready
        ;;
    vite)
        if [ ! -d node_modules ] || [ package-lock.json -nt node_modules/.package-lock.json ]; then
            npm ci
        fi
        wait_for "o composer install do serviço app" "test -f vendor/autoload.php"
        ;;
    queue)
        wait_for "as migrations do serviço app" "test -f storage/framework/.container-ready"
        ;;
esac

exec "$@"
