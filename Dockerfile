FROM php:7.4-cli

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y libmagickwand-dev --no-install-recommends \
    && rm -rf /var/lib/apt/lists/* \
    && pecl install imagick
RUN docker-php-ext-enable imagick
