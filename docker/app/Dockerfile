# Imagem de desenvolvimento do med-share: PHP 8.3 + Composer + Node 22.
# Uma imagem só serve o Laravel, o Vite e o worker da fila, porque o plugin
# do Wayfinder no Vite chama `php artisan` para gerar as rotas tipadas.
FROM php:8.3-cli-bookworm

ARG UID=1000
ARG GID=1000

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev libicu-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql zip intl bcmath pcntl \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node 22 (mesma versão da CI). Copia só o node e o npm para não sobrescrever
# o /usr/local do PHP.
COPY --from=node:22-bookworm-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-bookworm-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

COPY docker/app/php.ini /usr/local/etc/php/conf.d/zz-med-share.ini
COPY docker/app/entrypoint.sh /usr/local/bin/med-share-entrypoint
RUN chmod +x /usr/local/bin/med-share-entrypoint

# Usuário com o mesmo UID/GID do seu usuário no WSL: os arquivos criados pelo
# container (vendor, node_modules, logs) continuam seus.
RUN groupadd --gid "${GID}" app \
    && useradd --uid "${UID}" --gid "${GID}" --create-home --shell /bin/bash app

USER app
WORKDIR /var/www/html

ENTRYPOINT ["med-share-entrypoint"]
