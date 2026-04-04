FROM serversideup/php:8.2-cli

USER root

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y \
    libpq-dev \
    curl \
    git \
    unzip \
    && install-php-extensions pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

COPY docker/prod/php-opcache.ini /usr/local/etc/php/conf.d/99-opcache.ini

RUN rm -f bootstrap/cache/*.php

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --ignore-platform-reqs \
    --no-interaction

RUN npm install && npm run build

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

USER www-data

EXPOSE 10000

CMD ["sh", "-c", "php artisan migrate --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=10000"]