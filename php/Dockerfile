FROM php:8.4-cli-alpine AS base

FROM base AS builder
COPY --from=composer:2.8 /usr/bin/composer /usr/local/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-plugins \
    --optimize-autoloader

FROM base
WORKDIR /app
COPY --from=builder /app/vendor ./vendor
COPY . .

RUN rm -rf /var/cache/apk/*

CMD ["php", "/app/bin/server.php"]