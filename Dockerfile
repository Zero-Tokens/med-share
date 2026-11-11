# Imagem de PRODUÇÃO do med-share (Railway e qualquer host de containers).
# O ambiente de desenvolvimento usa docker/app/Dockerfile via compose.yaml.
#
#   docker build -t med-share .
#   docker run -p 8080:8080 -e APP_KEY=... -e DB_URL=... med-share
#
# Servidor: FrankenPHP (Caddy + PHP), ouvindo na porta $PORT (Railway define).

ARG PHP_EXTENSIONS="pdo_pgsql intl zip bcmath pcntl opcache"

# ---------------------------------------------------------------------------
# Base: PHP 8.3 com as extensões da aplicação
# ---------------------------------------------------------------------------
FROM dunglas/frankenphp:1-php8.3-bookworm AS base
ARG PHP_EXTENSIONS
RUN install-php-extensions ${PHP_EXTENSIONS}
WORKDIR /app

# ---------------------------------------------------------------------------
# Build: dependências do PHP + assets do Vite.
# O build do Vite precisa de PHP porque o plugin do Wayfinder roda
# `php artisan wayfinder:generate` para gerar as rotas tipadas.
# ---------------------------------------------------------------------------
FROM base AS build

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=node:22-bookworm-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-bookworm-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && apt-get update && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

ENV COMPOSER_ALLOW_SUPERUSER=1

# Dependências primeiro, para aproveitar o cache de camadas.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY package.json package-lock.json .npmrc ./
RUN npm ci

COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi \
    && npm run build \
    && rm -rf node_modules

# ---------------------------------------------------------------------------
# Runtime: só o necessário para servir a aplicação
# ---------------------------------------------------------------------------
FROM base AS runtime

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    PORT=8080

COPY docker/prod/php.ini "$PHP_INI_DIR/conf.d/zz-med-share.ini"
COPY docker/prod/start.sh /usr/local/bin/med-share-start
COPY --from=build /app /app

# Roda sem root: a aplicação só escreve em storage/ e bootstrap/cache, e o
# Caddy do FrankenPHP em /data e /config.
RUN chmod +x /usr/local/bin/med-share-start \
    && mkdir -p storage/framework/cache/data storage/framework/sessions \
        storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache /data/caddy /config/caddy

USER www-data
EXPOSE 8080

# ROLE=web (padrão) serve HTTP; ROLE=worker roda a fila.
CMD ["med-share-start"]
