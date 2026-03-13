FROM serversideup/php:8.2-cli

USER root

# Install PostgreSQL support
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

# RUN php -d memory_limit=-1 /usr/bin/composer install --no-dev --no-scripts --ignore-platform-reqs -v

RUN npm install && npm run build

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD php artisan migrate --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=10000