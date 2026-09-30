FROM dunglas/frankenphp:1-php8.4 AS base

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends acl file git unzip \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions apcu intl opcache zip pdo_pgsql

COPY --from=composer/composer:2-bin /composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV SERVER_NAME=:80

COPY docker/php/Caddyfile /etc/frankenphp/Caddyfile
COPY docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/app.ini

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN groupadd -g ${GROUP_ID} app 2>/dev/null || true \
    && useradd -u ${USER_ID} -g ${GROUP_ID} -m -s /bin/bash app 2>/dev/null || true \
    && setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
    && mkdir -p /data/caddy /config/caddy \
    && chown -R ${USER_ID}:${GROUP_ID} /data/caddy /config/caddy /app

FROM base AS dev

ENV APP_ENV=dev
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

USER app

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile", "--watch"]

FROM base AS prod

ENV APP_ENV=prod
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-progress --no-interaction

COPY . .
RUN composer dump-autoload --classmap-authoritative --no-dev \
    && composer run-script --no-dev post-install-cmd \
    && chmod +x bin/console \
    && chown -R app:app var

USER app

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
