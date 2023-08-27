FROM php:8.2.8-cli-alpine3.18 as base

RUN addgroup -S appuser && adduser -S appuser -G appuser
RUN docker-php-ext-install pdo_mysql

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
ADD ./php.ini "$PHP_INI_DIR/conf.d/php.ini"

WORKDIR /usr/src
RUN chown -R appuser:appuser /usr/src

FROM base as builder
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
USER appuser
COPY --chown=appuser:appuser ./composer.* /usr/src
RUN set -xe \
    && composer install --no-dev --no-interaction --no-ansi --prefer-dist --no-autoloader --no-scripts
COPY --chown=appuser:appuser . /usr/src
RUN composer dump

FROM base as dev-container
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
USER appuser
COPY --chown=appuser:appuser ./composer.* /usr/src
RUN set -xe \
    && composer install --no-interaction --no-ansi --prefer-dist --no-autoloader --no-scripts
COPY --chown=appuser:appuser . /usr/src
RUN composer dump
ENTRYPOINT php -S 0.0.0.0:8000 -t public


# это подготовка под сборку production'контейнера
FROM base
WORKDIR /usr/src

COPY --from=builder /usr/src /usr/src

USER appuser
ENTRYPOINT php -S 0.0.0.0:8000 -t public
