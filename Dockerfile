# Dockerfile
FROM php:8.2-fpm

RUN mkdir -p /var/www

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    sqlite3 \
    libsqlite3-dev

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-install pdo pdo_mysql pdo_sqlite

COPY . .

COPY docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


# Crear el archivo SQLite
RUN mkdir -p database && touch database/database.sqlite

RUN chmod -R 777 storage bootstrap/cache database

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

EXPOSE 8000
