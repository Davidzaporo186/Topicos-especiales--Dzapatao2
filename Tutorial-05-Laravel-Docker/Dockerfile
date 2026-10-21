# Tutorial 05 - Laravel 12 en Docker (PHP 8.3)
# El proyecto Laravel se instala en la imagen durante la construccion.
FROM composer:2 AS builder
WORKDIR /build
RUN composer create-project laravel/laravel:^12.0 app --no-interaction --prefer-dist
COPY routes/web.php /build/app/routes/web.php
RUN cd /build/app && composer install --no-dev --optimize-autoloader --no-interaction \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs \
    && chmod -R 775 storage bootstrap/cache

FROM php:8.3-cli-bookworm
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev libicu-dev libonig-dev default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring intl zip \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /var/www/html
COPY --from=builder /build/app /var/www/html
COPY docker/entrypoint.sh /usr/local/bin/laravel-entrypoint
RUN chmod +x /usr/local/bin/laravel-entrypoint && chown -R www-data:www-data storage bootstrap/cache
ENV APP_ENV=production APP_DEBUG=false APP_URL=http://localhost
EXPOSE 80
ENTRYPOINT ["laravel-entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
