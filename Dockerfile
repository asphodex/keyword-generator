FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache --virtual .build-deps \
        icu-dev \
        oniguruma-dev \
    && docker-php-ext-install intl mbstring opcache \
    && apk del .build-deps \
    && apk add --no-cache icu-libs

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

ARG APP_ENV=dev

COPY composer.json composer.lock ./
RUN if [ "$APP_ENV" = "prod" ]; then \
      composer install --no-dev --no-scripts --no-autoloader --prefer-dist; \
    else \
      composer install --no-scripts --no-autoloader --prefer-dist; \
    fi

COPY . .

RUN composer dump-autoload --optimize

RUN mkdir -p var/log var/cache \
    && chown -R www-data:www-data var

ENV APP_ENV=${APP_ENV}

FROM nginx:alpine AS nginx

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=base /app/public /app/public
