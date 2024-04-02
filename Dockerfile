FROM php:8.2-fpm-alpine AS app_php

RUN apk add --no-cache acl git

RUN set -eux; \
	apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev postgresql-dev; \
	docker-php-ext-install -j$(nproc) intl pdo_mysql pdo_pgsql; \
  apk add --no-cache --virtual .phpexts-rundeps icu-libs postgresql-libs; \
	apk del .build-deps

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux; \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /srv/api

COPY composer.json composer.lock symfony.lock ./
COPY .env ./
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
RUN mkdir -p var/cache var/log

RUN composer install --no-progress --no-interaction --no-scripts

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]


FROM nginx:1.22-alpine AS app_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /srv/api

COPY --from=app_php /srv/api ./
