FROM php:8.4-fpm

# Instalar dependencias del sistema y extensiones de MySQL para PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer dentro del contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
