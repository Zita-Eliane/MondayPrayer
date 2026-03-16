FROM serversideup/php:8.2-cli

USER root

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

RUN chown -R www-data:www-data /var/www/html

COPY --chown=www-data:www-data . .

# RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

RUN install-php-extensions pdo_pgsql

RUN npm install
RUN npm run build

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD ["sh", "-c", "php artisan migrate --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=10000"]