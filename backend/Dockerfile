FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    unzip \
    curl \
    && docker-php-ext-install pdo_sqlite bcmath \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev

RUN cp .env.example .env \
    && sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env \
    && sed -i '/^DB_HOST/d; /^DB_PORT/d; /^DB_DATABASE/d; /^DB_USERNAME/d; /^DB_PASSWORD/d' .env \
    && touch database/database.sqlite \
    && php artisan key:generate

RUN chmod -R 775 storage bootstrap/cache database

EXPOSE 8000

CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8000
